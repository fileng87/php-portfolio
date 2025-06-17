<?php
require_once __DIR__ . '/../config/session_helper.php';
require_once __DIR__ . '/../config/database.php';

// 檢查是否已登入
if (!is_logged_in()) {
    header("Location: /login?error=" . urlencode("請先登入以訪問個人設定"));
    exit;
}

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /profile");
    exit;
}

// 驗證 CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    header("Location: /profile?error=" . urlencode("安全驗證失敗，請重新提交"));
    exit;
}

$current_user = get_logged_in_user();
$pdo = get_db_connection();
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'update_avatar':
            handle_avatar_update($pdo, $current_user);
            break;

        case 'update_profile':
            handle_profile_update($pdo, $current_user);
            break;

        case 'update_password':
            handle_password_update($pdo, $current_user);
            break;

        default:
            header("Location: /profile?error=" . urlencode("無效的操作"));
            exit;
    }
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    header("Location: /profile?error=" . urlencode("系統錯誤，請稍後再試"));
    exit;
}

/**
 * 處理大頭貼上傳
 */
function handle_avatar_update($pdo, $current_user)
{
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        header("Location: /profile?error=" . urlencode("請選擇要上傳的圖片檔案"));
        exit;
    }

    $file = $_FILES['avatar'];

    // 檢查檔案大小 (2MB 限制)
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: /profile?error=" . urlencode("檔案大小不能超過 2MB"));
        exit;
    }

    // 檢查檔案類型
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        header("Location: /profile?error=" . urlencode("只支援 JPG、PNG、GIF 格式的圖片"));
        exit;
    }

    // 生成唯一檔案名
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $current_user['id'] . '_' . time() . '.' . $file_extension;
    $upload_path = 'public/uploads/avatars/' . $new_filename;

    // 移動上傳的檔案
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        header("Location: /profile?error=" . urlencode("檔案上傳失敗"));
        exit;
    }

    // 刪除舊的大頭貼檔案
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$current_user['id']]);
    $old_avatar = $stmt->fetchColumn();

    if ($old_avatar && file_exists($old_avatar)) {
        unlink($old_avatar);
    }

    // 更新資料庫
    $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$upload_path, $current_user['id']]);

    // 更新 session 中的使用者資料
    $_SESSION['user']['avatar'] = $upload_path;

    header("Location: /profile?success=" . urlencode("大頭貼更新成功"));
    exit;
}

/**
 * 處理個人資料更新
 */
function handle_profile_update($pdo, $current_user)
{
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');

    // 驗證輸入
    if (empty($username) || empty($email)) {
        header("Location: /profile?error=" . urlencode("使用者名稱和電子郵件不能為空"));
        exit;
    }

    // 驗證使用者名稱格式
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        header("Location: /profile?error=" . urlencode("使用者名稱格式不正確"));
        exit;
    }

    // 驗證電子郵件格式
    if (!validate_email($email)) {
        header("Location: /profile?error=" . urlencode("電子郵件格式不正確"));
        exit;
    }

    // 檢查使用者名稱是否已被其他使用者使用
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $current_user['id']]);
    if ($stmt->fetch()) {
        header("Location: /profile?error=" . urlencode("使用者名稱已被使用"));
        exit;
    }

    // 檢查電子郵件是否已被其他使用者使用
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $current_user['id']]);
    if ($stmt->fetch()) {
        header("Location: /profile?error=" . urlencode("電子郵件已被使用"));
        exit;
    }

    // 更新資料庫
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$username, $email, $current_user['id']]);

    // 更新 session 中的使用者資料
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;

    header("Location: /profile?success=" . urlencode("個人資料更新成功"));
    exit;
}

/**
 * 處理密碼更新
 */
function handle_password_update($pdo, $current_user)
{
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 驗證輸入
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header("Location: /profile?error=" . urlencode("所有密碼欄位都必須填寫"));
        exit;
    }

    // 驗證新密碼格式
    $password_errors = validate_password($new_password);
    if (!empty($password_errors)) {
        header("Location: /profile?error=" . urlencode(implode('，', $password_errors)));
        exit;
    }

    // 檢查新密碼確認
    if ($new_password !== $confirm_password) {
        header("Location: /profile?error=" . urlencode("新密碼確認不一致"));
        exit;
    }

    // 驗證目前密碼
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$current_user['id']]);
    $stored_hash = $stmt->fetchColumn();

    if (!password_verify($current_password, $stored_hash)) {
        header("Location: /profile?error=" . urlencode("目前密碼不正確"));
        exit;
    }

    // 檢查新密碼是否與目前密碼相同
    if (password_verify($new_password, $stored_hash)) {
        header("Location: /profile?error=" . urlencode("新密碼不能與目前密碼相同"));
        exit;
    }

    // 更新密碼
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_hash, $current_user['id']]);

    header("Location: /profile?success=" . urlencode("密碼更新成功"));
    exit;
}
