<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session_helper.php';

$current_user = get_logged_in_user();

$pdo = get_db_connection();
$db_error = get_db_error();
$messages = [];

if ($pdo && !$db_error) {
    try {
        // Fetch messages with user info, likes, dislikes, and user's current reaction
        $current_user_id = $current_user ? $current_user['id'] : 0;
        $stmt = $pdo->prepare("
            SELECT m.id, m.author, m.content, m.timestamp, u.id as author_id, u.avatar, u.display_name as author_display_name,
                   COALESCE(likes.count, 0) as likes,
                   COALESCE(dislikes.count, 0) as dislikes,
                   user_reaction.reaction_type as user_reaction
            FROM messages m 
            LEFT JOIN users u ON m.author = u.username 
            LEFT JOIN (
                SELECT message_id, COUNT(*) as count 
                FROM message_reactions 
                WHERE reaction_type = 'like' 
                GROUP BY message_id
            ) likes ON m.id = likes.message_id
            LEFT JOIN (
                SELECT message_id, COUNT(*) as count 
                FROM message_reactions 
                WHERE reaction_type = 'dislike' 
                GROUP BY message_id
            ) dislikes ON m.id = dislikes.message_id
            LEFT JOIN (
                SELECT message_id, reaction_type
                FROM message_reactions
                WHERE user_id = ?
            ) user_reaction ON m.id = user_reaction.message_id
            ORDER BY m.timestamp DESC
        ");
        $stmt->execute([$current_user_id]);
        $messages = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Handle fetch error
        $db_error = "Error fetching messages: " . $e->getMessage();
        error_log($db_error);
    }
} elseif (!$pdo) {
    $db_error = "Database connection is not available.";
}

$page_title = "留言板";
$page_styles = [
    '/src/pages/guestbook/style.css',
    '/src/components/message-card/style.css'
];

ob_start();
?>

<div class="page-content guestbook-page">
    <!-- Terminal Header -->
    <div class="terminal-header guestbook-terminal-header animate-fade-in-up">
        <span class="prompt-icon">📝</span> $ tail -f ./guestbook.log <span class="cursor">|</span>
    </div>

    <!-- Display DB Error if any -->
    <?php if ($db_error): ?>
        <div class="error-message content-card animate-fade-in-up" style="--card-index: 0;">
            <p><strong>錯誤：</strong> <?php echo htmlspecialchars($db_error); ?></p>
        </div>
    <?php endif; ?>

    <!-- Message Submission Form -->
    <?php if ($current_user): ?>
        <!-- 已登入使用者 - 可以留言 -->
        <form action="/submit_message" method="POST" class="guestbook-form content-card animate-fade-in-up" style="--card-index: 0;">
            <h3 class="card-title">留下訊息</h3>

            <div class="form-group">
                <label for="guest_message">想說的話：</label>
                <textarea id="guest_message" name="guest_message" rows="4" required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-button">送出留言</button>
            </div>
        </form>
    <?php else: ?>
        <!-- 未登入使用者 - 需要登入才能留言 -->
        <div class="login-required content-card animate-fade-in-up" style="--card-index: 0;">
            <h3 class="card-title">留下訊息</h3>
            <div class="login-prompt">
                <p>🔒 您需要登入才能留言</p>
                <p>登入後可以使用您的使用者名稱留言，並享受更好的體驗！</p>
                <div class="login-actions">
                    <a href="/login" class="submit-button">立即登入</a>
                    <a href="/register" class="link-button">還沒有帳號？註冊</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Message Display Area -->
    <div class="guestbook-messages">
        <?php if (!$db_error && !empty($messages)): ?>
            <?php foreach ($messages as $index => $message): // Get index here 
            ?>
                <?php
                $message_id = $message['id'];
                $content = $message['content'];
                // 使用顯示名稱（如果有的話）
                $author_user = [
                    'username' => $message['author'],
                    'display_name' => $message['author_display_name'] ?? null
                ];
                $author = get_user_display_name($author_user);
                $avatar = $message['avatar'];
                $author_id = $message['author_id'];
                $likes = $message['likes'];
                $dislikes = $message['dislikes'];
                $user_reaction = $message['user_reaction'] ?? null;
                $current_user_id = $current_user ? $current_user['id'] : null;
                $dt = new DateTime($message['timestamp']);
                $timestamp = $dt->format('Y-m-d H:i');
                // Set the CSS variable inline for animation delay (start messages from index 1)
                $style_attribute = 'style="--card-index: ' . ($index + 1) . ';"';

                include __DIR__ . '/../../components/message-card/index.php';
                ?>
            <?php endforeach; ?>
        <?php elseif (!$db_error): ?>
            <!-- Use card structure for no messages -->
            <div class="message-item content-card no-messages-placeholder animate-fade-in-up" style="--card-index: 1;">
                <p>目前還沒有留言，快來搶頭香！</p>
            </div>
        <?php endif; ?>
        <!-- Don't show placeholders if there's a DB error -->
    </div>

</div>

<?php
$page_content = ob_get_clean();

// 加入 JavaScript 檔案
$page_scripts = '<script src="/src/pages/guestbook/guestbook.js"></script>';

include __DIR__ . '/../../layout/index.php';
?>