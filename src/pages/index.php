<?php
require_once __DIR__ . '/../config/session_helper.php';
include __DIR__ . '/../components/header.php';

// 處理訊息顯示
$message = '';
if (isset($_GET['message'])) {
    $message = sanitize_input($_GET['message']);
}
?>

<?php if ($message): ?>
    <div class="page-message success-message animate-fade-in-up">
        <p><?php echo $message; ?></p>
    </div>
<?php endif; ?>

<div class="hero-content">
    <h1 class="main-title animate-fade-in-up">
        <span>JUST</span> <span class="color-code">CODE</span> <span>FOR</span> <span class="color-fun">FUN</span> <span class="emoji">🍍</span>
    </h1>
    <p class="subtitle animate-fade-in-up">Ctrl+C Ctrl+V Developer</p>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>