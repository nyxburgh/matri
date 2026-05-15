<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag green">Advanced</div>
    <h1 style="font-size:1.35rem">Advanced Search</h1>
    <p style="color:var(--muted);font-size:.85rem;margin-top:.2rem">Fine-tune with detailed filters to find your ideal match.</p>
  </div>
  <span class="badge-gold" style="font-size:.82rem;padding:.35rem .9rem">
    <i class="bi bi-gem me-1"></i> <?= htmlspecialchars($plan['plan_name'] ?? 'Premium') ?> Feature
  </span>
</div>

<form method="GET" action="<?= APP_URL ?>/search/results">
<div class="row g-4">

  <!-- Basic Criteria -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-person me-2" style="color:var(--pink)"></i>Basic Criteria</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Looking for</label>
          <select name="gender" class="form-select">
            <?php $g = Session::get('user_gender'); ?>
            <option value="female" <?= $g==='male'?'selected':'' ?>>Bride (Female)</option>
            <option value="male"   <?= $g==='female'?'selected':'' ?>>Groom (Male)</option>
            <option value="">Any</option>
          </select>
        </div>
        <div class="col-3">
          <label class="form-label">Age From</label>
          <input type="number" name="age_from" class="form-control" value="21" min="18" max="70">
        </div>
        <div class="col-3">
          <label class="form-label">Age To</label>
          <input type="number" name="age_to" class="form-control" value="40" min="18" max="70">
        </div>
        <div class="col-6">
          <label class="form-label">Marital Status</label>
          <select name="marital_status" class="form-select">
            <option value="">Any</option>
            <option value="never_married">Never Married</option>
            <option value="divorced">Divorced</option>
            <option value="widowed">Widowed</option>
            <option value="separated">Separated</option>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label">Diet</label>
          <select name="diet" class="form-select">
            <option value="">Any</option>
            <option value="vegetarian">Vegetarian</option>
            <option value="non_vegetarian">Non-Vegetarian</option>
            <option value="eggetarian">Eggetarian</option>
            <option value="vegan">Vegan</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Religion & Community -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-globe-asia-australia me-2" style="color:var(--green)"></i>Religion & Community</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Religion</label>
          <input type="text" name="religion" class="form-control" placeholder="e.g. Hindu">
        </div>
        <div class="col-6">
          <label class="form-label">Caste</label>
          <input type="text" name="caste" class="form-control" placeholder="e.g. Iyer, Vellalar">
        </div>
        <div class="col-6">
          <label class="form-label">Star (Nakshatra)</label>
          <input type="text" name="star" class="form-control" placeholder="e.g. Rohini">
        </div>
        <div class="col-6">
          <label class="form-label">Dhosam</label>
          <select name="dhosam" class="form-select">
            <option value="">Any</option>
            <option value="none">No Dhosam</option>
            <option value="chevvai">Chevvai Dhosam</option>
            <option value="rahu">Rahu Dhosam</option>
            <option value="ketu">Ketu Dhosam</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Education & Career -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-mortarboard me-2" style="color:var(--pink)"></i>Education & Career</h5>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Education</label>
          <input type="text" name="education" class="form-control" placeholder="e.g. B.E., M.B.A., MBBS">
        </div>
        <div class="col-6">
          <label class="form-label">Employed In</label>
          <select name="employed_in" class="form-select">
            <option value="">Any</option>
            <option value="government">Government</option>
            <option value="private">Private</option>
            <option value="business">Business</option>
            <option value="not_working">Not Working</option>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label">Annual Income</label>
          <select name="annual_income" class="form-select">
            <option value="">Any</option>
            <option value="5l_10l">₹5-10 Lakh</option>
            <option value="10l_25l">₹10-25 Lakh</option>
            <option value="above_25l">Above ₹25 Lakh</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Location -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-geo-alt me-2" style="color:var(--green)"></i>Location</h5>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">City or State</label>
          <input type="text" name="city" class="form-control" placeholder="e.g. Chennai, Coimbatore, Tamil Nadu">
        </div>
        <div class="col-12">
          <label class="form-label">Search by Profile ID</label>
          <input type="text" name="profile_id" class="form-control" placeholder="MAT-000001" style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
        </div>
      </div>
    </div>
  </div>

</div><!-- row -->

<div class="d-flex justify-content-end gap-3 mt-4">
  <a href="<?= APP_URL ?>/search" class="btn-outline-pink">Reset</a>
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem">
    <i class="bi bi-search me-1"></i> Search Now
  </button>
</div>
</form>
