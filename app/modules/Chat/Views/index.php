<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Messages</div>
    <h1 style="font-size:1.35rem">Chat</h1>
  </div>
  <?php if (($plan['plan_code'] ?? 'FREE') === 'FREE'): ?>
  <a href="<?= APP_URL ?>/subscription" class="btn-green d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.4rem 1rem">
    <i class="bi bi-gem"></i> Upgrade to Chat
  </a>
  <?php endif; ?>
</div>

<?php if (($plan['plan_code'] ?? 'FREE') === 'FREE'): ?>
<div class="mat-card p-5 text-center mb-4" style="background:linear-gradient(135deg,var(--pink-pale),var(--green-pale))">
  <i class="bi bi-chat-lock" style="font-size:3rem;color:var(--pink)"></i>
  <h4 class="mt-3" style="color:var(--dark)">Chat is a Premium Feature</h4>
  <p style="color:var(--muted);max-width:420px;margin:.5rem auto 1.2rem">Upgrade to Silver plan or above to send unlimited messages to your accepted matches.</p>
  <a href="<?= APP_URL ?>/subscription" class="btn-pink" style="padding:.65rem 2rem">Upgrade Now — from ₹499</a>
</div>
<?php endif; ?>

<?php if (empty($threads)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-chat-dots" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No conversations yet</h4>
  <p style="color:var(--muted)">Accept interests or get yours accepted to start chatting!</p>
  <a href="<?= APP_URL ?>/interests" class="btn-outline-pink mt-2" style="display:inline-block;padding:.5rem 1.5rem">View Interests</a>
</div>
<?php else: ?>
<div class="row g-0" style="background:#fff;border-radius:var(--radius-lg);border:1px solid var(--border);overflow:hidden;min-height:500px">

  <!-- Thread List -->
  <div class="col-12 col-md-4" style="border-right:1px solid var(--border)">
    <div class="p-3 border-bottom" style="border-color:var(--border)!important">
      <input type="text" placeholder="Search conversations..." class="form-control form-control-sm" style="border-radius:50px;border-color:var(--border)">
    </div>
    <div style="overflow-y:auto;max-height:520px">
      <?php foreach ($threads as $t): ?>
      <a href="<?= APP_URL ?>/chat/<?= $t['partner_id'] ?>"
         class="d-flex align-items-center gap-3 p-3 text-decoration-none"
         style="border-bottom:1px solid var(--border);transition:background .15s;<?= (strpos($_SERVER['REQUEST_URI'], '/chat/'.$t['partner_id']) !== false) ? 'background:var(--pink-pale)' : '' ?>"
         onmouseover="this.style.background='var(--pink-pale)'" onmouseout="this.style.background=''">
        <div style="position:relative;flex-shrink:0">
          <div style="width:48px;height:48px;border-radius:50%;overflow:hidden;background:var(--pink-pale)">
            <?php if (!empty($t['partner_photo'])): ?>
              <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($t['partner_photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
            <?php endif; ?>
          </div>
          <?php if (!empty($t['partner_last_active']) && strtotime($t['partner_last_active']) > time()-300): ?>
          <div style="position:absolute;bottom:1px;right:1px;width:11px;height:11px;border-radius:50%;background:var(--green);border:2px solid #fff"></div>
          <?php endif; ?>
        </div>
        <div class="flex-grow-1 overflow-hidden">
          <div class="d-flex justify-content-between align-items-baseline">
            <span style="font-weight:700;font-size:.9rem;color:var(--dark)"><?= htmlspecialchars($t['partner_name']) ?></span>
            <span style="font-size:.68rem;color:var(--muted);flex-shrink:0;margin-left:.5rem"><?= $t['last_msg_time'] ? date('d M', strtotime($t['last_msg_time'])) : '' ?></span>
          </div>
          <div style="font-size:.78rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?= htmlspecialchars(mb_substr($t['last_message'] ?? 'Start a conversation', 0, 50)) ?>
          </div>
        </div>
        <?php if ($t['unread_count'] > 0): ?>
        <div style="background:var(--pink);color:#fff;font-size:.68rem;font-weight:700;min-width:18px;height:18px;border-radius:9px;display:flex;align-items:center;justify-content:center;padding:0 4px;flex-shrink:0"><?= $t['unread_count'] ?></div>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Placeholder when no thread selected (desktop) -->
  <div class="col-md-8 d-none d-md-flex align-items-center justify-content-center" style="background:var(--pink-pale)">
    <div class="text-center">
      <i class="bi bi-chat-heart" style="font-size:4rem;color:var(--pink);opacity:.4"></i>
      <h5 style="color:var(--muted);margin-top:1rem;font-size:1rem">Select a conversation to start chatting</h5>
    </div>
  </div>
</div>
<?php endif; ?>
