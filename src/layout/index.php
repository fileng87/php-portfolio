<?php

/**
 * 主要佈局檔案 - Main Layout
 * 負責載入全局樣式、組件樣式和頁面樣式
 */

require_once __DIR__ . '/../config/session_helper.php';

// 獲取當前頁面資訊
$page_title = $page_title ?? '鳳梨的個人網站';
$page_styles = $page_styles ?? [];
$page_scripts = $page_scripts ?? '';

$current_user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- 全局樣式 -->
    <link rel="stylesheet" href="/src/global.css">

    <!-- 組件樣式 -->
    <link rel="stylesheet" href="/src/components/header/style.css">
    <link rel="stylesheet" href="/src/components/footer/style.css">
    <link rel="stylesheet" href="/src/components/background-effect/style.css">

    <!-- 頁面專屬樣式 -->
    <?php foreach ($page_styles as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
    <?php endforeach; ?>
</head>

<body>
    <?php include __DIR__ . '/../components/background-effect/index.php'; ?>
    <div class="site-wrapper">
        <?php include __DIR__ . '/../components/header/index.php'; ?>
        <main>
            <?php
            // 載入頁面內容 - 統一使用字串方式
            if (isset($page_content)) {
                echo $page_content;
            } else {
                echo '<p>頁面內容載入錯誤</p>';
            }
            ?>
        </main>
        <?php include __DIR__ . '/../components/footer/index.php'; ?>
    </div>

    <!-- 頁面專屬腳本 -->
    <?php if (!empty($page_scripts)): ?>
        <?php echo $page_scripts; ?>
    <?php endif; ?>
</body>

</html>