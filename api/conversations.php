<?php
if (!defined('KEL_NO_AUTO_ROUTER')) define('KEL_NO_AUTO_ROUTER', true);
require_once __DIR__ . '/../kel.class.php';

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

$db = (new Database())->pdo();
$cm = new ConversationModel($db);
$mm = new MessageModel($db);

// Helper: Get requestdata from POST, JSON body, or GET
function getRequestData($key, $default = null) {
    // Try POST first
    if (isset($_POST[$key])) return $_POST[$key];
    // Try GET
    if (isset($_GET[$key])) return $_GET[$key];
    // Try JSON body
    static $jsonData = null;
    if ($jsonData === null) {
        $input = file_get_contents('php://input');
        $jsonData = $input ? json_decode($input, true) : [];
    }
    return $jsonData[$key] ?? $default;
}

$action = getRequestData('action');
if (!$action) Utils::jsonResponse(['error' => 'missing_action'], 400);

/**
 * Dashboard Messages - List all conversations for current user
 * Used by tableau-de-bord.php to populate the messages section
 */
if ($action === 'dashboard_messages') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    $user_id = $_SESSION['user_id'];
    $conversations = $cm->listForUser($user_id, 50, 0);
    $result = [];
    foreach ($conversations as $conv) {
        $messages = $mm->listByConversation($conv['id'], 10, 0);
        $last_message = end($messages);
        $result[] = [
            'conversation' => $conv,
            'last_message' => $last_message,
            'messages_count' => count($messages)
        ];
    }
    Utils::jsonResponse(['ok' => true, 'conversations' => $result]);
}

/**
 * Create Conversation
 * POST/JSON: sujet, listing_id (optional), participants (optional - array of user IDs)
 */
if ($action === 'conversation_create' || $action === 'create') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $sujet = getRequestData('sujet');
    $listing_id = getRequestData('listing_id');
    $participants = getRequestData('participants', []);
    
    if (!is_array($participants) && is_string($participants)) {
        $participants = json_decode($participants, true) ?? [];
    }
    
    $id = $cm->create($sujet, $listing_id);
    
    // Add current user as a member
    $stmt = $db->prepare('INSERT IGNORE INTO conversation_members (conversation_id, user_id) VALUES (?, ?)');
    $stmt->execute([$id, $_SESSION['user_id']]);
    
    // Add other participants
    if (!empty($participants)) {
        foreach ($participants as $participant_id) {
            $stmt = $db->prepare('INSERT IGNORE INTO conversation_members (conversation_id, user_id) VALUES (?, ?)');
            $stmt->execute([$id, $participant_id]);
        }
    }
    
    Utils::jsonResponse(['ok' => true, 'id' => $id]);
}

/**
 * Send Message
 * POST/JSON: conversation_id, content, attachments (optional)
 */
if ($action === 'message_send' || $action === 'send') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    $content = getRequestData('content');
    $attachments = getRequestData('attachments');
    
    if (!$conversation_id || !$content) Utils::jsonResponse(['error' => 'missing_fields'], 400);
    
    // Verify user is member of conversation
    $stmt = $db->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        Utils::jsonResponse(['error' => 'not_member_of_conversation'], 403);
    }
    
    if ($attachments && is_string($attachments)) {
        $attachments = json_decode($attachments, true);
    }
    
    $mid = $mm->send($conversation_id, $_SESSION['user_id'], $content, $attachments);
    
    // Get the full message
    $stmt = $db->prepare('SELECT * FROM messages WHERE id = ? LIMIT 1');
    $stmt->execute([$mid]);
    $message = $stmt->fetch();
    if ($message && $message['attachments']) {
        $message['attachments'] = json_decode($message['attachments'], true);
    }
    
    Utils::jsonResponse(['ok' => true, 'id' => $mid, 'message' => $message]);
}

/**
 * List Messages in Conversation
 * GET/POST/JSON: conversation_id, limit (default 50), offset (default 0)
 */
if ($action === 'message_list' || $action === 'conversation_list') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    $limit = (int)getRequestData('limit', 50);
    $offset = (int)getRequestData('offset', 0);
    
    if (!$conversation_id) Utils::jsonResponse(['error' => 'missing_conversation_id'], 400);
    
    // Verify user is member of conversation
    $stmt = $db->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        Utils::jsonResponse(['error' => 'not_member_of_conversation'], 403);
    }
    
    $messages = $mm->listByConversation($conversation_id, $limit, $offset);
    foreach ($messages as &$msg) {
        if ($msg['attachments']) $msg['attachments'] = json_decode($msg['attachments'], true);
    }
    
    Utils::jsonResponse(['ok' => true, 'messages' => $messages]);
}

/**
 * List User's Conversations
 * GET/POST/JSON: limit (default 50), offset (default 0)
 */
if ($action === 'list') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $limit = (int)getRequestData('limit', 50);
    $offset = (int)getRequestData('offset', 0);
    
    $conversations = $cm->listForUser($_SESSION['user_id'], $limit, $offset);
    $result = [];
    
    foreach ($conversations as $conv) {
        $messages = $mm->listByConversation($conv['id'], 10, 0);
        $last_message = end($messages);
        $result[] = [
            'conversation' => $conv,
            'last_message' => $last_message,
            'messages_count' => count($messages)
        ];
    }
    
    Utils::jsonResponse(['ok' => true, 'conversations' => $result]);
}

/**
 * Mark Message as Read
 * POST/JSON: message_id
 */
if ($action === 'message_mark_read') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $message_id = getRequestData('message_id');
    if (!$message_id) Utils::jsonResponse(['error' => 'missing_message_id'], 400);
    
    $stmt = $db->prepare('UPDATE messages SET is_read = 1 WHERE id = ?');
    $stmt->execute([$message_id]);
    
    Utils::jsonResponse(['ok' => true]);
}

/**
 * Mark All Messages in Conversation as Read
 * POST/JSON: conversation_id
 */
if ($action === 'conversation_mark_read') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    if (!$conversation_id) Utils::jsonResponse(['error' => 'missing_conversation_id'], 400);
    
    $stmt = $db->prepare('UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ?');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    
    Utils::jsonResponse(['ok' => true]);
}

/**
 * Add Member to Conversation
 * POST/JSON: conversation_id, user_id
 */
if ($action === 'conversation_add_member') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    $user_id = getRequestData('user_id');
    
    if (!$conversation_id || !$user_id) Utils::jsonResponse(['error' => 'missing_fields'], 400);
    
    // Verify current user is member
    $stmt = $db->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        Utils::jsonResponse(['error' => 'not_member_of_conversation'], 403);
    }
    
    // Add new member
    $stmt = $db->prepare('INSERT IGNORE INTO conversation_members (conversation_id, user_id) VALUES (?, ?)');
    $stmt->execute([$conversation_id, $user_id]);
    
    Utils::jsonResponse(['ok' => true]);
}

/**
 * Get Conversation Details
 * GET/POST/JSON: conversation_id
 */
if ($action === 'conversation_get') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    if (!$conversation_id) Utils::jsonResponse(['error' => 'missing_conversation_id'], 400);
    
    // Verify user is member
    $stmt = $db->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        Utils::jsonResponse(['error' => 'not_member_of_conversation'], 403);
    }
    
    $stmt = $db->prepare('SELECT * FROM conversations WHERE id = ? LIMIT 1');
    $stmt->execute([$conversation_id]);
    $conversation = $stmt->fetch();
    
    if (!$conversation) Utils::jsonResponse(['error' => 'conversation_not_found'], 404);
    
    // Get members
    $stmt = $db->prepare('SELECT cm.user_id, u.display_name, u.email FROM conversation_members cm LEFT JOIN users u ON cm.user_id = u.id WHERE cm.conversation_id = ?');
    $stmt->execute([$conversation_id]);
    $members = $stmt->fetchAll();
    
    Utils::jsonResponse(['ok' => true, 'conversation' => $conversation, 'members' => $members]);
}

/**
 * Delete Conversation (soft delete by removing user)
 * POST/JSON: conversation_id
 */
if ($action === 'conversation_delete') {
    if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
    
    $conversation_id = getRequestData('conversation_id');
    if (!$conversation_id) Utils::jsonResponse(['error' => 'missing_conversation_id'], 400);
    
    $stmt = $db->prepare('DELETE FROM conversation_members WHERE conversation_id = ? AND user_id = ?');
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    
    Utils::jsonResponse(['ok' => true]);
}

Utils::jsonResponse(['error' => 'unknown_action'], 400);
