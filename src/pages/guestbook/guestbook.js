// 留言板頁面 JavaScript 功能

// 處理按讚倒讚功能
async function reactToMessage(messageId, reactionType) {
    try {
        const response = await fetch('/reaction_process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message_id: messageId,
                reaction_type: reactionType
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // 更新按讚倒讚計數
            document.getElementById('like-count-' + messageId).textContent = data.likes;
            document.getElementById('dislike-count-' + messageId).textContent = data.dislikes;
            
            // 更新按鈕狀態
            const likeBtn = document.getElementById('like-btn-' + messageId);
            const dislikeBtn = document.getElementById('dislike-btn-' + messageId);
            
            // 移除所有 active 狀態
            likeBtn.classList.remove('active');
            dislikeBtn.classList.remove('active');
            
            // 根據用戶當前反應設置 active 狀態
            if (data.user_reaction === 'like') {
                likeBtn.classList.add('active');
            } else if (data.user_reaction === 'dislike') {
                dislikeBtn.classList.add('active');
            }
        } else {
            console.error('Reaction failed:', data.error);
            alert('操作失敗：' + data.error);
        }
    } catch (error) {
        console.error('Network error:', error);
        alert('網路錯誤，請稍後再試');
    }
}

// 處理編輯留言功能
function editMessage(messageId) {
    // 隱藏留言內容，顯示編輯表單
    document.getElementById('message-content-' + messageId).style.display = 'none';
    document.getElementById('edit-form-' + messageId).style.display = 'block';
    
    // 聚焦到文字輸入框
    document.getElementById('edit-textarea-' + messageId).focus();
}

// 儲存編輯的留言
async function saveEdit(messageId) {
    const textarea = document.getElementById('edit-textarea-' + messageId);
    const newContent = textarea.value.trim();
    
    if (!newContent) {
        alert('留言內容不能為空');
        return;
    }
    
    if (newContent.length > 1000) {
        alert('留言內容過長（最多 1000 個字元）');
        return;
    }
    
    try {
        const response = await fetch('/edit_message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message_id: messageId,
                content: newContent
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // 更新留言內容
            document.getElementById('message-content-' + messageId).innerHTML = data.content.replace(/\n/g, '<br>');
            
            // 隱藏編輯表單，顯示留言內容
            document.getElementById('edit-form-' + messageId).style.display = 'none';
            document.getElementById('message-content-' + messageId).style.display = 'block';
            
            // 可選：顯示成功訊息
            // alert('留言更新成功！');
        } else {
            console.error('Edit failed:', data.error);
            alert('編輯失敗：' + data.error);
        }
    } catch (error) {
        console.error('Network error:', error);
        alert('網路錯誤，請稍後再試');
    }
}

// 取消編輯
function cancelEdit(messageId) {
    // 重置文字輸入框內容 - 使用隱藏的原始內容
    const originalContent = document.getElementById('original-content-' + messageId).textContent;
    document.getElementById('edit-textarea-' + messageId).value = originalContent;
    
    // 隱藏編輯表單，顯示留言內容
    document.getElementById('edit-form-' + messageId).style.display = 'none';
    document.getElementById('message-content-' + messageId).style.display = 'block';
} 