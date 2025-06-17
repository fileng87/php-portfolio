<?php
require_once __DIR__ . '/../config/session_helper.php';
$current_user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>鳳梨的個人網站</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/background_effect.php'; ?>
    <div class="site-wrapper">
        <header>
            <div class="header-container">
                <div class="header-left">
                    <a href="/" class="logo">鳳梨的網站</a>
                </div>
                <div class="header-center">
                    <nav>
                        <ul>
                            <li><a href="/">首頁</a></li>
                            <li><a href="/about">關於我</a></li>
                            <li><a href="/portfolio">作品集</a></li>
                            <li><a href="/guestbook">留言板</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="header-right">
                    <div class="header-right-content">
                        <?php if ($current_user): ?>
                            <!-- 已登入狀態 -->
                            <div class="user-menu">
                                <span class="user-greeting">👋 <?php echo sanitize_input($current_user['username']); ?></span>
                                <a href="/profile" class="profile-button" title="個人設定">⚙️</a>
                                <a href="/logout" class="logout-button" title="登出">🚪</a>
                            </div>
                        <?php else: ?>
                            <!-- 未登入狀態 -->
                            <div class="auth-buttons">
                                <a href="/login" class="auth-button login-button">登入</a>
                                <a href="/register" class="auth-button register-button">註冊</a>
                            </div>
                        <?php endif; ?>
                        <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank" class="icon-button" title="驚喜！">🎉</a> <!-- 瑞克搖連結 -->
                    </div>
                </div>
            </div>
        </header>
        <main>