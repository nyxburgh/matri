<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Outgoing</div>
    <h1 style="font-size:1.35rem">Interests Sent</h1>
    <p style="color:var(--muted);font-size:.85rem;margin-top:.2rem"><?= number_format($total) ?> interests sent</p>
  </div>
  <a href="<?= APP_URL ?>/interests" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.38rem .9rem">
    <i class="bi bi-arrow-left"></i> All Interests
  </a>
</div>

<!-- Status Filter -->
<div class="d-flex gap-2 mb-4 flex-wrap">
  <?php
  $statuses = [''  =>'All',  'pending'=>'Pending',  'accepted'=>'Accepted',  'rejected'=>'Rejected',  'cancelled'=>'Cancelled'];
  foreach ($statuses as $val=>$label): ?>
  <a href="?status=<?= $val ?>" class="btn btn-sm <?= $status===$val?'btn-pink':'btn-outline-secondary' ?>" style="border-radius:50px;font-size:.8rem;padding:.3rem .9rem<?= $status===$val?';background:linear-gradient(135deg,var(--pink),var(--pink-light));border:none;color:#fff':'' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-send" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No interests sent yet</h4>
  <p style="color:var(--muted)"><a href="<?= APP_URL ?>/search" style="color:var(--pink)">Browse profiles</a> and start sending interests!</p>
</div>
<?php else: ?>
<div class="row g-3 mb-4">
  <?php foreach ($data as $int): ?>
  <div class="col-12 col-md-6">
    <div class="mat-card p-3 d-flex align-items-center gap-3">
      <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>">
        <div style="width:60px;height:60px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--green-pale)">
          <?php if (!empty($int['photo'])): ?>
            <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($int['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
          <?php endif; ?>
        </div>
      </a>
      <div class="flex-grow-1 overflow-hidden">
        <div class="d-flex justify-content-between align-items-start">
          <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" style="font-weight:700;font-size:.92rem;color:var(--dark)"><?= htmlspecialchars($int['name']) ?></a>
          <span style="font-size:.72rem;font-weight:700;padding:.22rem .6rem;border-radius:50px;flex-shrink:0;background:<?= ['pending'=>'#FEF3C7','accepted'=>'#D1FAE5','rejected'=>'#FEE2E2','cancelled'=>'#F3F4F6'][$int['status']]??'#F3F4F6' ?>;color:<?= ['pending'=>'#92400E','accepted'=>'#065F46','rejected'=>'#991B1B','cancelled'=>'#6B7280'][$int['status']]??'#6B7280' ?>">
            <?= ucfirst($int['status']) ?>
          </span>
        </div>
        <div style="font-size:.78rem;color:var(--muted)"><?= $int['age']??'' ?><?= !empty($int['city'])?' · '.htmlspecialchars($int['city']):'' ?><?= !empty($int['religion'])?' · '.htmlspecialchars($int['religion']):'' ?></div>
        <?php if (!empty($int['message'])): ?>
        <div style="font-size:.76rem;color:var(--text);margin-top:.3rem;background:var(--pink-pale);border-radius:6px;padding:.3rem .5rem">"<?= htmlspecialchars(mb_substr($int['message'],0,80)) ?>"</div>
        <?php endif; ?>
        <div class="d-flex align-items-center gap-2 mt-2">
          <?php if ($int['status'] === 'accepted'): ?>
          <a href="<?= APP_URL ?>/chat/<?= $int['uid'] ?>" class="btn-green d-inline-flex align-items-center gap-1" style="font-size:.78rem;padding:.28rem .8rem">
            <i class="bi bi-chat-dots"></i> Chat
          </a>
          <?php elseif ($int['status'] === 'pending'): ?>
          <button onclick="cancelInterest(<?= $int['id'] ?>,this)" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.76rem;padding:.22rem .65rem">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <?php endif; ?>
          <span style="font-size:.72rem;color:var(--muted)"><?= date('d M Y', strtotime($int['sent_at'])) ?></span>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page>1): ?><li class="page-item"><a class="page-link" href="?status=<?= $status ?>&page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?status=<?= $status ?>&page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page<$last_page): ?><li class="page-item"><a class="page-link" href="?status=<?= $status ?>&page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function cancelInterest(id, btn) {
  if(!confirm('Cancel this interest?')) return;
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/cancel', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}&interest_id=${id}`
  }).then(r=>r.json()).then(d=>{
    if(d.success) btn.closest('.mat-card').style.opacity='0.5';
    else { btn.disabled=false; alert(d.message); }
  });
}
</script>
