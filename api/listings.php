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
$cm = new ConversationModel($db);
$msgm = new MessageModel($db);
function getBaseUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    // strip /api from script dir to get project root
    $root = rtrim(str_replace('/api', '', $scriptDir), '/');
    return $scheme . '://' . $host . $root;
}
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
    $baseUrl = getBaseUrl();
    foreach ($rows as &$row) {
        if (!empty($row['thumbnail_id'])) {
            $media = $mm->getById($row['thumbnail_id']);
            if ($media && !empty($media['path'])) {
                $row['thumbnail_path'] = $media['path'];
                $row['thumbnail_full_url'] = rtrim($baseUrl, '/') . '/' . ltrim($media['path'], '/');
            }
        }
    }
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

    $isPublished = isset($input['is_published']) ? (int)$input['is_published'] : 1;
    if ($isPublished === 1 && (empty($input['certify']) || $input['certify'] != 'on')) {
        Utils::jsonResponse(['error' => 'consent_required', 'message' => 'Vous devez certifier l’exactitude des informations avant publication.'], 400);
    }

    $allowedImageExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'tiff', 'raw', 'jfif'];
    $allowedDocumentExts = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'ods', 'odp'];

    $fileErrors = [];
    $validateFiles = function($fieldName, $allowedExts, &$fileErrors) {
        if (empty($_FILES[$fieldName]) || empty($_FILES[$fieldName]['name'])) return;
        $files = $_FILES[$fieldName];
        $count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $count; $i++) {
            $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            if ($size > 10 * 1024 * 1024) {
                $fileErrors[] = "$fieldName : fichier trop volumineux ($name)";
                continue;
            }
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
        'currency' => $input['currency'] ?? 'USD',
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
    $uploadsRoot = __DIR__ . '/../doc';
    $photosDir = $uploadsRoot . '/photos';
    $jurDir = $uploadsRoot . '/jur';
    foreach ([$uploadsRoot, $photosDir, $jurDir] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    $uploadedMedia = [];
    $firstImageId = null;

    $processFiles = function($fieldName, $type) use (&$firstImageId, &$uploadedMedia, $photosDir, $jurDir, $mm, $id) {
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

            $destDir = ($type === 'image') ? $photosDir : $jurDir;
            $dest = $destDir . '/' . $filename;
            if (!move_uploaded_file($tmp, $dest)) continue;

            $mimeType = is_array($files['type']) ? ($files['type'][$i] ?? null) : $files['type'];
            $sizeBytes = is_array($files['size']) ? ($files['size'][$i] ?? null) : $files['size'];

            $meta = [
                'owner_id' => $_SESSION['user_id'],
                'listing_id' => $id,
                'type' => $type,
                'filename' => $name,
                'path' => ($type === 'image' ? 'doc/photos/' : 'doc/jur/') . $filename,
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

    $response = ['ok' => true, 'id' => $id, 'media_ids' => $uploadedMedia];

    if ($firstImageId) {
        $lm->update($id, ['thumbnail_id' => $firstImageId]);
        $media = $mm->getById($firstImageId);
        if ($media && !empty($media['path'])) {
            $baseUrl = getBaseUrl();
            $response['thumbnail_id'] = $firstImageId;
            $response['thumbnail_path'] = $media['path'];
            $response['thumbnail_full_url'] = rtrim($baseUrl, '/') . '/' . ltrim($media['path'], '/');
        }
    }

    Utils::jsonResponse($response);
}

if ($action === 'listings_get' || $action === 'get') {
    $id = $input['id'] ?? $_GET['id'] ?? null;
    if (!$id) Utils::jsonResponse(['error' => 'missing_id'], 400);
    $listing = $lm->getById($id);
    if (!$listing) Utils::jsonResponse(['error' => 'not_found'], 404);

    if (!empty($listing['thumbnail_id'])) {
        $media = $mm->getById($listing['thumbnail_id']);
        if ($media && !empty($media['path'])) {
            $baseUrl = getBaseUrl();
            $listing['thumbnail_path'] = $media['path'];
            $listing['thumbnail_full_url'] = rtrim($baseUrl, '/') . '/' . ltrim($media['path'], '/');
        }
    }

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

if ($action === 'dashboard_overview') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $user_id = $_SESSION['user_id'];
    $listings = $lm->list(['owner_id' => $user_id], 1000, 0);
    $listings_count = count($listings);
    $favorites = $fm->listForUser($user_id);
    $favorites_count = count($favorites);
    $conversations = $cm->listForUser($user_id, 1000, 0);
    $messages_count = 0;
    foreach ($conversations as $conv) {
        $messages = $msgm->listByConversation($conv['id'], 1000, 0);
        $messages_count += count($messages);
    }
    $unread_count = $msgm->getUnreadCount($user_id);
    Utils::jsonResponse(['ok' => true, 'stats' => [
        'listings_count' => $listings_count,
        'favorites_count' => $favorites_count,
        'messages_count' => $messages_count,
        'unread_messages' => $unread_count
    ]]);
}

if ($action === 'dashboard_favorites') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $user_id = $_SESSION['user_id'];
    $favorite_ids = $fm->listForUser($user_id);
    $favorites = [];
    foreach ($favorite_ids as $id) {
        $listing = $lm->getById($id);
        if ($listing) $favorites[] = $listing;
    }
    Utils::jsonResponse(['ok' => true, 'favorites' => $favorites]);
}

if ($action === 'dashboard_listings') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $user_id = $_SESSION['user_id'];
    $listings = $lm->list(['owner_id' => $user_id], 50, 0);
    Utils::jsonResponse(['ok' => true, 'listings' => $listings]);
}

if ($action === 'dashboard_messages') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $user_id = $_SESSION['user_id'];
    $conversations = $cm->listForUser($user_id, 50, 0);
    $result = [];
    foreach ($conversations as $conv) {
        $messages = $msgm->listByConversation($conv['id'], 10, 0);
        $last_message = end($messages);
        $result[] = [
            'conversation' => $conv,
            'last_message' => $last_message,
            'messages_count' => count($messages)
        ];
    }
    Utils::jsonResponse(['ok' => true, 'conversations' => $result]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
