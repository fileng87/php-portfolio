<?php

/**
 * Message Card Component
 *
 * Expects the following variables to be set before inclusion:
 * - $content (string): The message content
 * - $author (string): The name of the person who left the message
 * - $timestamp (string|null): Optional timestamp string
 * - $avatar (string|null): Optional user avatar path
 */

$content = $content ?? '內容錯誤';
$author = $author ?? '匿名訪客';
$timestamp = $timestamp ?? null;
$avatar = $avatar ?? null;
$style_attribute = $style_attribute ?? '';

?>
<div class="message-item content-card animate-fade-in-up" <?php echo $style_attribute; ?>>
    <div class="message-header">
        <div class="message-author-info">
            <?php if ($avatar && file_exists($avatar)): ?>
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="<?php echo htmlspecialchars($author); ?>的大頭貼" class="message-avatar">
            <?php else: ?>
                <div class="message-avatar-placeholder">
                    <span class="avatar-icon">👤</span>
                </div>
            <?php endif; ?>
            <h3 class="card-title"><?php echo htmlspecialchars($author); ?></h3>
        </div>
        <span class="message-timestamp"><?php echo htmlspecialchars($timestamp ?? ''); ?></span>
    </div>
    <p class="message-content"><?php echo nl2br(htmlspecialchars($content)); ?></p>
</div>