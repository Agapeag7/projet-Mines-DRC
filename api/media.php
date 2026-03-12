<?php
// disable automatic router logic
if (!defined('KEL_NO_AUTO_ROUTER')) define('KEL_NO_AUTO_ROUTER', true);
require_once __DIR__ . '/../kel.class.php';
use KelFoncia\Database;
use KelFoncia\MediaModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$mm = new MediaModel($db);

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

if ($action === 'upload' || $action === 'media_upload') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    if (empty($_FILES['file'])) Utils::jsonResponse(['error' => 'missing_file'], 400);

    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) Utils::jsonResponse(['error' => 'upload_error', 'code' => $file['error']], 400);

    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $id = Utils::uuidv4();
    $filename = $id . ($ext ? "." . strtolower($ext) : '');
    $dest = $uploadsDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) Utils::jsonResponse(['error' => 'move_failed'], 500);

    $meta = [
        'owner_id' => $_SESSION['user_id'],
        'listing_id' => $_POST['listing_id'] ?? null,
        'type' => $_POST['type'] ?? 'image',
        'filename' => $file['name'],
        'path' => 'uploads/' . $filename,
        'mime_type' => $file['type'] ?? null,
        'size_bytes' => $file['size'] ?? null,
        'caption' => $_POST['caption'] ?? null,
        'is_public' => $_POST['is_public'] ?? 1
    ];
    $mid = $mm->create($meta);
    Utils::jsonResponse(['ok' => true, 'id' => $mid, 'path' => $meta['path']]);
}

if ($action === 'list_by_listing' || $action === 'media_list') {
    $listing_id = $_GET['listing_id'] ?? $_POST['listing_id'] ?? null;
    if (!$listing_id) Utils::jsonResponse(['error' => 'missing_listing_id'], 400);
    $rows = $mm->listByListing($listing_id);
    Utils::jsonResponse(['ok' => true, 'media' => $rows]);
}

if ($action === 'media_get' || $action === 'get') {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $m = $mm->getById($id);
    if (!$m) Utils::jsonResponse(['error' => 'not_found'], 404);
    Utils::jsonResponse(['ok' => true, 'media' => $m]);
}

if ($action === 'media_delete' || $action === 'delete') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $id = $_POST['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $m = $mm->getById($id);
    if (!$m) Utils::jsonResponse(['error' => 'not_found'], 404);
    if ($m['owner_id'] !== $_SESSION['user_id']) Utils::jsonResponse(['error' => 'forbidden'], 403);
    $ok = $mm->delete($id);
    Utils::jsonResponse(['ok' => (bool)$ok]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
