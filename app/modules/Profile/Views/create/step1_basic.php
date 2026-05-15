<?php
// Wizard progress helper
$steps = [
  ['label'=>'Basic Info',  'icon'=>'bi-person-fill',     'url'=>'profile/create',           'step'=>1],
  ['label'=>'Family',      'icon'=>'bi-house-heart-fill', 'url'=>'profile/create/family',    'step'=>2],
  ['label'=>'Horoscope',   'icon'=>'bi-stars',            'url'=>'profile/create/horoscope', 'step'=>3],
  ['label'=>'Photos',      'icon'=>'bi-images',           'url'=>'profile/create/photos',    'step'=>4],
];
$currentStep = 1;
?>
<!-- Wizard Steps -->
<div class="mat-card p-3 mb-4">
  <div class="d-flex align-items-center justify-content-between position-relative">
    <div style="position:absolute;top:50%;left:12%;right:12%;height:2px;background:var(--border);z-index:0"></div>
    <?php foreach ($steps as $s): ?>
    <div class="d-flex flex-column align-items-center gap-1 position-relative" style="z-index:1;flex:1">
      <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;
        <?= $s['step'] < $currentStep ? 'background:var(--green);color:#fff' : ($s['step'] === $currentStep ? 'background:var(--pink);color:#fff;box-shadow:0 4px 12px rgba(233,30,140,.3)' : 'background:var(--border);color:var(--muted)') ?>">
        <?php if ($s['step'] < $currentStep): ?>
          <i class="bi bi-check2-circle"></i>
        <?php else: ?>
          <i class="<?= $s['icon'] ?>"></i>
        <?php endif; ?>
      </div>
      <span style="font-size:.68rem;font-weight:<?= $s['step']===$currentStep?'700':'500' ?>;color:<?= $s['step']===$currentStep?'var(--pink)':'var(--muted)' ?>;white-space:nowrap"><?= $s['label'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="page-header mb-4">
  <div class="section-tag">Step 1 of 4</div>
  <h1 style="font-size:1.3rem">Basic Profile Information</h1>
  <p style="color:var(--muted);font-size:.85rem;margin-top:.3rem">Tell us about yourself. Fields marked * are required.</p>
</div>

<?php if (!empty($error)): ?>
<div class="flash-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/profile/basic">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">

<div class="row g-4">

  <!-- Personal Details -->
  <div class="col-12">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-person me-2" style="color:var(--pink)"></i>Personal Details</h5>
      <div class="row g-3">
        <div class="col-6 col-md-3">
          <label class="form-label">Date of Birth *</label>
          <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($profile['dob'] ?? '') ?>" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Height (cm)</label>
          <input type="number" name="height_cm" class="form-control" value="<?= $profile['height_cm'] ?? '' ?>" min="130" max="220" placeholder="e.g. 165">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Weight (kg)</label>
          <input type="number" name="weight_kg" class="form-control" value="<?= $profile['weight_kg'] ?? '' ?>" min="30" max="180" placeholder="e.g. 60">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Marital Status *</label>
          <select name="marital_status" class="form-select" required>
            <?php foreach (['never_married'=>'Never Married','divorced'=>'Divorced','widowed'=>'Widowed','separated'=>'Separated'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['marital_status']??'never_married')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Complexion</label>
          <select name="complexion" class="form-select">
            <option value="">Select</option>
            <?php foreach (['very_fair'=>'Very Fair','fair'=>'Fair','wheatish'=>'Wheatish','dark'=>'Dark'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['complexion']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Body Type</label>
          <select name="body_type" class="form-select">
            <option value="">Select</option>
            <?php foreach (['slim'=>'Slim','average'=>'Average','athletic'=>'Athletic','heavy'=>'Heavy'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['body_type']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Mother Tongue</label>
          <input type="text" name="mother_tongue" class="form-control" value="<?= htmlspecialchars($profile['mother_tongue'] ?? '') ?>" placeholder="e.g. Tamil">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Diet</label>
          <select name="diet" class="form-select">
            <option value="">Select</option>
            <?php foreach (['vegetarian'=>'Vegetarian','non_vegetarian'=>'Non-Vegetarian','eggetarian'=>'Eggetarian','vegan'=>'Vegan'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['diet']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Smoking</label>
          <select name="smoking" class="form-select">
            <?php foreach (['no'=>'No','occasionally'=>'Occasionally','yes'=>'Yes'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['smoking']??'no')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Drinking</label>
          <select name="drinking" class="form-select">
            <?php foreach (['no'=>'No','occasionally'=>'Occasionally','yes'=>'Yes'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['drinking']??'no')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Religion & Community -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4 h-100">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-globe-asia-australia me-2" style="color:var(--green)"></i>Religion & Community</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Religion *</label>
          <input type="text" name="religion" class="form-control" value="<?= htmlspecialchars($profile['religion'] ?? '') ?>" placeholder="e.g. Hindu" required>
        </div>
        <div class="col-6">
          <label class="form-label">Caste</label>
          <input type="text" name="caste" class="form-control" value="<?= htmlspecialchars($profile['caste'] ?? '') ?>" placeholder="e.g. Iyer">
        </div>
        <div class="col-6">
          <label class="form-label">Sub-Caste</label>
          <input type="text" name="sub_caste" class="form-control" value="<?= htmlspecialchars($profile['sub_caste'] ?? '') ?>" placeholder="Optional">
        </div>
        <div class="col-6">
          <label class="form-label">Gothram</label>
          <input type="text" name="gothram" class="form-control" value="<?= htmlspecialchars($profile['gothram'] ?? '') ?>" placeholder="e.g. Koundinya">
        </div>
        <div class="col-6">
          <label class="form-label">Star (Nakshatra)</label>
          <input type="text" name="star" class="form-control" value="<?= htmlspecialchars($profile['star'] ?? '') ?>" placeholder="e.g. Rohini">
        </div>
        <div class="col-6">
          <label class="form-label">Raasi</label>
          <input type="text" name="raasi" class="form-control" value="<?= htmlspecialchars($profile['raasi'] ?? '') ?>" placeholder="e.g. Rishabam">
        </div>
        <div class="col-12">
          <label class="form-label">Dhosam</label>
          <select name="dhosam" class="form-select">
            <?php foreach (['none'=>'None','chevvai'=>'Chevvai Dhosam','rahu'=>'Rahu Dhosam','ketu'=>'Ketu Dhosam','naga'=>'Naga Dhosam','shani'=>'Shani Dhosam','none_confirmed'=>'No Dhosam (Confirmed)'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['dhosam']??'none')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Education & Career -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4 h-100">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-mortarboard me-2" style="color:var(--pink)"></i>Education & Career</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Education *</label>
          <input type="text" name="education" class="form-control" value="<?= htmlspecialchars($profile['education'] ?? '') ?>" placeholder="e.g. B.E. Computer Science" required>
        </div>
        <div class="col-6">
          <label class="form-label">College / Institute</label>
          <input type="text" name="education_detail" class="form-control" value="<?= htmlspecialchars($profile['education_detail'] ?? '') ?>" placeholder="College name">
        </div>
        <div class="col-6">
          <label class="form-label">Employed In</label>
          <select name="employed_in" class="form-select">
            <option value="">Select</option>
            <?php foreach (['government'=>'Government','private'=>'Private','business'=>'Business','not_working'=>'Not Working','other'=>'Other'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['employed_in']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label">Occupation</label>
          <input type="text" name="occupation" class="form-control" value="<?= htmlspecialchars($profile['occupation'] ?? '') ?>" placeholder="e.g. Software Engineer">
        </div>
        <div class="col-12">
          <label class="form-label">Annual Income</label>
          <select name="annual_income" class="form-select">
            <option value="">Prefer not to say</option>
            <?php foreach (['no_income'=>'No Income','below_1l'=>'Below ₹1 Lakh','1l_3l'=>'₹1-3 Lakh','3l_5l'=>'₹3-5 Lakh','5l_10l'=>'₹5-10 Lakh','10l_25l'=>'₹10-25 Lakh','above_25l'=>'Above ₹25 Lakh'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['annual_income']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
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
        <div class="col-6">
          <label class="form-label">Country</label>
          <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($profile['country'] ?? 'India') ?>">
        </div>
        <div class="col-6">
          <label class="form-label">State *</label>
          <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($profile['state'] ?? '') ?>" placeholder="e.g. Tamil Nadu" required>
        </div>
        <div class="col-6">
          <label class="form-label">City *</label>
          <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" placeholder="e.g. Chennai" required>
        </div>
        <div class="col-6">
          <label class="form-label">Pincode</label>
          <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($profile['pincode'] ?? '') ?>" placeholder="600001" maxlength="6">
        </div>
      </div>
    </div>
  </div>

  <!-- About Me -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-chat-quote me-2" style="color:var(--pink)"></i>About Me</h5>
      <textarea name="about_me" class="form-control" rows="5" maxlength="1000" placeholder="Write a brief description about yourself, your interests, values, and what you're looking for in a partner..." style="resize:none"><?= htmlspecialchars($profile['about_me'] ?? '') ?></textarea>
      <div style="text-align:right;font-size:.75rem;color:var(--muted);margin-top:.3rem">
        <span id="aboutCount"><?= strlen($profile['about_me'] ?? '') ?></span>/1000
      </div>
    </div>
  </div>

</div><!-- row -->

<div class="d-flex justify-content-end gap-3 mt-4">
  <a href="<?= APP_URL ?>/dashboard" class="btn-outline-pink">Skip for Now</a>
  <button type="submit" class="btn-pink">
    Next: Family Details <i class="bi bi-arrow-right ms-1"></i>
  </button>
</div>

</form>

<script>
const aboutTa = document.querySelector('[name="about_me"]');
if (aboutTa) {
  aboutTa.addEventListener('input', () => {
    document.getElementById('aboutCount').textContent = aboutTa.value.length;
  });
}
</script>
