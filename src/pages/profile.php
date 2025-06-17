<?php
require_once __DIR__ . '/../config/session_helper.php';
require_once __DIR__ . '/../config/database.php';

// 檢查是否已登入
if (!is_logged_in()) {
    header("Location: /login?error=" . urlencode("請先登入以訪問個人設定"));
    exit;
}

$current_user = get_logged_in_user();
$pdo = get_db_connection();

// 從資料庫取得最新的使用者資料
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$current_user['id']]);
$user_data = $stmt->fetch();

// 處理訊息顯示
$error_message = '';
$success_message = '';

if (isset($_GET['error'])) {
    $error_message = sanitize_input($_GET['error']);
}

if (isset($_GET['success'])) {
    $success_message = sanitize_input($_GET['success']);
}

include __DIR__ . '/../components/header.php';
?>

<div class="page-content profile-page">
    <!-- Terminal Header -->
    <div class="terminal-header profile-terminal-header animate-fade-in-up">
        <span class="prompt-icon">⚙️</span> $ ./profile.sh --edit-user <span class="cursor">|</span>
    </div>

    <!-- Display Messages -->
    <?php if ($error_message): ?>
        <div class="error-message content-card animate-fade-in-up" style="--card-index: 0;">
            <p><strong>錯誤：</strong> <?php echo $error_message; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success-message content-card animate-fade-in-up" style="--card-index: 0;">
            <p><strong>成功：</strong> <?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <div class="profile-container">
        <!-- Avatar Section -->
        <div class="avatar-section content-card animate-fade-in-up" style="--card-index: 1;">
            <h3 class="card-title">大頭貼</h3>
            <div class="avatar-display">
                <?php if ($user_data['avatar']): ?>
                    <img src="<?php echo sanitize_input($user_data['avatar']); ?>" alt="使用者大頭貼" class="current-avatar">
                <?php else: ?>
                    <div class="default-avatar">
                        <span class="avatar-icon">👤</span>
                    </div>
                <?php endif; ?>
            </div>

            <form action="/profile_process" method="POST" enctype="multipart/form-data" class="avatar-form">
                <input type="hidden" name="action" value="update_avatar">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-group">
                    <label for="avatar">選擇新大頭貼：</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="file-input">
                    <small class="form-help">支援 JPG、PNG、GIF 格式，檔案大小不超過 2MB</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">更新大頭貼</button>
                </div>
            </form>
        </div>

        <!-- Profile Info Section -->
        <div class="profile-info content-card animate-fade-in-up" style="--card-index: 2;">
            <h3 class="card-title">個人資料</h3>

            <form action="/profile_process" method="POST" class="profile-form">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-group">
                    <label for="username">使用者名稱：</label>
                    <input type="text" id="username" name="username"
                        value="<?php echo sanitize_input($user_data['username']); ?>"
                        required pattern="[a-zA-Z0-9_]{3,20}"
                        title="3-20個字元，只能包含字母、數字和底線">
                    <small class="form-help">3-20個字元，只能包含字母、數字和底線</small>
                </div>

                <div class="form-group">
                    <label for="email">電子郵件：</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo sanitize_input($user_data['email']); ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">更新個人資料</button>
                </div>
            </form>
        </div>

        <!-- Password Section -->
        <div class="password-section content-card animate-fade-in-up" style="--card-index: 3;">
            <h3 class="card-title">修改密碼</h3>

            <form action="/profile_process" method="POST" class="password-form">
                <input type="hidden" name="action" value="update_password">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-group">
                    <label for="current_password">目前密碼：</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">新密碼：</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small class="form-help">至少6個字元，需包含字母和數字</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">確認新密碼：</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">更新密碼</button>
                </div>
            </form>
        </div>

        <!-- Account Info -->
        <div class="account-info content-card animate-fade-in-up" style="--card-index: 4;">
            <h3 class="card-title">帳號資訊</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">使用者 ID：</span>
                    <span class="info-value"><?php echo $user_data['id']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">註冊時間：</span>
                    <span class="info-value"><?php echo date('Y-m-d H:i', strtotime($user_data['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">最後更新：</span>
                    <span class="info-value"><?php echo date('Y-m-d H:i', strtotime($user_data['updated_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>