<?php
require_once __DIR__ . '../kel.class.php';
use KelFoncia\Database;
use KelFoncia\ConversationModel;
use KelFoncia\MessageModel;
use KelFoncia\Utils;

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$cm = new ConversationModel($db);
$mm = new MessageModel($db);

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

if ($action === 'conversation_create' || $action === 'create') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $sujet = $_POST['sujet'] ?? null;
    $listing_id = $_POST['listing_id'] ?? null;
    $id = $cm->create($sujet, $listing_id);
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

if ($action === 'message_send' || $action === 'send') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $conversation_id = $_POST['conversation_id'] ?? null;
    $content = $_POST['content'] ?? null;
    if (!$conversation_id || !$content) Utils::jsonResponse(['error' => 'missing_fields'], 400);
    $attachments = isset($_POST['attachments']) ? json_decode($_POST['attachments'], true) : null;
    $mid = $mm->send($conversation_id, $_SESSION['user_id'], $content, $attachments);
    Utils::jsonResponse(['ok' => true, 'id' => $mid]);
}

// basic listing of conversations/messages could be added later

Utils::jsonResponse(['error' => 'unknown_action'], 400);
