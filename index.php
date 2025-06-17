<?php

/**
 * 主要入口點 - 整合路由功能
 * 用於 PHP 內建伺服器的路由處理
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// 移除開頭的斜線
$path = ltrim($path, '/');

// 處理靜態檔案
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $path)) {
    return false; // 讓 PHP 內建伺服器處理靜態檔案
}

// 定義路由規則
$routes = [
    // 頁面路由
    '' => 'src/pages/index.php',
    'index.php' => 'src/pages/index.php',
    'about' => 'src/pages/about.php',
    'portfolio' => 'src/pages/portfolio.php',
    'guestbook' => 'src/pages/guestbook.php',
    'profile' => 'src/pages/profile.php',

    // 認證路由
    'login' => 'src/auth/login.php',
    'register' => 'src/auth/register.php',
    'logout' => 'src/auth/logout.php',

    // 動作路由
    'login_process' => 'src/auth/login_process.php',
    'register_process' => 'src/auth/register_process.php',
    'profile_process' => 'src/actions/profile_process.php',
    'submit_message.php' => 'src/actions/submit_message.php',

    // 向後相容的 .php 路由
    'about.php' => 'src/pages/about.php',
    'portfolio.php' => 'src/pages/portfolio.php',
    'guestbook.php' => 'src/pages/guestbook.php',
    'profile.php' => 'src/pages/profile.php',
    'login.php' => 'src/auth/login.php',
    'register.php' => 'src/auth/register.php',
    'login_process.php' => 'src/auth/login_process.php',
    'register_process.php' => 'src/auth/register_process.php',
    'profile_process.php' => 'src/actions/profile_process.php',
];

// 檢查路由是否存在
if (isset($routes[$path])) {
    $file = $routes[$path];
    if (file_exists($file)) {
        include $file;
        exit;
    }
}

// 如果找不到路由，返回 404
http_response_code(404);
echo "<!DOCTYPE html>
<html>
<head>
    <title>404 - 頁面不存在</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>404 - 頁面不存在</h1>
    <p>您要找的頁面不存在。</p>
    <a href='/'>返回首頁</a>
</body>
</html>";
