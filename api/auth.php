<?php
require_once __DIR__ . '/../kel.class.php';
use KelFoncia\Database;
use KelFoncia\UserModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$um = new UserModel($db);

// accept form POST or JSON body
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

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

    // dispatch by email or SMS - simple mail stub for now
    $to = $user['email'];
    $subject = 'Votre code de vérification KelFoncia';
    $body = "Bonjour,\n\nVotre code de vérification est : $code\nIl expire dans 5 minutes.\n\nCordialement,\nKelFoncia";
    // @phpstan-ignore-next-line
    @mail($to, $subject, $body);
    // if you have a gateway, send SMS to $user['phone'] instead/also

    Utils::jsonResponse([
        'ok' => true,
        'need_2fa' => true,
        'message' => 'Code de vérification envoyé par email.'
    ]);
    return;
}

if ($action === 'register') {
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
        Utils::jsonResponse(['error' => 'missing_fields', 'message' => 'Champs manquants: '.implode(', ', $missing), 'missing' => $missing], 400);
    }

    // email regex stricter
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2}$/', $email)) {
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
        // compare with existing descriptors
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

    // phone validation / uniqueness
    if ($phone) {
        $phone_regex = '/^(?:(?:099|097|081|082|086)\d{7}|(?:\+243|243|0)(?:99|97|81|82|86)\d{7})$/';
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
        ]);
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
