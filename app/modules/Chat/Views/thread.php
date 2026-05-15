<?php $userId = Session::get('user_id'); ?>

<style>
.chat-container{height:calc(100vh - 180px);display:flex;flex-direction:column;background:#fff;border-radius:var(--radius-lg);border:1px solid var(--border);overflow:hidden}
.chat-header{padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.75rem;background:#fff;flex-shrink:0}
.chat-body{flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:.5rem;background:#F9F3FB}
.chat-body::-webkit-scrollbar{width:4px}
.chat-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.chat-footer{padding:.75rem 1rem;border-top:1px solid var(--border);background:#fff;flex-shrink:0}
.bubble{max-width:72%;padding:.65rem .9rem;border-radius:18px;font-size:.9rem;line-height:1.45;word-break:break-word}
.bubble-sent{background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.bubble-recv{background:#fff;color:var(--text);align-self:flex-start;border:1px solid var(--border);border-bottom-left-radius:4px}
.msg-time{font-size:.68rem;opacity:.65;margin-top:.2rem}
.msg-sent-wrap{display:flex;flex-direction:column;align-items:flex-end}
.msg-recv-wrap{display:flex;flex-direction:column;align-items:flex-start}
.typing{display:none;align-self:flex-start;padding:.4rem .8rem;background:#fff;border:1px solid var(--border);border-radius:18px;font-size:.8rem;color:var(--muted)}
.partner-avatar{width:42px;height:42px;border-radius:50%;object-fit:cover;background:var(--pink-pale);display:flex;align-items:center;justify-content:center;color:var(--pink);font-size:1.2rem;flex-shrink:0;overflow:hidden}
</style>

<div class="chat-container">
  <!-- Chat Header -->
  <div class="chat-header">
    <a href="<?= APP_URL ?>/chat" style="color:var(--muted)"><i class="bi bi-arrow-left-circle fs-5"></i></a>
    <div class="partner-avatar">
      <?php if (!empty($partner['photo'])): ?>
      <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($partner['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
      <?php else: ?>
      <i class="bi bi-person-circle"></i>
      <?php endif; ?>
    </div>
    <div class="flex-grow-1">
      <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($partner['profile_id']) ?>" style="font-weight:700;font-size:.95rem;color:var(--dark);text-decoration:none"><?= htmlspecialchars($partner['name']) ?></a>
      <div id="onlineStatus" style="font-size:.75rem;color:var(--muted)">
        <?php
        $lastAct = $partner['last_active'] ?? null;
        $online  = $lastAct && strtotime($lastAct) > time() - 300;
        echo $online
          ? '<span style="color:var(--green)"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Online</span>'
          : 'Last seen ' . ($lastAct ? date('d M, g:i a', strtotime($lastAct)) : 'a while ago');
        ?>
      </div>
    </div>
    <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($partner['profile_id']) ?>" class="btn-outline-pink" style="font-size:.78rem;padding:.3rem .8rem">Profile</a>
  </div>

  <!-- Chat Body -->
  <div class="chat-body" id="chatBody">
    <?php if (empty($messages)): ?>
    <div class="text-center my-4">
      <i class="bi bi-chat-heart" style="font-size:2.5rem;color:var(--pink-pale)"></i>
      <p style="color:var(--muted);margin-top:.5rem;font-size:.88rem">
        You and <?= htmlspecialchars($partner['name']) ?> are now connected!<br>Say hello 👋
      </p>
    </div>
    <?php endif; ?>

    <?php foreach ($messages as $msg): ?>
    <?php $isSent = $msg['sender_id'] == $userId; ?>
    <div class="<?= $isSent ? 'msg-sent-wrap' : 'msg-recv-wrap' ?>">
      <div class="bubble <?= $isSent ? 'bubble-sent' : 'bubble-recv' ?>">
        <?= nl2br(htmlspecialchars($msg['message'])) ?>
      </div>
      <div class="msg-time <?= $isSent ? 'text-end pe-1' : 'ps-1' ?>">
        <?= date('g:i a', strtotime($msg['created_at'])) ?>
        <?php if ($isSent && $msg['is_read']): ?>
        <i class="bi bi-check2-all" style="color:rgba(255,255,255,.7)"></i>
        <?php elseif ($isSent): ?>
        <i class="bi bi-check2"></i>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="typing" id="typingIndicator">
      <i class="bi bi-three-dots me-1"></i> <?= htmlspecialchars($partner['name']) ?> is typing...
    </div>
  </div>

  <!-- Chat Footer -->
  <div class="chat-footer">
    <div class="d-flex gap-2 align-items-end">
      <div class="flex-grow-1 position-relative">
        <textarea id="msgInput" rows="1" placeholder="Type a message..." style="border-radius:20px;border:1.5px solid var(--border);padding:.6rem 1rem;width:100%;resize:none;font-size:.9rem;max-height:120px;overflow-y:auto;font-family:'DM Sans',sans-serif" oninput="autoResize(this)"></textarea>
      </div>
      <button onclick="sendMsg()" class="btn-pink" style="width:44px;height:44px;border-radius:50%;padding:0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="bi bi-send-fill"></i>
      </button>
    </div>
    <?php if (!$plan['chat_limit']): ?>
    <div style="font-size:.75rem;color:var(--muted);text-align:center;margin-top:.4rem">
      <a href="<?= APP_URL ?>/subscription" style="color:var(--pink)">Upgrade to Silver</a> to send unlimited messages.
    </div>
    <?php endif; ?>
  </div>
</div>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<input type="hidden" id="partnerId" value="<?= $partner['id'] ?>">
<input type="hidden" id="lastMsgId" value="<?= !empty($messages) ? end($messages)['id'] : 0 ?>">

<script>
const chatBody  = document.getElementById('chatBody');
const msgInput  = document.getElementById('msgInput');
const csrfToken = document.getElementById('csrfToken').value;
const partnerId = document.getElementById('partnerId').value;

// Auto-scroll to bottom
function scrollBottom() { chatBody.scrollTop = chatBody.scrollHeight; }
scrollBottom();

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// Send message
function sendMsg() {
  const msg = msgInput.value.trim();
  if (!msg) return;

  msgInput.value = '';
  msgInput.style.height = 'auto';

  // Optimistic UI
  const id = 'temp-' + Date.now();
  appendMessage({id, message: msg, sender_id: '<?= $userId ?>', created_at: new Date().toISOString(), is_read: 0}, true);
  scrollBottom();

  fetch('<?= APP_URL ?>/chat/send', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrfToken}&receiver_id=${partnerId}&message=${encodeURIComponent(msg)}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) {
      document.getElementById(id).id = 'msg-' + d.message_id;
      document.getElementById('lastMsgId').value = d.message_id;
    }
  });
}

function appendMessage(msg, isSent) {
  const t = new Date(msg.created_at);
  const timeStr = t.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
  const div = document.createElement('div');
  div.className = isSent ? 'msg-sent-wrap' : 'msg-recv-wrap';
  div.id = msg.id;
  div.innerHTML = `
    <div class="bubble ${isSent?'bubble-sent':'bubble-recv'}">
      ${msg.message.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>')}
    </div>
    <div class="msg-time ${isSent?'text-end pe-1':'ps-1'}">${timeStr}${isSent?'<i class="bi bi-check2 ms-1"></i>':''}</div>`;
  chatBody.insertBefore(div, document.getElementById('typingIndicator'));
}

// Poll for new messages every 3s
setInterval(() => {
  const lastId = document.getElementById('lastMsgId').value;
  fetch(`<?= APP_URL ?>/chat/messages/${partnerId}?last_id=${lastId}`)
    .then(r=>r.json())
    .then(d=>{
      if (d.messages && d.messages.length) {
        d.messages.forEach(m => {
          if (!document.getElementById('msg-'+m.id)) {
            appendMessage(m, m.sender_id == <?= $userId ?>);
            document.getElementById('lastMsgId').value = m.id;
          }
        });
        scrollBottom();
      }
    }).catch(()=>{});
}, 3000);

// Enter to send (Shift+Enter = new line)
msgInput.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
});
</script>
