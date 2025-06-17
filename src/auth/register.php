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
        <span class="prompt-icon">👤</span> $ ./register.sh --create-account <span class="cursor">|</span>
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

    <!-- Registration Form -->
    <form action="/register_process" method="POST" class="auth-form content-card animate-fade-in-up" style="--card-index: 1;">
        <h3 class="card-title">建立新帳號</h3>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group">
            <label for="username">使用者名稱：</label>
            <input type="text" id="username" name="username" required
                pattern="[a-zA-Z0-9_]{3,20}"
                title="3-20個字元，只能包含字母、數字和底線"
                value="<?php echo isset($_GET['username']) ? sanitize_input($_GET['username']) : ''; ?>">
            <small class="form-help">3-20個字元，只能包含字母、數字和底線</small>
        </div>

        <div class="form-group">
            <label for="email">電子郵件：</label>
            <input type="email" id="email" name="email" required
                value="<?php echo isset($_GET['email']) ? sanitize_input($_GET['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" required>
            <small class="form-help">至少6個字元，需包含字母和數字</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">確認密碼：</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-button">建立帳號</button>
            <a href="/login" class="link-button">已有帳號？登入</a>
        </div>
    </form>

    <!-- Additional Info -->
    <div class="auth-info content-card animate-fade-in-up" style="--card-index: 2;">
        <h4>為什麼要註冊？</h4>
        <ul>
            <li>在留言板留言時顯示您的使用者名稱</li>
            <li>未來可能新增的個人化功能</li>
            <li>與其他使用者互動</li>
        </ul>
    </div>
</div>



<?php include __DIR__ . '/../components/footer.php'; ?>