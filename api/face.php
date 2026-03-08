<?php
require_once __DIR__ . '../kel.class.php';
use KelFoncia\Database;
use KelFoncia\UserModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$um = new UserModel($db);

$raw = file_get_contents('php://input');
$input = json_decode($raw, true) ?: $_POST;

action:
$action = $_GET['action'] ?? $input['action'] ?? null;
if (!$action) {
    Utils::jsonResponse(['error' => 'missing_action'], 400);
}

if ($action === 'recognize') {
    $descriptor = $input['descriptor'] ?? null;
    if (!$descriptor || !is_array($descriptor)) {
        Utils::jsonResponse(['error' => 'missing_descriptor'], 400);
    }
    $threshold = 0.6;
    $best = null;
    $bestdist = PHP_FLOAT_MAX;
    foreach ($um->allFaceDescriptors() as $row) {
        $existing = json_decode($row['face_descriptor'], true);
        if (!is_array($existing)) continue;
        $sum = 0;
        for ($i = 0; $i < count($existing); $i++) {
            $diff = ($existing[$i] ?? 0) - ($descriptor[$i] ?? 0);
            $sum += $diff * $diff;
        }
        $dist = sqrt($sum);
        if ($dist < $bestdist) {
            $bestdist = $dist;
            $best = $row['id'];
        }
    }
    if ($best !== null && $bestdist < $threshold) {
        Utils::jsonResponse(['ok' => true, 'match' => true, 'user_id' => $best, 'distance' => $bestdist]);
    } else {
        Utils::jsonResponse(['ok' => true, 'match' => false, 'distance' => $bestdist]);
    }
}

if ($action === 'list') {
    $all = [];
    foreach ($um->allFaceDescriptors() as $row) {
        $all[] = ['user_id' => $row['id'], 'descriptor' => json_decode($row['face_descriptor'], true)];
    }
    Utils::jsonResponse(['ok' => true, 'descriptors' => $all]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
