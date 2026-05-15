<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Saved</div>
    <h1 style="font-size:1.35rem">My Shortlist</h1>
    <p style="color:var(--muted);font-size:.85rem;margin-top:.2rem"><?= number_format($total) ?> profiles saved</p>
  </div>
  <a href="<?= APP_URL ?>/search" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.4rem 1rem">
    <i class="bi bi-search"></i> Find More Matches
  </a>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-bookmark-heart" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">Your shortlist is empty</h4>
  <p style="color:var(--muted)">Browse profiles and click the bookmark icon to save them here.</p>
  <a href="<?= APP_URL ?>/search" class="btn-pink mt-2" style="display:inline-block;padding:.6rem 1.5rem">Browse Profiles</a>
</div>
<?php else: ?>
<div class="row g-3 mb-4">
  <?php foreach ($data as $p): ?>
  <div class="col-6 col-md-4 col-lg-3">
    <div class="mat-card profile-card h-100">
      <div class="photo-wrap">
        <?php if (!empty($p['photo'])): ?>
          <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
          <div class="no-photo"><i class="bi bi-person-circle"></i></div>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="profile-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="profile-meta"><?= $p['age'] ?? '' ?><?= !empty($p['city']) ? ' · '.htmlspecialchars($p['city']) : '' ?></div>
        <div class="profile-meta mt-1"><?= htmlspecialchars($p['religion'] ?? '') ?><?= !empty($p['education']) ? ' · '.htmlspecialchars($p['education']) : '' ?></div>
        <div style="font-size:.72rem;color:var(--muted);margin-top:.4rem"><i class="bi bi-bookmark-fill me-1" style="color:var(--pink)"></i>Saved <?= date('d M', strtotime($p['shortlisted_at'])) ?></div>
      </div>
      <div class="action-bar">
        <button onclick="removeShortlist(<?= $p['id'] ?>,this)" class="btn-outline-pink" style="font-size:.76rem;padding:.32rem .6rem" title="Remove">
          <i class="bi bi-bookmark-x"></i>
        </button>
        <button class="btn-pink flex-grow-1" style="font-size:.76rem;padding:.32rem .4rem" onclick="sendInterest(<?= $p['id'] ?>,this)">
          <i class="bi bi-heart"></i> Interest
        </button>
        <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($p['profile_id']) ?>" class="btn-outline-pink" style="font-size:.76rem;padding:.32rem .6rem">View</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page < $last_page): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function removeShortlist(uid, btn) {
  fetch('<?= APP_URL ?>/profile/shortlist', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}&target_id=${uid}`
  }).then(r=>r.json()).then(d=>{ if(d.action==='removed') btn.closest('.col-6, .col-md-4, .col-lg-3')?.remove(); });
}
function sendInterest(uid, btn) {
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/send', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}&receiver_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    if(d.success) btn.outerHTML='<span class="badge-pink flex-grow-1 text-center" style="padding:.35rem;font-size:.74rem"><i class="bi bi-check me-1"></i>Sent</span>';
    else { btn.disabled=false; alert(d.message); }
  });
}
</script>
