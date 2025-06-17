<?php

/**
 * Message Card Component
 *
 * Expects the following variables to be set before inclusion:
 * - $message_id (int): Message ID
 * - $content (string): The message content
 * - $author (string): The name of the person who left the message
 * - $timestamp (string|null): Optional timestamp string
 * - $avatar (string|null): Optional user avatar path
 * - $likes (int): Number of likes
 * - $dislikes (int): Number of dislikes
 * - $current_user_id (int|null): Current logged in user ID
 * - $author_id (int): Message author ID
 */

$message_id = $message_id ?? 0;
$content = $content ?? '內容錯誤';
$author = $author ?? '匿名訪客';
$timestamp = $timestamp ?? null;
$avatar = $avatar ?? null;
$likes = $likes ?? 0;
$dislikes = $dislikes ?? 0;
$current_user_id = $current_user_id ?? null;
$author_id = $author_id ?? null;
$style_attribute = $style_attribute ?? '';

// 檢查是否可以編輯（只有作者本人可以編輯）
$can_edit = ($current_user_id && $current_user_id == $author_id);

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
        <div class="message-meta">
            <span class="message-timestamp"><?php echo htmlspecialchars($timestamp ?? ''); ?></span>
            <?php if ($can_edit): ?>
                <button class="edit-message-btn" onclick="editMessage(<?php echo $message_id; ?>)" title="編輯留言">
                    <span>✏️</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="message-content" id="message-content-<?php echo $message_id; ?>"><?php echo nl2br(htmlspecialchars($content)); ?></div>

    <!-- 編輯表單（隱藏） -->
    <div class="edit-form" id="edit-form-<?php echo $message_id; ?>" style="display: none;">
        <textarea class="edit-textarea" id="edit-textarea-<?php echo $message_id; ?>" rows="4"><?php echo htmlspecialchars($content); ?></textarea>
        <div class="edit-actions">
            <button class="submit-button" onclick="saveEdit(<?php echo $message_id; ?>)">儲存</button>
            <button class="link-button" onclick="cancelEdit(<?php echo $message_id; ?>)">取消</button>
        </div>
    </div>

    <!-- 隱藏的原始內容，供 JavaScript 使用 -->
    <script type="text/plain" id="original-content-<?php echo $message_id; ?>"><?php echo htmlspecialchars($content); ?></script>

    <!-- 按讚倒讚功能 -->
    <div class="message-actions">
        <div class="reaction-buttons">
            <button class="reaction-btn like-btn <?php echo ($user_reaction === 'like') ? 'active' : ''; ?>"
                id="like-btn-<?php echo $message_id; ?>"
                onclick="reactToMessage(<?php echo $message_id; ?>, 'like')"
                title="按讚">
                <span class="reaction-icon">👍</span>
                <span class="reaction-count" id="like-count-<?php echo $message_id; ?>"><?php echo $likes; ?></span>
            </button>
            <button class="reaction-btn dislike-btn <?php echo ($user_reaction === 'dislike') ? 'active' : ''; ?>"
                id="dislike-btn-<?php echo $message_id; ?>"
                onclick="reactToMessage(<?php echo $message_id; ?>, 'dislike')"
                title="倒讚">
                <span class="reaction-icon">👎</span>
                <span class="reaction-count" id="dislike-count-<?php echo $message_id; ?>"><?php echo $dislikes; ?></span>
            </button>
        </div>
    </div>
</div>