<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_helper.php';

// 檢查使用者是否已登入
if (!is_logged_in()) {
    header("Location: /login?error=" . urlencode("您需要登入才能留言"));
    exit;
}

$pdo = get_db_connection();
$error = get_db_error();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $pdo && !$error) {
    $name = trim($_POST['guest_name'] ?? '');
    $message = trim($_POST['guest_message'] ?? '');

    // Basic validation
    if (!empty($name) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (author, content) VALUES (:author, :content)");
            $stmt->bindParam(':author', $name); // Bind the original trimmed name
            $stmt->bindParam(':content', $message);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error inserting message: " . $e->getMessage());
        }
    }
    // Redirect back to the guestbook page regardless of success/error
    // to prevent form resubmission on refresh.
    // In a real app, you might show success/error messages.
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
