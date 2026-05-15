<?php $oppGender = (Session::get('user_gender') === 'male') ? 'female' : 'male'; ?>

<div class="page-header">
  <div class="section-tag">Profiles</div>
  <h1 style="font-size:1.35rem">Search Matches</h1>
</div>

<!-- Filter Bar -->
<form method="GET" action="<?= APP_URL ?>/search/results" id="searchForm">
<div class="mat-card p-3 mb-4">
  <div class="row g-2 align-items-end">
    <div class="col-6 col-md-2">
      <label class="form-label">Age From</label>
      <input type="number" name="age_from" class="form-control form-control-sm" value="<?= (int)($filters['age_from']??18) ?>" min="18" max="70">
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label">Age To</label>
      <input type="number" name="age_to" class="form-control form-control-sm" value="<?= (int)($filters['age_to']??50) ?>" min="18" max="70">
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label">Religion</label>
      <input type="text" name="religion" class="form-control form-control-sm" placeholder="Any religion" value="<?= htmlspecialchars($filters['religion']??'') ?>">
    </div>
    <div class="col-12 col-md-3">
      <label class="form-label">Location</label>
      <input type="text" name="city" class="form-control form-control-sm" placeholder="City or State" value="<?= htmlspecialchars($filters['city']??'') ?>">
    </div>
    <div class="col-12 col-md-2">
      <label class="form-label">Marital Status</label>
      <select name="marital_status" class="form-select form-select-sm">
        <option value="">Any</option>
        <option value="never_married" <?= ($filters['marital_status']??'')==='never_married'?'selected':'' ?>>Never Married</option>
        <option value="divorced"      <?= ($filters['marital_status']??'')==='divorced'?'selected':'' ?>>Divorced</option>
        <option value="widowed"       <?= ($filters['marital_status']??'')==='widowed'?'selected':'' ?>>Widowed</option>
      </select>
    </div>

    <?php if ($isAdvanced): ?>
    <!-- Advanced Filters -->
    <div class="col-12"><hr class="my-1" style="border-color:var(--border)"><small class="text-muted"><i class="bi bi-gem me-1" style="color:var(--green)"></i> Advanced Filters</small></div>
    <div class="col-6 col-md-3">
      <label class="form-label">Caste</label>
      <input type="text" name="caste" class="form-control form-control-sm" placeholder="e.g. Iyer" value="<?= htmlspecialchars($filters['caste']??'') ?>">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Education</label>
      <input type="text" name="education" class="form-control form-control-sm" placeholder="e.g. B.E." value="<?= htmlspecialchars($filters['education']??'') ?>">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Diet</label>
      <select name="diet" class="form-select form-select-sm">
        <option value="">Any</option>
        <option value="vegetarian" <?= ($filters['diet']??'')==='vegetarian'?'selected':'' ?>>Vegetarian</option>
        <option value="non_vegetarian" <?= ($filters['diet']??'')==='non_vegetarian'?'selected':'' ?>>Non-Vegetarian</option>
        <option value="eggetarian" <?= ($filters['diet']??'')==='eggetarian'?'selected':'' ?>>Eggetarian</option>
      </select>
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Dhosam</label>
      <select name="dhosam" class="form-select form-select-sm">
        <option value="">Any</option>
        <option value="none" <?= ($filters['dhosam']??'')==='none'?'selected':'' ?>>None</option>
        <option value="chevvai" <?= ($filters['dhosam']??'')==='chevvai'?'selected':'' ?>>Chevvai Dhosam</option>
        <option value="rahu" <?= ($filters['dhosam']??'')==='rahu'?'selected':'' ?>>Rahu Dhosam</option>
      </select>
    </div>
    <?php else: ?>
    <div class="col-12">
      <small style="color:var(--muted)">
        <i class="bi bi-lock-fill me-1"></i>
        <a href="<?= APP_URL ?>/subscription" style="color:var(--green)">Upgrade to Silver</a> for advanced filters (caste, education, income, diet, dhosam)
      </small>
    </div>
    <?php endif; ?>

    <div class="col-12 col-md-2 d-flex gap-2">
      <button type="submit" class="btn-pink flex-grow-1" style="padding:.45rem .5rem;font-size:.85rem">
        <i class="bi bi-search me-1"></i> Search
      </button>
      <a href="<?= APP_URL ?>/search/results" class="btn btn-outline-secondary btn-sm" style="border-radius:50px">Reset</a>
    </div>
  </div>
</div>
</form>

<!-- Results -->
<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-search" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No profiles found</h4>
  <p style="color:var(--muted)">Try adjusting your filters for more results.</p>
</div>
<?php else: ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span style="color:var(--muted);font-size:.88rem"><?= number_format($total) ?> profiles found</span>
  <span style="font-size:.8rem;color:var(--muted)">Page <?= $current_page ?> of <?= $last_page ?></span>
</div>

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
        <span class="plan-badge"><i class="bi bi-star-fill me-1"></i> Featured</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="profile-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="profile-meta"><?= $p['age'] ?? '' ?> <?= !empty($p['city']) ? '· '.htmlspecialchars($p['city']) : '' ?></div>
        <div class="profile-meta mt-1">
          <?= htmlspecialchars($p['religion'] ?? '') ?>
          <?= !empty($p['education']) ? '· '.htmlspecialchars($p['education']) : '' ?>
        </div>
        <?php if (!empty($p['dhosam']) && $p['dhosam'] !== 'none'): ?>
        <span class="badge-gold mt-1" style="font-size:.68rem"><?= ucfirst($p['dhosam']) ?> Dhosam</span>
        <?php endif; ?>
      </div>
      <div class="action-bar">
        <?php if (empty($p['interest_status'])): ?>
        <button class="btn-pink flex-grow-1" style="font-size:.76rem;padding:.3rem .4rem" onclick="sendInterest(<?= $p['id'] ?>,this)">
          <i class="bi bi-heart"></i> Interest
        </button>
        <?php elseif ($p['interest_status'] === 'accepted'): ?>
        <a href="<?= APP_URL ?>/chat/<?= $p['id'] ?>" class="btn-green flex-grow-1 text-center" style="font-size:.76rem;padding:.3rem .4rem">
          <i class="bi bi-chat-dots"></i> Chat
        </a>
        <?php else: ?>
        <span class="badge-pink flex-grow-1 text-center" style="padding:.35rem;font-size:.75rem"><?= ucfirst($p['interest_status']) ?></span>
        <?php endif; ?>
        <button onclick="toggleShortlist(<?= $p['id'] ?>,this)" class="btn border-0 p-1" title="Shortlist" style="color:<?= !empty($p['is_shortlisted'])?'var(--pink)':'var(--muted)' ?>">
          <i class="bi bi-bookmark<?= !empty($p['is_shortlisted'])?'-fill':'' ?>"></i>
        </button>
        <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($p['profile_id']) ?>" class="btn-outline-pink" style="font-size:.76rem;padding:.3rem .6rem">View</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($last_page > 1): ?>
<nav>
  <ul class="pagination justify-content-center" style="gap:.25rem">
    <?php if ($current_page > 1): ?>
    <li class="page-item">
      <a class="page-link" href="?<?= http_build_query(array_merge($filters,['page'=>$current_page-1])) ?>" style="border-radius:8px;border-color:var(--border)">
        <i class="bi bi-chevron-left"></i>
      </a>
    </li>
    <?php endif; ?>
    <?php for ($i = max(1,$current_page-2); $i <= min($last_page,$current_page+2); $i++): ?>
    <li class="page-item <?= $i===$current_page?'active':'' ?>">
      <a class="page-link" href="?<?= http_build_query(array_merge($filters,['page'=>$i])) ?>"
         style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>
    <?php if ($current_page < $last_page): ?>
    <li class="page-item">
      <a class="page-link" href="?<?= http_build_query(array_merge($filters,['page'=>$current_page+1])) ?>" style="border-radius:8px;border-color:var(--border)">
        <i class="bi bi-chevron-right"></i>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function sendInterest(uid, btn) {
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/send', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&receiver_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    if(d.success) btn.outerHTML='<span class="badge-pink flex-grow-1 text-center" style="padding:.35rem;font-size:.75rem"><i class="bi bi-check me-1"></i>Sent</span>';
    else { btn.disabled=false; alert(d.message); }
  });
}
function toggleShortlist(uid, btn) {
  fetch('<?= APP_URL ?>/profile/shortlist', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&target_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    const added = d.action === 'added';
    btn.style.color = added ? 'var(--pink)' : 'var(--muted)';
    btn.querySelector('i').className = added ? 'bi bi-bookmark-fill' : 'bi bi-bookmark';
  });
}
</script>
