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
$reaction_type = $input['reaction_type'] ?? null;

// 驗證輸入
if (!$message_id || !in_array($reaction_type, ['like', 'dislike'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$pdo = get_db_connection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    $user_id = $current_user['id'];

    // 檢查用戶是否已經對這則留言有反應
    $stmt = $pdo->prepare("SELECT reaction_type FROM message_reactions WHERE user_id = ? AND message_id = ?");
    $stmt->execute([$user_id, $message_id]);
    $existing_reaction = $stmt->fetch();

    if ($existing_reaction) {
        if ($existing_reaction['reaction_type'] === $reaction_type) {
            // 如果點擊相同的反應，則移除反應
            $stmt = $pdo->prepare("DELETE FROM message_reactions WHERE user_id = ? AND message_id = ?");
            $stmt->execute([$user_id, $message_id]);
            $action = 'removed';
        } else {
            // 如果點擊不同的反應，則更新反應
            $stmt = $pdo->prepare("UPDATE message_reactions SET reaction_type = ? WHERE user_id = ? AND message_id = ?");
            $stmt->execute([$reaction_type, $user_id, $message_id]);
            $action = 'updated';
        }
    } else {
        // 新增反應
        $stmt = $pdo->prepare("INSERT INTO message_reactions (user_id, message_id, reaction_type) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $message_id, $reaction_type]);
        $action = 'added';
    }

    // 獲取更新後的統計數據
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(CASE WHEN reaction_type = 'like' THEN 1 ELSE 0 END), 0) as likes,
            COALESCE(SUM(CASE WHEN reaction_type = 'dislike' THEN 1 ELSE 0 END), 0) as dislikes
        FROM message_reactions 
        WHERE message_id = ?
    ");
    $stmt->execute([$message_id]);
    $stats = $stmt->fetch();

    // 檢查用戶當前的反應狀態
    $stmt = $pdo->prepare("SELECT reaction_type FROM message_reactions WHERE user_id = ? AND message_id = ?");
    $stmt->execute([$user_id, $message_id]);
    $current_reaction = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'action' => $action,
        'likes' => (int)$stats['likes'],
        'dislikes' => (int)$stats['dislikes'],
        'user_reaction' => $current_reaction ? $current_reaction['reaction_type'] : null
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
