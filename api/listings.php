<?php
require_once __DIR__ . '/../kel.class.php';
use KelFoncia\Database;
use KelFoncia\ListingModel;
use KelFoncia\FavoriteModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$lm = new ListingModel($db);
$fm = new FavoriteModel($db);

// accept GET/POST or JSON
$input = $_REQUEST;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) $input = array_merge($input, $json);
}
$action = $_GET['action'] ?? $input['action'] ?? null;
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

if ($action === 'listings_list' || $action === 'list') {
    $filters = [];
    if (!empty($input['province_id'])) $filters['province_id'] = (int)$input['province_id'];
    if (!empty($input['ville'])) $filters['ville'] = $input['ville'];
    $rows = $lm->list($filters, 100, 0);
    Utils::jsonResponse(['ok' => true, 'listings' => $rows]);
}

if ($action === 'listings_create' || $action === 'create') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $data = [
        'owner_id' => $_SESSION['user_id'],
        'title' => $input['title'] ?? 'Sans titre',
        'description' => $input['description'] ?? null,
        'area_m2' => $input['area_m2'] ?? null,
        'price' => $input['price'] ?? null,
        'currency' => $input['currency'] ?? 'CDF',
        'province_id' => $input['province_id'] ?? null,
        'province' => $input['province'] ?? null,
        'ville' => $input['ville'] ?? null,
        'commune' => $input['commune'] ?? null,
        'features' => isset($input['features']) ? (is_array($input['features']) ? $input['features'] : json_decode($input['features'], true)) : null,
    ];
    $id = $lm->create($data);
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

if ($action === 'listings_get' || $action === 'get') {
    $id = $input['id'] ?? $_GET['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $listing = $lm->getById($id);
    if (!$listing) Utils::jsonResponse(['error' => 'not_found'], 404);
    Utils::jsonResponse(['ok' => true, 'listing' => $listing]);
}

if ($action === 'listings_update' || $action === 'update') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $id = $input['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $existing = $lm->getById($id);
    if (!$existing) Utils::jsonResponse(['error' => 'not_found'], 404);
    if ($existing['owner_id'] !== $_SESSION['user_id']) Utils::jsonResponse(['error' => 'forbidden'], 403);
    $data = $input;
    unset($data['action']);
    $ok = $lm->update($id, $data);
    Utils::jsonResponse(['ok' => (bool)$ok]);
}

if ($action === 'listings_delete' || $action === 'delete') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $id = $input['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $existing = $lm->getById($id);
    if (!$existing) Utils::jsonResponse(['error' => 'not_found'], 404);
    if ($existing['owner_id'] !== $_SESSION['user_id']) Utils::jsonResponse(['error' => 'forbidden'], 403);
    $ok = $lm->delete($id);
    Utils::jsonResponse(['ok' => (bool)$ok]);
}

if ($action === 'toggle_favorite' || $action === 'favorite_toggle') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $listing_id = $input['listing_id'] ?? null;
    if (!$listing_id) Utils::jsonResponse(['error' => 'missing_listing_id'], 400);
    $res = $fm->toggle($_SESSION['user_id'], $listing_id);
    Utils::jsonResponse(['ok' => true, 'result' => $res]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
