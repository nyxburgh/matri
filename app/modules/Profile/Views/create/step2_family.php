<?php $currentStep = 2;
$steps = [['label'=>'Basic Info','icon'=>'bi-person-fill','step'=>1],['label'=>'Family','icon'=>'bi-house-heart-fill','step'=>2],['label'=>'Horoscope','icon'=>'bi-stars','step'=>3],['label'=>'Photos','icon'=>'bi-images','step'=>4]];
?>
<!-- Wizard Steps -->
<div class="mat-card p-3 mb-4">
  <div class="d-flex align-items-center justify-content-between position-relative">
    <div style="position:absolute;top:50%;left:12%;right:12%;height:2px;background:var(--border);z-index:0"></div>
    <?php foreach ($steps as $s): ?>
    <div class="d-flex flex-column align-items-center gap-1 position-relative" style="z-index:1;flex:1">
      <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;
        <?= $s['step'] < $currentStep ? 'background:var(--green);color:#fff' : ($s['step']==$currentStep ? 'background:var(--pink);color:#fff;box-shadow:0 4px 12px rgba(233,30,140,.3)' : 'background:var(--border);color:var(--muted)') ?>">
        <i class="<?= $s['step'] < $currentStep ? 'bi bi-check2-circle' : ['bi-person-fill','bi-house-heart-fill','bi-stars','bi-images'][$s['step']-1] ?>"></i>
      </div>
      <span style="font-size:.68rem;font-weight:<?= $s['step']==$currentStep?'700':'500' ?>;color:<?= $s['step']==$currentStep?'var(--pink)':'var(--muted)' ?>;white-space:nowrap"><?= $s['label'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="page-header mb-4">
  <div class="section-tag">Step 2 of 4</div>
  <h1 style="font-size:1.3rem">Family Details</h1>
  <p style="color:var(--muted);font-size:.85rem;margin-top:.3rem">Help matches understand your family background.</p>
</div>

<form method="POST" action="<?= APP_URL ?>/profile/family">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">

<div class="row g-4">
  <!-- Parents -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-people me-2" style="color:var(--pink)"></i>Parents</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Father's Name</label>
          <input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($family['father_name'] ?? '') ?>" placeholder="Father's name">
        </div>
        <div class="col-6">
          <label class="form-label">Father's Status</label>
          <select name="father_status" class="form-select">
            <option value="">Select</option>
            <?php foreach (['employed'=>'Employed','business'=>'Business','retired'=>'Retired','deceased'=>'Deceased','not_known'=>'Not Known'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($family['father_status']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6">
          <label class="form-label">Mother's Name</label>
          <input type="text" name="mother_name" class="form-control" value="<?= htmlspecialchars($family['mother_name'] ?? '') ?>" placeholder="Mother's name">
        </div>
        <div class="col-6">
          <label class="form-label">Mother's Status</label>
          <select name="mother_status" class="form-select">
            <option value="">Select</option>
            <?php foreach (['homemaker'=>'Homemaker','employed'=>'Employed','business'=>'Business','retired'=>'Retired','deceased'=>'Deceased','not_known'=>'Not Known'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($family['mother_status']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Siblings -->
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-person-hearts me-2" style="color:var(--green)"></i>Siblings</h5>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Brothers</label>
          <input type="number" name="brothers" class="form-control" value="<?= $family['brothers'] ?? 0 ?>" min="0" max="10">
        </div>
        <div class="col-6">
          <label class="form-label">Brothers Married</label>
          <input type="number" name="brothers_married" class="form-control" value="<?= $family['brothers_married'] ?? 0 ?>" min="0" max="10">
        </div>
        <div class="col-6">
          <label class="form-label">Sisters</label>
          <input type="number" name="sisters" class="form-control" value="<?= $family['sisters'] ?? 0 ?>" min="0" max="10">
        </div>
        <div class="col-6">
          <label class="form-label">Sisters Married</label>
          <input type="number" name="sisters_married" class="form-control" value="<?= $family['sisters_married'] ?? 0 ?>" min="0" max="10">
        </div>
      </div>
    </div>
  </div>

  <!-- Family Background -->
  <div class="col-12">
    <div class="mat-card p-4">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-house-heart me-2" style="color:var(--pink)"></i>Family Background</h5>
      <div class="row g-3">
        <div class="col-6 col-md-3">
          <label class="form-label">Family Type</label>
          <select name="family_type" class="form-select">
            <option value="">Select</option>
            <?php foreach (['joint'=>'Joint Family','nuclear'=>'Nuclear Family','extended'=>'Extended Family'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($family['family_type']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Family Status</label>
          <select name="family_status" class="form-select">
            <option value="">Select</option>
            <?php foreach (['middle_class'=>'Middle Class','upper_middle'=>'Upper Middle Class','rich'=>'Rich','affluent'=>'Affluent'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($family['family_status']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Family Values</label>
          <select name="family_values" class="form-select">
            <option value="">Select</option>
            <?php foreach (['orthodox'=>'Orthodox','traditional'=>'Traditional','moderate'=>'Moderate','liberal'=>'Liberal'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($family['family_values']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Native Place</label>
          <input type="text" name="native_place" class="form-control" value="<?= htmlspecialchars($family['native_place'] ?? '') ?>" placeholder="e.g. Thanjavur">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-4">
  <a href="<?= APP_URL ?>/profile/create" class="btn-outline-pink"><i class="bi bi-arrow-left me-1"></i> Back</a>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/profile/create/horoscope" class="btn-outline-pink">Skip</a>
    <button type="submit" class="btn-pink">Next: Horoscope <i class="bi bi-arrow-right ms-1"></i></button>
  </div>
</div>
</form>
