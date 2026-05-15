<div class="page-header text-center" style="padding:2rem">
  <div class="section-tag mx-auto" style="display:inline-block">Discover</div>
  <h1 style="font-size:1.6rem;margin-bottom:.5rem">Find Your Perfect Match</h1>
  <p style="color:var(--muted);font-size:.92rem">Search from thousands of verified profiles</p>
</div>

<!-- Quick Search Card -->
<div class="mat-card p-4 mb-4" style="max-width:640px;margin:0 auto 2rem">
  <form method="GET" action="<?= APP_URL ?>/search/results">
    <div class="row g-3">
      <div class="col-6">
        <label class="form-label">Looking for</label>
        <select name="gender" class="form-select">
          <?php $g = Session::get('user_gender','male'); ?>
          <option value="female" <?= $g==='male'?'selected':'' ?>>Bride (Female)</option>
          <option value="male"   <?= $g==='female'?'selected':'' ?>>Groom (Male)</option>
        </select>
      </div>
      <div class="col-3">
        <label class="form-label">Age From</label>
        <input type="number" name="age_from" class="form-control" value="21" min="18" max="70">
      </div>
      <div class="col-3">
        <label class="form-label">Age To</label>
        <input type="number" name="age_to" class="form-control" value="35" min="18" max="70">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Religion</label>
        <input type="text" name="religion" class="form-control" placeholder="e.g. Hindu, Christian">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Location</label>
        <input type="text" name="city" class="form-control" placeholder="City or State">
      </div>
      <div class="col-12">
        <button type="submit" class="btn-pink w-100" style="padding:.7rem">
          <i class="bi bi-search me-2"></i> Search Profiles
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4" style="max-width:640px;margin:0 auto">
  <?php
  $ql = [
    ['label'=>'Recently Joined',  'icon'=>'bi-clock-history', 'color'=>'var(--pink)',  'url'=>'search/results?sort=newest'],
    ['label'=>'Advanced Search',  'icon'=>'bi-sliders',       'color'=>'var(--green)', 'url'=>'search/advanced'],
    ['label'=>'Search by ID',     'icon'=>'bi-person-badge',  'color'=>'#7C3AED',      'url'=>'search/results'],
    ['label'=>'Premium Profiles', 'icon'=>'bi-gem',           'color'=>'#F57F17',      'url'=>'search/results?highlight=1'],
  ];
  foreach ($ql as $q): ?>
  <div class="col-6">
    <a href="<?= APP_URL ?>/<?= $q['url'] ?>" class="mat-card p-3 d-flex align-items-center gap-3 text-decoration-none">
      <div style="width:40px;height:40px;border-radius:10px;background:<?= str_replace('var(--pink)','var(--pink-pale)',str_replace('var(--green)','var(--green-pale)',$q['color'])) ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="<?= $q['icon'] ?>" style="color:<?= $q['color'] ?>;font-size:1.1rem"></i>
      </div>
      <span style="font-weight:600;font-size:.88rem;color:var(--dark)"><?= $q['label'] ?></span>
    </a>
  </div>
  <?php endforeach; ?>
</div>
