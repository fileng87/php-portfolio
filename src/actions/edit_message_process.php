<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_helper.php';

header('Content-Type: application/json');

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// 檢查用戶是否已登入
$current_user = get_logged_in_user();
if (!$current_user) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// 獲取 POST 數據
$input = json_decode(file_get_contents('php://input'), true);
$message_id = $input['message_id'] ?? null;
$new_content = $input['content'] ?? null;

// 驗證輸入
if (!$message_id || !$new_content) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing message ID or content']);
    exit;
}

// 驗證內容長度
$new_content = trim($new_content);
if (empty($new_content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Content cannot be empty']);
    exit;
}

if (strlen($new_content) > 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'Content too long (max 1000 characters)']);
    exit;
}

$pdo = get_db_connection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // 檢查留言是否存在並且屬於當前用戶
    $stmt = $pdo->prepare("
        SELECT m.id, m.author, u.id as user_id 
        FROM messages m 
        LEFT JOIN users u ON m.author = u.username 
        WHERE m.id = ?
    ");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();

    if (!$message) {
        http_response_code(404);
        echo json_encode(['error' => 'Message not found']);
        exit;
    }

    // 檢查是否為留言作者
    if ($message['user_id'] != $current_user['id']) {
        http_response_code(403);
        echo json_encode(['error' => 'You can only edit your own messages']);
        exit;
    }

    // 更新留言內容
    $stmt = $pdo->prepare("UPDATE messages SET content = ? WHERE id = ?");
    $stmt->execute([$new_content, $message_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Message updated successfully',
        'content' => htmlspecialchars($new_content)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
