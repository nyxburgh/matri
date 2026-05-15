<div class="page-header">
  <div class="section-tag">Activity</div>
  <h1 style="font-size:1.35rem">My Interests</h1>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <?php
  $iStats = [
    ['label'=>'Received (Pending)', 'val'=>$stats['received_pending'],  'color'=>'#F57F17','bg'=>'#FFF8E1','icon'=>'bi-heart-arrow'],
    ['label'=>'Received (Accepted)','val'=>$stats['received_accepted'], 'color'=>'var(--green)','bg'=>'var(--green-pale)','icon'=>'bi-heart-fill'],
    ['label'=>'Sent (Pending)',      'val'=>$stats['sent_pending'],      'color'=>'var(--pink)','bg'=>'var(--pink-pale)','icon'=>'bi-send-heart'],
    ['label'=>'Sent (Accepted)',     'val'=>$stats['sent_accepted'],     'color'=>'#7C3AED','bg'=>'#F3E8FF','icon'=>'bi-check-circle-fill'],
  ];
  foreach ($iStats as $s): ?>
  <div class="col-6 col-md-3">
    <div class="stat-pill" style="background:<?= $s['bg'] ?>">
      <i class="<?= $s['icon'] ?>" style="color:<?= $s['color'] ?>;font-size:1.2rem"></i>
      <div class="stat-num" style="background:none;-webkit-text-fill-color:<?= $s['color'] ?>;color:<?= $s['color'] ?>"><?= $s['val'] ?></div>
      <div class="stat-label"><?= $s['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Tabs -->
<ul class="nav mb-4" style="border-bottom:2px solid var(--border)">
  <li class="nav-item">
    <a class="nav-link <?= $tab==='received'?'active':'' ?> fw-600" href="?tab=received"
       style="<?= $tab==='received'?'color:var(--pink);border-bottom:2px solid var(--pink);margin-bottom:-2px':'color:var(--muted)' ?>">
      <i class="bi bi-heart-arrow me-1"></i> Received
      <?php if ($stats['received_pending']>0): ?><span class="badge rounded-pill" style="background:var(--pink);font-size:.65rem"><?= $stats['received_pending'] ?></span><?php endif; ?>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $tab==='sent'?'active':'' ?> fw-600" href="?tab=sent"
       style="<?= $tab==='sent'?'color:var(--pink);border-bottom:2px solid var(--pink);margin-bottom:-2px':'color:var(--muted)' ?>">
      <i class="bi bi-send-heart me-1"></i> Sent
    </a>
  </li>
</ul>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">

<?php if ($tab === 'received'): ?>
<!-- Received Interests -->
<?php if (empty($received['data'])): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-heart" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No interests received yet</h4>
  <p style="color:var(--muted)">Complete your profile and upload photos to attract more matches.</p>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($received['data'] as $int): ?>
  <div class="col-12 col-md-6" id="int-<?= $int['id'] ?>">
    <div class="mat-card p-3 d-flex gap-3 align-items-start">
      <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>">
        <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--pink-pale)">
          <?php if (!empty($int['photo'])): ?>
          <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($int['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?>
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
          <?php endif; ?>
        </div>
      </a>
      <div class="flex-grow-1">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" style="font-weight:700;font-size:.95rem;color:var(--dark)"><?= htmlspecialchars($int['name']) ?></a>
            <div style="font-size:.78rem;color:var(--muted)"><?= $int['age']??'' ?> <?= !empty($int['city'])?'· '.htmlspecialchars($int['city']):'' ?> <?= !empty($int['religion'])?'· '.htmlspecialchars($int['religion']):'' ?></div>
            <div style="font-size:.78rem;color:var(--muted)"><?= htmlspecialchars($int['education']??'') ?> <?= !empty($int['occupation'])?'· '.htmlspecialchars($int['occupation']):'' ?></div>
          </div>
          <span class="badge text-capitalize" style="font-size:.72rem;background:<?= ['pending'=>'#FEF3C7','accepted'=>'#D1FAE5','rejected'=>'#FEE2E2'][$int['status']]??'#f3f4f6' ?>;color:<?= ['pending'=>'#92400E','accepted'=>'#065F46','rejected'=>'#991B1B'][$int['status']]??'#6B7280' ?>">
            <?= ucfirst($int['status']) ?>
          </span>
        </div>
        <?php if (!empty($int['message'])): ?>
        <p style="font-size:.8rem;color:var(--text);margin-top:.5rem;background:var(--pink-pale);border-radius:8px;padding:.4rem .6rem">
          "<?= htmlspecialchars($int['message']) ?>"
        </p>
        <?php endif; ?>
        <?php if ($int['status'] === 'pending'): ?>
        <div class="d-flex gap-2 mt-2">
          <button class="btn-green" style="font-size:.82rem;padding:.35rem 1rem" onclick="respond(<?= $int['id'] ?>,'accepted')">
            <i class="bi bi-check-circle me-1"></i> Accept
          </button>
          <button class="btn-outline-pink" style="font-size:.82rem;padding:.35rem 1rem" onclick="respond(<?= $int['id'] ?>,'rejected')">
            <i class="bi bi-x-circle me-1"></i> Decline
          </button>
        </div>
        <?php elseif ($int['status'] === 'accepted'): ?>
        <a href="<?= APP_URL ?>/chat/<?= $int['uid'] ?>" class="btn-green d-inline-flex align-items-center gap-1 mt-2" style="font-size:.82rem;padding:.35rem 1rem">
          <i class="bi bi-chat-dots"></i> Chat Now
        </a>
        <?php endif; ?>
        <div style="font-size:.72rem;color:var(--muted);margin-top:.3rem"><?= date('d M Y, g:i a', strtotime($int['sent_at'])) ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php else: ?>
<!-- Sent Interests -->
<?php if (empty($sent['data'])): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-send" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No interests sent yet</h4>
  <p style="color:var(--muted)"><a href="<?= APP_URL ?>/search" style="color:var(--pink)">Search profiles</a> and send your first interest!</p>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($sent['data'] as $int): ?>
  <div class="col-12 col-md-6">
    <div class="mat-card p-3 d-flex gap-3">
      <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>">
        <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--green-pale)">
          <?php if (!empty($int['photo'])): ?>
          <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($int['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?>
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
          <?php endif; ?>
        </div>
      </a>
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between">
          <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" style="font-weight:700;font-size:.95rem;color:var(--dark)"><?= htmlspecialchars($int['name']) ?></a>
          <span class="badge" style="font-size:.72rem;background:<?= ['pending'=>'#FEF3C7','accepted'=>'#D1FAE5','rejected'=>'#FEE2E2','cancelled'=>'#F3F4F6'][$int['status']]??'#f3f4f6' ?>;color:<?= ['pending'=>'#92400E','accepted'=>'#065F46','rejected'=>'#991B1B','cancelled'=>'#6B7280'][$int['status']]??'#6B7280' ?>">
            <?= ucfirst($int['status']) ?>
          </span>
        </div>
        <div style="font-size:.78rem;color:var(--muted)"><?= $int['age']??'' ?> <?= !empty($int['city'])?'· '.htmlspecialchars($int['city']):'' ?></div>
        <?php if ($int['status'] === 'accepted'): ?>
        <a href="<?= APP_URL ?>/chat/<?= $int['uid'] ?>" class="btn-green d-inline-flex align-items-center gap-1 mt-2" style="font-size:.8rem;padding:.3rem .9rem">
          <i class="bi bi-chat-dots"></i> Chat Now
        </a>
        <?php elseif ($int['status'] === 'pending'): ?>
        <button onclick="cancelInterest(<?= $int['id'] ?>,this)" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:.78rem;border-radius:50px">
          Cancel
        </button>
        <?php endif; ?>
        <div style="font-size:.72rem;color:var(--muted);margin-top:.3rem"><?= date('d M Y', strtotime($int['sent_at'])) ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
function respond(id, action) {
  if (!confirm(action==='accepted'?'Accept this interest?':'Decline this interest?')) return;
  fetch('<?= APP_URL ?>/interest/respond', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&interest_id=${id}&action=${action}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) location.reload();
    else alert(d.message);
  });
}

function cancelInterest(id, btn) {
  if (!confirm('Cancel this interest?')) return;
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/cancel', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&interest_id=${id}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) btn.closest('.mat-card').style.opacity='0.5';
    else { btn.disabled=false; alert(d.message); }
  });
}
</script>
