<?php
require_once __DIR__ . '../kel.class.php';
use KelFoncia\Database;
use KelFoncia\NotificationModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$nm = new NotificationModel($db);

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

if ($action === 'notifications_list' || $action === 'list') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $limit = $_GET['limit'] ?? $_POST['limit'] ?? 50;
    $offset = $_GET['offset'] ?? $_POST['offset'] ?? 0;
    $rows = $nm->listForUser($_SESSION['user_id'], $limit, $offset);
    Utils::jsonResponse(['ok' => true, 'notifications' => $rows]);
}

if ($action === 'notifications_mark_read' || $action === 'mark_read') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $id = $_POST['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $ok = $nm->markRead($id);
    Utils::jsonResponse(['ok' => (bool)$ok]);
}

if ($action === 'notifications_create' || $action === 'create') {
    // simple create (could be restricted)
    $data = [
        'user_id' => $_POST['user_id'] ?? null,
        'type' => $_POST['type'] ?? 'system',
        'payload' => isset($_POST['payload']) ? json_decode($_POST['payload'], true) : null,
        'titre' => $_POST['titre'] ?? null,
        'message' => $_POST['message'] ?? null,
        'is_read' => $_POST['is_read'] ?? 0
    ];
    $id = $nm->create($data);
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
