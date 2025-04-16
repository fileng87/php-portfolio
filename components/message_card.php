<?php

/**
 * Message Card Component
 *
 * Expects the following variables to be set before inclusion:
 * - $content (string): The message content
 * - $author (string): The name of the person who left the message
 * - $timestamp (string|null): Optional timestamp string
 */

$content = $content ?? '內容錯誤';
$author = $author ?? '匿名訪客';
$timestamp = $timestamp ?? null;
$style_attribute = $style_attribute ?? '';

?>
<div class="message-item content-card animate-fade-in-up" <?php echo $style_attribute; ?>>
    <h3 class="card-title"><?php echo htmlspecialchars($author); ?></h3>
    <p class="message-content"><?php echo nl2br(htmlspecialchars($content)); ?></p>
    <div class="message-footer">
        <span class="message-timestamp"><?php echo htmlspecialchars($timestamp ?? ''); ?></span>
    </div>
</div>