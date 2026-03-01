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
    
    // set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    Utils::jsonResponse([
        'ok' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
            'role' => $user['role']
        ]
    ], 200);
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
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

    try {
        $id = $um->create([
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'display_name' => $display_name,
            'phone' => $phone
        ]);
        
        // Auto-login after registration
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        
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

Utils::jsonResponse(['error' => 'unknown_action'], 400);
