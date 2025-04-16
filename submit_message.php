<?php
require_once 'database.php';

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
    header("Location: guestbook.php");
    exit;
} else {
    if ($error) {
        echo "Database connection error. Cannot submit message.";
    } else {
        header("Location: guestbook.php");
        exit;
    }
}
