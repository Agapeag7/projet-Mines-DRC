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
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    if (!$email || !$password) Utils::jsonResponse(['error' => 'missing_credentials'], 400);
    $user = $um->verifyCredentials($email, $password);
    if (!$user) Utils::jsonResponse(['error' => 'invalid_credentials'], 401);
    // set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    Utils::jsonResponse(['ok' => true, 'user' => ['id' => $user['id'], 'email' => $user['email'], 'display_name' => $user['display_name']]]);
}

if ($action === 'register') {
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    if (!$email || !$password) Utils::jsonResponse(['error' => 'missing_fields'], 400);
    if ($um->findByEmail($email)) Utils::jsonResponse(['error' => 'email_exists'], 409);
    $id = $um->create(['email' => $email, 'password' => $password, 'role' => 'proprietaire']);
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
