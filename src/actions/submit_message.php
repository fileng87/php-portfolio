<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_helper.php';

// 檢查使用者是否已登入
$current_user = get_logged_in_user();
if (!$current_user) {
    header("Location: /login?error=" . urlencode("您需要登入才能留言"));
    exit;
}

$pdo = get_db_connection();
$error = get_db_error();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $pdo && !$error) {
    $message = trim($_POST['guest_message'] ?? '');
    $author = $current_user['username']; // 使用當前登入用戶的用戶名

    // Basic validation
    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (author, content) VALUES (:author, :content)");
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':content', $message);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error inserting message: " . $e->getMessage());
        }
    }
    // Redirect back to the guestbook page
    header("Location: /guestbook");
    exit;
} else {
    if ($error) {
        echo "Database connection error. Cannot submit message.";
    } else {
        header("Location: /guestbook");
        exit;
    }
}
