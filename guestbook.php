<?php
require_once 'database.php';
include 'components/header.php';

$pdo = get_db_connection();
$db_error = get_db_error();
$messages = [];

if ($pdo && !$db_error) {
    try {
        // Fetch messages, newest first
        $stmt = $pdo->query("SELECT author, content, timestamp FROM messages ORDER BY timestamp DESC");
        $messages = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Handle fetch error
        $db_error = "Error fetching messages: " . $e->getMessage();
        error_log($db_error);
    }
} elseif (!$pdo) {
    $db_error = "Database connection is not available.";
}

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
    <form action="submit_message.php" method="POST" class="guestbook-form content-card animate-fade-in-up" style="--card-index: 0;">
        <h3 class="card-title">留下訊息</h3>
        <div class="form-group">
            <label for="guest_name">你的名字：</label>
            <input type="text" id="guest_name" name="guest_name" required>
        </div>
        <div class="form-group">
            <label for="guest_message">想說的話：</label>
            <textarea id="guest_message" name="guest_message" rows="4" required></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="submit-button">送出留言</button>
        </div>
    </form>

    <!-- Message Display Area -->
    <div class="guestbook-messages">
        <?php if (!$db_error && !empty($messages)): ?>
            <?php foreach ($messages as $index => $message): // Get index here 
            ?>
                <?php
                $content = $message['content'];
                $author = $message['author'];
                $dt = new DateTime($message['timestamp']);
                $timestamp = $dt->format('Y-m-d H:i');
                // Set the CSS variable inline for animation delay (start messages from index 1)
                $style_attribute = 'style="--card-index: ' . ($index + 1) . ';"';

                include 'components/message_card.php';
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

<?php include 'components/footer.php'; ?>