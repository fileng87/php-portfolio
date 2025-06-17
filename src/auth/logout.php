<?php
require_once __DIR__ . '/../config/session_helper.php';

// 登出使用者
logout_user();

// 重定向到登入頁面，並顯示登出成功訊息
header("Location: /login?success=" . urlencode("您已成功登出"));
exit;
