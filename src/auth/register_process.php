<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_helper.php';

// 確保是 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /register");
    exit;
}

// 驗證 CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    header("Location: /register?error=" . urlencode("安全驗證失敗，請重新嘗試"));
    exit;
}

// 取得並清理輸入資料
$username = sanitize_input($_POST['username'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];

// 基本驗證
if (empty($username)) {
    $errors[] = '請輸入使用者名稱';
} elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors[] = '使用者名稱格式不正確';
}

if (empty($email)) {
    $errors[] = '請輸入電子郵件';
} elseif (!validate_email($email)) {
    $errors[] = '電子郵件格式不正確';
}

if (empty($password)) {
    $errors[] = '請輸入密碼';
} else {
    $password_errors = validate_password($password);
    if (!empty($password_errors)) {
        $errors = array_merge($errors, $password_errors);
    }
}

if ($password !== $confirm_password) {
    $errors[] = '密碼確認不一致';
}

// 如果有驗證錯誤，返回註冊頁面
if (!empty($errors)) {
    $error_message = implode('、', $errors);
    $query_params = http_build_query([
        'error' => $error_message,
        'username' => $username,
        'email' => $email
    ]);
    header("Location: /register?" . $query_params);
    exit;
}

// 資料庫操作
$pdo = get_db_connection();
$db_error = get_db_error();

if (!$pdo || $db_error) {
    header("Location: /register?error=" . urlencode("資料庫連接失敗，請稍後再試"));
    exit;
}

try {
    // 檢查使用者名稱是否已存在
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $query_params = http_build_query([
            'error' => '使用者名稱已存在',
            'email' => $email
        ]);
        header("Location: /register?" . $query_params);
        exit;
    }

    // 檢查電子郵件是否已存在
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $query_params = http_build_query([
            'error' => '電子郵件已被註冊',
            'username' => $username
        ]);
        header("Location: /register?" . $query_params);
        exit;
    }

    // 建立新使用者
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);

    if ($stmt->execute()) {
        // 註冊成功，重定向到登入頁面
        header("Location: /login?success=" . urlencode("註冊成功！請使用您的帳號登入"));
        exit;
    } else {
        throw new Exception("註冊失敗");
    }
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());

    // 檢查是否為重複鍵錯誤
    if ($e->getCode() == 23000) {
        $error_message = '使用者名稱或電子郵件已存在';
    } else {
        $error_message = '註冊失敗，請稍後再試';
    }

    $query_params = http_build_query([
        'error' => $error_message,
        'username' => $username,
        'email' => $email
    ]);
    header("Location: /register?" . $query_params);
    exit;
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    header("Location: /register?error=" . urlencode("註冊失敗，請稍後再試"));
    exit;
}
