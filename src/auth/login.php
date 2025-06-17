<?php
require_once __DIR__ . '/../config/session_helper.php';

// 如果已經登入，重定向到首頁
if (is_logged_in()) {
    header("Location: /");
    exit;
}

// 處理錯誤訊息顯示
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

<div class="page-content auth-page">
    <!-- Terminal Header -->
    <div class="terminal-header auth-terminal-header animate-fade-in-up">
        <span class="prompt-icon">🔐</span> $ ./login.sh --authenticate <span class="cursor">|</span>
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

    <!-- Login Form -->
    <form action="/login_process" method="POST" class="auth-form content-card animate-fade-in-up" style="--card-index: 1;">
        <h3 class="card-title">登入帳號</h3>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group">
            <label for="username">使用者名稱或電子郵件：</label>
            <input type="text" id="username" name="username" required
                value="<?php echo isset($_GET['username']) ? sanitize_input($_GET['username']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-button">登入</button>
            <a href="/register" class="link-button">還沒有帳號？註冊</a>
        </div>
    </form>

    <!-- Additional Info -->
    <div class="auth-info content-card animate-fade-in-up" style="--card-index: 2;">
        <h4>登入後您可以：</h4>
        <ul>
            <li>在留言板以您的使用者名稱留言</li>
            <li>享受個人化的網站體驗</li>
            <li>與其他使用者進行互動</li>
        </ul>

        <div class="demo-info">
            <h5>測試帳號（如果您想快速體驗）：</h5>
            <p><small>使用者名稱：demo | 密碼：demo123</small></p>
        </div>
    </div>
</div>



<?php include __DIR__ . '/../components/footer.php'; ?>