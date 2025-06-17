<?php
// 啟動 session（如果尚未啟動）
function start_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// 檢查使用者是否已登入
function is_logged_in(): bool
{
    start_session();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// 取得當前登入的使用者資訊
function get_logged_in_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? ''
    ];
}

// 登入使用者
function login_user(array $user): void
{
    start_session();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];

    // 重新生成 session ID 以防止 session fixation 攻擊
    session_regenerate_id(true);
}

// 登出使用者
function logout_user(): void
{
    start_session();

    // 清除所有 session 變數
    $_SESSION = [];

    // 刪除 session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // 銷毀 session
    session_destroy();
}

// 生成 CSRF token
function generate_csrf_token(): string
{
    start_session();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 驗證 CSRF token
function verify_csrf_token(string $token): bool
{
    start_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// 驗證密碼強度
function validate_password(string $password): array
{
    $errors = [];

    if (strlen($password) < 6) {
        $errors[] = '密碼長度至少需要 6 個字元';
    }

    if (!preg_match('/[A-Za-z]/', $password)) {
        $errors[] = '密碼必須包含至少一個字母';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = '密碼必須包含至少一個數字';
    }

    return $errors;
}

// 驗證 email 格式
function validate_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// 清理輸入資料
function sanitize_input(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
