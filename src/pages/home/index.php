<?php

/**
 * 首頁模組
 */

// 設定頁面標題和樣式
$page_title = '鳳梨的個人網站';
$page_styles = ['/src/pages/home/style.css'];

// 使用 ob_start 捕獲頁面內容
ob_start();
?>

<div class="hero-content">
    <h1 class="main-title animate-fade-in-up">
        <span>JUST</span> <span class="color-code">CODE</span> <span>FOR</span> <span class="color-fun">FUN</span> <span class="emoji">🍍</span>
    </h1>
    <p class="subtitle animate-fade-in-up">Ctrl+C Ctrl+V Developer</p>
</div>
<?php
$page_content = ob_get_clean();

// 載入佈局
include __DIR__ . '/../../layout/index.php';
?>