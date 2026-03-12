<?php
// when we include the core library we usually want to run its Router class
// automatically, but auth.php implements its own handlers and must not be
// intercepted. define a flag before including so kel.class.php skips the
// auto-router logic.
if (!defined('KEL_NO_AUTO_ROUTER')) {
    define('KEL_NO_AUTO_ROUTER', true);
}

// (debug marker removed) — normal execution continues
require_once __DIR__ . '/../kel.class.php';
// core classes are defined in the global namespace; no `use` needed

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$um = new UserModel($db);

// ensure log folder exist for debug output
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// accept form POST or JSON body
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    // write debugging info to local file (apache may not show error_log output)
    @file_put_contents(__DIR__ . '/../logs/auth_debug.log', "raw body: $raw\n", FILE_APPEND);
    $json = json_decode($raw, true);
    if (is_array($json)) $input = $json;
}
// log parsed input as well
@file_put_contents(__DIR__ . '/../logs/auth_debug.log', "parsed input: " . json_encode($input) . "\n", FILE_APPEND);

$action = $_GET['action'] ?? $input['action'] ?? null;
if (!$action) {
    Utils::jsonResponse(['error' => 'missing_action'], 400);
}

if ($action === 'login') {
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if (!$email || !$password) {
        Utils::jsonResponse(['error' => 'missing_credentials', 'message' => 'Email et mot de passe requis'], 400);
    }

    $user = $um->verifyCredentials($email, $password);
    if (!$user) {
        Utils::jsonResponse(['error' => 'invalid_credentials', 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    // generate 2FA code and store temporarily in session
    $code = rand(100000, 999999);
    $_SESSION['pending_2fa_user'] = $user['id'];
    $_SESSION['pending_2fa_code'] = $code;
    $_SESSION['pending_2fa_expires'] = time() + 300; // valid 5 minutes

    // prepare message; user may configure php.ini SMTP settings or
    // use an external library (PHPMailer) to send via Gmail/another provider.
    $to = $user['email'];
    $subject = 'Votre code de vérification KelFoncia';
    $body = "Bonjour,\n\nVotre code de vérification est : $code\nIl expire dans 5 minutes.\n\nCordialement,\nKelFoncia";

    // attempt to send via built-in mail().  On XAMPP this requires proper SMTP
    // settings in php.ini (SMTP, smtp_port, sendmail_from).
    @mail($to, $subject, $body);

    // if PHPMailer is installed (via composer require phpmailer/phpmailer),
    // you can send using Gmail or another SMTP provider.  Define constants in
    // a config file or at the top of this script:
    //   define('SMTP_HOST', 'smtp.gmail.com');
    //   define('SMTP_PORT', 587);
    //   define('SMTP_USER', 'your@gmail.com');
    //   define('SMTP_PASS', 'app-specific-password');
    //   define('MAIL_FROM', 'no-reply@yourdomain.com');
    //   define('MAIL_FROM_NAME', 'KelFoncia');
    if (class_exists('PHPMailer\PHPMailer\PHPMailer') && defined('SMTP_HOST') && defined('SMTP_USER') && defined('SMTP_PASS')) {
        try {
            $pm = new PHPMailer\PHPMailer\PHPMailer(true);
            $pm->isSMTP();
            $pm->Host = SMTP_HOST;
            $pm->SMTPAuth = true;
            $pm->Username = SMTP_USER;
            $pm->Password = SMTP_PASS;
            $pm->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
            $pm->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $pm->setFrom(defined('MAIL_FROM') ? MAIL_FROM : SMTP_USER, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'KelFoncia');
            $pm->addAddress($to);
            $pm->Subject = $subject;
            $pm->Body = $body;
            $pm->send();
        } catch (Exception $e) {
            @file_put_contents(__DIR__ . '/../logs/auth_debug.log', "PHPMailer error: {$e->getMessage()}\n", FILE_APPEND);
        }
    }

    // always log code to project log for testing
    @file_put_contents(__DIR__ . '/../logs/auth_debug.log', "sent 2fa code to $to: $code\n", FILE_APPEND);

    // reply to client.  during development you can add &debug to the URL or
    // check the debug field below to obtain the code directly.
    $response = [
        'ok' => true,
        'need_2fa' => true,
        'message' => 'Code de vérification envoyé par email.'
    ];
    if (!empty($_GET['debug']) || !empty($input['debug'])) {
        // convenient when testing on devices without email setup
        $response['debug_code'] = $code;
    }
    Utils::jsonResponse($response);
    return;
}

if ($action === 'register') {
    @file_put_contents(__DIR__ . '/../logs/auth_debug.log', "handling register action\n", FILE_APPEND);
    // disallow registration while already logged in; client should redirect
    if (!empty($_SESSION['user_id'])) {
        Utils::jsonResponse(['error' => 'already_authenticated', 'message' => 'Vous êtes déjà connecté'], 403);
    }

    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $password_confirm = $input['password_confirm'] ?? '';
    $display_name = trim($input['display_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $role = trim($input['role'] ?? 'proprietaire');

    // Validation - phone optional
    $missing = [];
    if (!$email) $missing[] = 'email';
    if (!$password) $missing[] = 'password';
    if (!$password_confirm) $missing[] = 'password_confirm';
    if (!$display_name) $missing[] = 'display_name';
    if (!empty($missing)) {
        // helpful server-side debug when the client thinks it sent every field
        error_log("[auth] register missing fields: " . implode(', ', $missing));
        Utils::jsonResponse(['error' => 'missing_fields', 'message' => 'Champs manquants: '.implode(', ', $missing), 'missing' => $missing], 400);
    }

    // email regex stricter (allow any TLD length >=2)
    if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {
        Utils::jsonResponse(['error' => 'invalid_email', 'message' => 'Email invalide'], 400);
    }

    if ($password !== $password_confirm) {
        Utils::jsonResponse(['error' => 'password_mismatch', 'message' => 'Les mots de passe ne correspondent pas'], 400);
    }

    if (strlen($password) < 8) {
        Utils::jsonResponse(['error' => 'password_weak', 'message' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
    }

    $valid_roles = ['proprietaire', 'promoteur', 'investisseur'];
    if (!in_array($role, $valid_roles)) {
        Utils::jsonResponse(['error' => 'invalid_role', 'message' => 'Rôle invalide'], 400);
    }

    if ($um->findByEmail($email)) {
        Utils::jsonResponse(['error' => 'email_exists', 'message' => 'Cet email est déjà utilisé'], 409);
    }

    // face descriptor duplicate check
    $descriptor = $input['face_descriptor'] ?? null;
    if ($descriptor && is_array($descriptor)) {
        // compare with existing descriptors (only photos already saved in DB)
        $threshold = 0.6;
        foreach ($um->allFaceDescriptors() as $row) {
            $existing = json_decode($row['face_descriptor'], true);
            if (!is_array($existing)) continue;
            $sum = 0;
            for ($i = 0; $i < count($existing); $i++) {
                $diff = ($existing[$i] ?? 0) - ($descriptor[$i] ?? 0);
                $sum += $diff * $diff;
            }
            $dist = sqrt($sum);
            if ($dist < $threshold) {
                Utils::jsonResponse(['error' => 'face_exists', 'message' => 'Un compte avec ce visage existe déjà', 'match_id' => $row['id'], 'distance' => $dist], 409);
            }
        }
    }

    // if a photo was sent alongside the descriptor, keep it for storage
    $photoData = $input['face_photo'] ?? null;
    $savedPhotoPath = null;
    if ($photoData && is_string($photoData)) {
        // if the string looks like a data URI, decode and write to /img
        if (preg_match('#^data:image\/([a-zA-Z]+);base64,#', $photoData, $m)) {
            $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
            $base64 = substr($photoData, strpos($photoData, ',') + 1);
            $bin = base64_decode($base64);
            if ($bin !== false) {
                // generate temporary filename; we will rename to use user id when known
                $tempName = uniqid('face_', true) . ".{$ext}";
                $filePath = __DIR__ . '/../img/' . $tempName;
                if (@file_put_contents($filePath, $bin) !== false) {
                    $savedPhotoPath = 'img/' . $tempName;
                }
            }
        } else {
            // treat as plain text (maybe user wants to store base64 directly)
            $savedPhotoPath = $photoData;
        }
    }

    // phone validation / uniqueness
    if ($phone) {
        // DR Congo numbers: start with 0 or +243/243 then 9 digits beginning with 8 or 9
        $phone_regex = '/^(?:\+243|243|0)(?:[89]\d{8})$/';
        if (!preg_match($phone_regex, $phone)) {
            Utils::jsonResponse(['error' => 'invalid_phone', 'message' => 'Numéro de téléphone invalide'], 400);
        }
        if ($um->findByPhone($phone)) {
            Utils::jsonResponse(['error' => 'phone_exists', 'message' => 'Ce numéro est déjà utilisé'], 409);
        }
    }

    try {
        $id = $um->create([
            'email' => $email,
            'phone' => $phone ?: null,
            'password' => $password,
            'display_name' => $display_name,
            'role' => $role,
            'face_descriptor' => $descriptor ?? null,
            // if we decoded an image earlier, temporarily stored in $savedPhotoPath
            // it will be updated/renamed after creation below
            'face_photo' => $savedPhotoPath
        ]);
        // if we wrote a temporary file and the id is known we can rename it to
        // include the user id for clarity and update the record again.
        if ($savedPhotoPath && strpos($savedPhotoPath, 'img/face_') === 0) {
            $ext = pathinfo($savedPhotoPath, PATHINFO_EXTENSION);
            $newName = "img/{$id}.{$ext}";
            $oldFs = __DIR__ . '/../' . $savedPhotoPath;
            $newFs = __DIR__ . '/../' . $newName;
            if (@rename($oldFs, $newFs)) {
                $savedPhotoPath = $newName;
                $um->updateFacePhoto($id, $newName);
            }
        }
        Utils::jsonResponse([
            'ok' => true,
            'id' => $id,
            'message' => 'Inscription réussie',
            'user' => ['id' => $id, 'email' => $email, 'display_name' => $display_name, 'role' => $role]
        ], 201);
    } catch (Exception $e) {
        Utils::jsonResponse(['error' => 'register_failed', 'message' => $e->getMessage()], 500);
    }
}


if ($action === 'verify_2fa') {
    $code = trim($input['code'] ?? '');
    if (!$code || !isset($_SESSION['pending_2fa_user'])) {
        Utils::jsonResponse(['error' => 'missing_2fa', 'message' => 'Code requis'], 400);
    }
    if (time() > ($_SESSION['pending_2fa_expires'] ?? 0)) {
        Utils::jsonResponse(['error' => 'code_expired', 'message' => 'Le code a expiré'], 400);
    }
    if ($code != $_SESSION['pending_2fa_code']) {
        Utils::jsonResponse(['error' => 'invalid_code', 'message' => 'Code incorrect'], 401);
    }
    // complete login
    $user = $um->findById($_SESSION['pending_2fa_user']);
    if (!$user) {
        Utils::jsonResponse(['error' => 'user_not_found'], 404);
    }
    // set session permanently
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    // cleanup
    unset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_code'], $_SESSION['pending_2fa_expires']);
    Utils::jsonResponse(['ok' => true, 'message' => 'Connexion 2FA réussie']);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
