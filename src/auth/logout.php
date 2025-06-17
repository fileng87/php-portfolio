<?php
require_once __DIR__ . '/../config/session_helper.php';

// 登出使用者
logout_user();

// 重定向到首頁，並顯示登出成功訊息
header("Location: /?message=" . urlencode("您已成功登出"));
exit;
