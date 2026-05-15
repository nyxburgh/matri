<!-- matches.php -->
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Discover</div>
    <h1 style="font-size:1.35rem">Daily Matches</h1>
    <p style="color:var(--muted);font-size:.85rem;margin-top:.2rem"><?= number_format($total) ?> profiles found for you</p>
  </div>
  <a href="<?= APP_URL ?>/search/advanced" class="btn-outline-green d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.4rem 1rem">
    <i class="bi bi-sliders"></i> Advanced Filter
  </a>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-hearts" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No matches yet</h4>
  <p style="color:var(--muted)">Complete your profile to start seeing matches.</p>
  <a href="<?= APP_URL ?>/profile/edit" class="btn-pink mt-2" style="display:inline-block;padding:.6rem 1.5rem">Complete Profile</a>
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
        <?php if (!empty($p['is_highlighted'])): ?>
          <span class="plan-badge"><i class="bi bi-star-fill me-1"></i>Featured</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="profile-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="profile-meta"><?= $p['age'] ?? '' ?><?= !empty($p['city']) ? ' · '.htmlspecialchars($p['city']) : '' ?></div>
        <div class="profile-meta mt-1"><?= htmlspecialchars($p['religion'] ?? '') ?><?= !empty($p['education']) ? ' · '.htmlspecialchars($p['education']) : '' ?></div>
        <?php if (!empty($p['marital_status']) && $p['marital_status'] !== 'never_married'): ?>
          <span class="badge-gold mt-1" style="font-size:.68rem"><?= ucwords(str_replace('_',' ',$p['marital_status'])) ?></span>
        <?php endif; ?>
      </div>
      <div class="action-bar">
        <button class="btn-pink flex-grow-1" style="font-size:.76rem;padding:.32rem .4rem" onclick="sendInterest(<?= $p['id'] ?>,this)">
          <i class="bi bi-heart"></i> Interest
        </button>
        <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($p['profile_id']) ?>" class="btn-outline-pink" style="font-size:.76rem;padding:.32rem .6rem">View</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page > 1): ?>
  <li class="page-item"><a class="page-link" href="?page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li>
  <?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>">
    <a class="page-link" href="?page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a>
  </li>
  <?php endfor; ?>
  <?php if ($current_page < $last_page): ?>
  <li class="page-item"><a class="page-link" href="?page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li>
  <?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function sendInterest(uid, btn) {
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/send', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}&receiver_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) btn.outerHTML='<span class="badge-pink flex-grow-1 text-center" style="padding:.35rem;font-size:.74rem"><i class="bi bi-check me-1"></i>Sent</span>';
    else { btn.disabled=false; alert(d.message); }
  });
}
</script>
