<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Inbox</div>
    <h1 style="font-size:1.35rem">Notifications</h1>
  </div>
  <?php if ($total > 0): ?>
  <button onclick="markAllRead()" class="btn-outline-green d-inline-flex align-items-center gap-1" style="font-size:.82rem;padding:.38rem .9rem">
    <i class="bi bi-check2-all"></i> Mark All Read
  </button>
  <?php endif; ?>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-bell-slash" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No notifications yet</h4>
  <p style="color:var(--muted)">We'll notify you when someone sends an interest, accepts yours, or messages you.</p>
</div>
<?php else: ?>
<div class="d-flex flex-column gap-2 mb-4">
  <?php
  $icons = [
    'interest_received'  => ['icon'=>'bi-heart-arrow','color'=>'var(--pink)','bg'=>'var(--pink-pale)'],
    'interest_accepted'  => ['icon'=>'bi-heart-fill','color'=>'var(--green)','bg'=>'var(--green-pale)'],
    'interest_rejected'  => ['icon'=>'bi-heart-break','color'=>'#94A3B8','bg'=>'#F1F5F9'],
    'new_message'        => ['icon'=>'bi-chat-dots-fill','color'=>'#0EA5E9','bg'=>'#E0F2FE'],
    'profile_viewed'     => ['icon'=>'bi-eye-fill','color'=>'#7C3AED','bg'=>'#F3E8FF'],
    'subscription_expiry'=> ['icon'=>'bi-gem','color'=>'#F57F17','bg'=>'#FFF8E1'],
    'admin_message'      => ['icon'=>'bi-megaphone-fill','color'=>'var(--dark)','bg'=>'#F3F4F6'],
    'profile_approved'   => ['icon'=>'bi-shield-check','color'=>'var(--green)','bg'=>'var(--green-pale)'],
    'profile_rejected'   => ['icon'=>'bi-shield-x','color'=>'#EF4444','bg'=>'#FEE2E2'],
  ];
  ?>
  <?php foreach ($data as $n):
    $ic = $icons[$n['type']] ?? ['icon'=>'bi-bell','color'=>'var(--pink)','bg'=>'var(--pink-pale)'];
  ?>
  <div class="mat-card p-3 d-flex align-items-start gap-3" style="<?= !$n['is_read'] ? 'border-left:3px solid var(--pink)' : 'opacity:.82' ?>">
    <div style="width:42px;height:42px;border-radius:50%;background:<?= $ic['bg'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="<?= $ic['icon'] ?>" style="color:<?= $ic['color'] ?>;font-size:1.1rem"></i>
    </div>
    <div class="flex-grow-1">
      <div style="font-weight:<?= !$n['is_read']?'700':'600' ?>;font-size:.9rem;color:var(--dark)"><?= htmlspecialchars($n['title']) ?></div>
      <?php if (!empty($n['body'])): ?>
      <div style="font-size:.82rem;color:var(--muted);margin-top:.15rem"><?= htmlspecialchars($n['body']) ?></div>
      <?php endif; ?>
      <div style="font-size:.72rem;color:var(--muted);margin-top:.3rem"><?= date('d M Y, g:i a', strtotime($n['created_at'])) ?></div>
    </div>
    <?php if (!$n['is_read']): ?>
    <div style="width:8px;height:8px;border-radius:50%;background:var(--pink);flex-shrink:0;margin-top:.4rem"></div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page>1): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page<$last_page): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function markAllRead() {
  fetch('<?= APP_URL ?>/notifications/read-all', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}`
  }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
