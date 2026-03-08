<?php
require_once __DIR__ . '../kel.class.php';
use KelFoncia\Database;
use KelFoncia\KycModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$km = new KycModel($db);

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

if ($action === 'kyc_request' || $action === 'request') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $type = $_POST['type'] ?? 'id_scan';
    $evidence = isset($_POST['evidence']) ? json_decode($_POST['evidence'], true) : [];
    $id = $km->request($_SESSION['user_id'], $type, $evidence);
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
