<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_helper.php';

// 確保是 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /login");
    exit;
}

// 驗證 CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    header("Location: /login?error=" . urlencode("安全驗證失敗，請重新嘗試"));
    exit;
}

// 取得並清理輸入資料
$username = sanitize_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

// 基本驗證
if (empty($username)) {
    $errors[] = '請輸入使用者名稱或電子郵件';
}

if (empty($password)) {
    $errors[] = '請輸入密碼';
}

// 如果有驗證錯誤，返回登入頁面
if (!empty($errors)) {
    $error_message = implode('、', $errors);
    $query_params = http_build_query([
        'error' => $error_message,
        'username' => $username
    ]);
    header("Location: /login?" . $query_params);
    exit;
}

// 資料庫操作
$pdo = get_db_connection();
$db_error = get_db_error();

if (!$pdo || $db_error) {
    header("Location: /login?error=" . urlencode("資料庫連接失敗，請稍後再試"));
    exit;
}

try {
    // 查詢使用者（支援使用者名稱或電子郵件登入）
    $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $username);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        // 使用者不存在
        $query_params = http_build_query([
            'error' => '使用者名稱或密碼錯誤',
            'username' => $username
        ]);
        header("Location: /login?" . $query_params);
        exit;
    }

    // 驗證密碼
    if (!password_verify($password, $user['password_hash'])) {
        // 密碼錯誤
        $query_params = http_build_query([
            'error' => '使用者名稱或密碼錯誤',
            'username' => $username
        ]);
        header("Location: /login?" . $query_params);
        exit;
    }

    // 登入成功，設定 session
    login_user([
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ]);

    // 檢查是否有重定向目標
    $redirect_to = $_POST['redirect_to'] ?? 'index.php';

    // 確保重定向目標是安全的（防止開放重定向攻擊）
    $allowed_redirects = ['index.php', 'guestbook.php', 'portfolio.php', 'about.php'];
    if (!in_array($redirect_to, $allowed_redirects)) {
        $redirect_to = 'index.php';
    }

    header("Location: " . $redirect_to);
    exit;
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: /login?error=" . urlencode("登入失敗，請稍後再試"));
    exit;
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: /login?error=" . urlencode("登入失敗，請稍後再試"));
    exit;
}
