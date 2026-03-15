<?php
// disable auto-router from kel.class.php; this script handles its own actions
if (!defined('KEL_NO_AUTO_ROUTER')) define('KEL_NO_AUTO_ROUTER', true);
require_once __DIR__ . '/../kel.class.php';

// les classes sont définies dans l'espace global par kel.class.php

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$lm = new ListingModel($db);
$fm = new FavoriteModel($db);
$mm = new MediaModel($db);

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
    if (!empty($input['owner_id'])) $filters['owner_id'] = $input['owner_id'];
    if (!empty($input['province_id'])) $filters['province_id'] = (int)$input['province_id'];
    if (!empty($input['ville'])) $filters['ville'] = $input['ville'];
    $rows = $lm->list($filters, 100, 0);
    Utils::jsonResponse(['ok' => true, 'listings' => $rows]);
}

if ($action === 'listings_create' || $action === 'create') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);

    // Required fields
    $required = ['province', 'ville', 'commune', 'address_text', 'area_m2', 'price', 'description'];
    $missing = [];
    foreach ($required as $field) {
        if (empty(trim((string)($input[$field] ?? '')))) {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        Utils::jsonResponse(['error' => 'missing_fields', 'missing' => $missing, 'message' => 'Champs manquants: ' . implode(', ', $missing)], 400);
    }

    if (empty($input['certify']) || $input['certify'] != 'on') {
        Utils::jsonResponse(['error' => 'consent_required', 'message' => 'Vous devez certifier l’exactitude des informations.'], 400);
    }

    $allowedImageExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'tiff', 'raw', 'jfif'];
    $allowedDocumentExts = ['pdf', 'docx', 'doc', 'odt', 'rtf', 'legal'];

    $fileErrors = [];
    $validateFiles = function($fieldName, $allowedExts, &$fileErrors) {
        if (empty($_FILES[$fieldName]) || empty($_FILES[$fieldName]['name'])) return;
        $files = $_FILES[$fieldName];
        $count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $count; $i++) {
            $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext === '') {
                $fileErrors[] = "$fieldName : nom de fichier invalide ($name)";
                continue;
            }
            if (!in_array($ext, $allowedExts, true)) {
                $fileErrors[] = "$fieldName : extension non autorisée ($name)";
            }
        }
    };

    $validateFiles('photos', $allowedImageExts, $fileErrors);
    $validateFiles('documents', $allowedDocumentExts, $fileErrors);
    if (!empty($fileErrors)) {
        Utils::jsonResponse(['error' => 'invalid_file_type', 'message' => 'Extension de fichier invalide', 'details' => $fileErrors], 400);
    }

    // Build feature map
    $features = [];
    if (!empty($input['usage'])) $features['usage'] = $input['usage'];
    if (!empty($input['statut'])) $features['statut_juridique'] = $input['statut'];
    if (!empty($input['reference_titre'])) $features['reference_titre'] = $input['reference_titre'];
    if (!empty($input['annee_acquisition'])) $features['annee_acquisition'] = $input['annee_acquisition'];

    $title = trim($input['title'] ?? '');
    if (!$title) {
        $title = 'Terrain à vendre - ' . trim($input['address_text']);
    }

    $isPublished = isset($input['is_published']) ? (int)$input['is_published'] : 1;

    // Résoudre province_id à partir du nom de province (backend) si possible
    $provinceId = null;
    if (!empty($input['province'])) {
        $stmt = $db->prepare('SELECT id FROM provinces WHERE nom = ? LIMIT 1');
        $stmt->execute([trim($input['province'])]);
        $row = $stmt->fetch();
        if ($row) {
            $provinceId = $row['id'];
        }
    }

    $data = [
        'owner_id' => $_SESSION['user_id'],
        'title' => $title,
        'description' => $input['description'],
        'area_m2' => $input['area_m2'],
        'price' => $input['price'],
        'currency' => $input['currency'] ?? 'CDF',
        'statut' => 'available',
        'address_text' => $input['address_text'],
        'latitude' => $input['latitude'] ?? null,
        'longitude' => $input['longitude'] ?? null,
        'province_id' => $provinceId,
        'province' => $input['province'],
        'ville' => $input['ville'],
        'commune' => $input['commune'],
        'territoire' => $input['territoire'] ?? null,
        'features' => !empty($features) ? $features : null,
        'is_published' => $isPublished,
        'visible' => 1,
    ];

    $id = $lm->create($data);

    // Handle file uploads (photos + documents)
    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

    $uploadedMedia = [];
    $firstImageId = null;

    $processFiles = function($fieldName, $type) use (&$firstImageId, &$uploadedMedia, $uploadsDir, $mm, $id) {
        if (empty($_FILES[$fieldName]) || empty($_FILES[$fieldName]['name'])) return;
        $files = &$_FILES[$fieldName];
        $count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $count; $i++) {
            $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            if ($error !== UPLOAD_ERR_OK) continue;
            $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $tmp = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];

            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $mediaId = Utils::uuidv4();
            $filename = $mediaId . ($ext ? '.' . strtolower($ext) : '');
            $dest = $uploadsDir . '/' . $filename;
            if (!move_uploaded_file($tmp, $dest)) continue;

            $mimeType = is_array($files['type']) ? ($files['type'][$i] ?? null) : $files['type'];
            $sizeBytes = is_array($files['size']) ? ($files['size'][$i] ?? null) : $files['size'];

            $meta = [
                'owner_id' => $_SESSION['user_id'],
                'listing_id' => $id,
                'type' => $type,
                'filename' => $name,
                'path' => 'uploads/' . $filename,
                'mime_type' => $mimeType,
                'size_bytes' => $sizeBytes,
                'is_public' => 1,
            ];

            $mid = $mm->create($meta);
            $uploadedMedia[] = $mid;

            if ($type === 'image' && !$firstImageId) {
                $firstImageId = $mid;
            }
        }
    };

    $processFiles('photos', 'image');
    $processFiles('documents', 'document');

    if ($firstImageId) {
        $lm->update($id, ['thumbnail_id' => $firstImageId]);
    }

    Utils::jsonResponse(['ok' => true, 'id' => $id, 'media_ids' => $uploadedMedia]);
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

if ($action === 'favorite_list' || $action === 'list_favorites') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $ids = $fm->listForUser($_SESSION['user_id']);
    Utils::jsonResponse(['ok' => true, 'listing_ids' => $ids]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
