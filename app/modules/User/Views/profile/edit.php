<?php
$planets = ['Su'=>'Sun','Mo'=>'Moon','Ma'=>'Mars','Me'=>'Mercury','Ju'=>'Jupiter','Ve'=>'Venus','Sa'=>'Saturn','Ra'=>'Rahu','Ke'=>'Ketu','La'=>'Lagna'];
$rasiData    = !empty($horoscope['rasi_chart'])    ? json_decode($horoscope['rasi_chart'],    true) : [];
$navamsaData = !empty($horoscope['navamsa_chart']) ? json_decode($horoscope['navamsa_chart'], true) : [];
$partnerPref = !empty($profile['partner_pref'])    ? json_decode($profile['partner_pref'],    true) : [];
?>

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">My Account</div>
    <h1 style="font-size:1.35rem">Edit My Profile</h1>
  </div>
  <a href="<?= APP_URL ?>/my-profile" class="btn-outline-green d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.38rem .9rem">
    <i class="bi bi-eye"></i> Preview Profile
  </a>
</div>

<?php if (!empty($flash)): ?>
<div class="flash-success mb-3"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="flash-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Tab Nav -->
<ul class="nav nav-tabs mb-4" style="border-bottom:2px solid var(--border);gap:.25rem" id="editTabs">
  <?php
  $tabs = [
    ['id'=>'basic',     'label'=>'Basic Info',      'icon'=>'bi-person-fill'],
    ['id'=>'family',    'label'=>'Family',           'icon'=>'bi-house-heart-fill'],
    ['id'=>'horoscope', 'label'=>'Horoscope',        'icon'=>'bi-stars'],
    ['id'=>'photos',    'label'=>'Photos',           'icon'=>'bi-images'],
    ['id'=>'partner',   'label'=>'Partner Prefs',    'icon'=>'bi-heart-fill'],
    ['id'=>'privacy',   'label'=>'Privacy',          'icon'=>'bi-shield-fill-check'],
  ];
  foreach ($tabs as $tab): ?>
  <li class="nav-item">
    <button class="nav-link d-flex align-items-center gap-1" id="tab-<?= $tab['id'] ?>" onclick="showTab('<?= $tab['id'] ?>')"
            style="border:none;border-bottom:2px solid transparent;border-radius:0;font-size:.85rem;font-weight:600;color:var(--muted);padding:.5rem .9rem;background:none;cursor:pointer;transition:all .2s">
      <i class="<?= $tab['icon'] ?>"></i>
      <span class="d-none d-sm-inline"><?= $tab['label'] ?></span>
    </button>
  </li>
  <?php endforeach; ?>
</ul>

<!-- ══ TAB: Basic Info ══ -->
<div id="pane-basic" class="tab-pane">
<form method="POST" action="<?= APP_URL ?>/profile/edit/basic">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.1rem"><i class="bi bi-person me-2" style="color:var(--pink)"></i>Personal</h5>
      <div class="row g-3">
        <div class="col-6"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= $profile['dob']??'' ?>" max="<?= date('Y-m-d',strtotime('-18 years')) ?>"></div>
        <div class="col-6"><label class="form-label">Height (cm)</label><input type="number" name="height_cm" class="form-control" value="<?= $profile['height_cm']??'' ?>" min="130" max="220"></div>
        <div class="col-6"><label class="form-label">Weight (kg)</label><input type="number" name="weight_kg" class="form-control" value="<?= $profile['weight_kg']??'' ?>" min="30" max="180"></div>
        <div class="col-6"><label class="form-label">Marital Status</label>
          <select name="marital_status" class="form-select">
            <?php foreach(['never_married'=>'Never Married','divorced'=>'Divorced','widowed'=>'Widowed','separated'=>'Separated'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['marital_status']??'never_married')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Complexion</label>
          <select name="complexion" class="form-select"><option value="">Select</option>
            <?php foreach(['very_fair'=>'Very Fair','fair'=>'Fair','wheatish'=>'Wheatish','dark'=>'Dark'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['complexion']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Body Type</label>
          <select name="body_type" class="form-select"><option value="">Select</option>
            <?php foreach(['slim'=>'Slim','average'=>'Average','athletic'=>'Athletic','heavy'=>'Heavy'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['body_type']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Mother Tongue</label><input type="text" name="mother_tongue" class="form-control" value="<?= htmlspecialchars($profile['mother_tongue']??'') ?>" placeholder="e.g. Tamil"></div>
        <div class="col-6"><label class="form-label">Diet</label>
          <select name="diet" class="form-select"><option value="">Select</option>
            <?php foreach(['vegetarian'=>'Vegetarian','non_vegetarian'=>'Non-Vegetarian','eggetarian'=>'Eggetarian','vegan'=>'Vegan'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['diet']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Smoking</label>
          <select name="smoking" class="form-select"><?php foreach(['no'=>'No','occasionally'=>'Occasionally','yes'=>'Yes'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['smoking']??'no')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select>
        </div>
        <div class="col-6"><label class="form-label">Drinking</label>
          <select name="drinking" class="form-select"><?php foreach(['no'=>'No','occasionally'=>'Occasionally','yes'=>'Yes'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['drinking']??'no')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4 mb-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.1rem"><i class="bi bi-globe-asia-australia me-2" style="color:var(--green)"></i>Religion & Community</h5>
      <div class="row g-3">
        <div class="col-6"><label class="form-label">Religion</label><input type="text" name="religion" class="form-control" value="<?= htmlspecialchars($profile['religion']??'') ?>" placeholder="e.g. Hindu"></div>
        <div class="col-6"><label class="form-label">Caste</label><input type="text" name="caste" class="form-control" value="<?= htmlspecialchars($profile['caste']??'') ?>" placeholder="e.g. Iyer"></div>
        <div class="col-6"><label class="form-label">Sub-Caste</label><input type="text" name="sub_caste" class="form-control" value="<?= htmlspecialchars($profile['sub_caste']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Gothram</label><input type="text" name="gothram" class="form-control" value="<?= htmlspecialchars($profile['gothram']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Star</label><input type="text" name="star" class="form-control" value="<?= htmlspecialchars($profile['star']??'') ?>" placeholder="e.g. Rohini"></div>
        <div class="col-6"><label class="form-label">Raasi</label><input type="text" name="raasi" class="form-control" value="<?= htmlspecialchars($profile['raasi']??'') ?>" placeholder="e.g. Rishabam"></div>
        <div class="col-12"><label class="form-label">Dhosam</label>
          <select name="dhosam" class="form-select">
            <?php foreach(['none'=>'None','chevvai'=>'Chevvai','rahu'=>'Rahu','ketu'=>'Ketu','naga'=>'Naga','shani'=>'Shani','none_confirmed'=>'No Dhosam (Confirmed)'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($profile['dhosam']??'none')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
    <div class="mat-card p-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.1rem"><i class="bi bi-mortarboard me-2" style="color:var(--pink)"></i>Education & Location</h5>
      <div class="row g-3">
        <div class="col-6"><label class="form-label">Education</label><input type="text" name="education" class="form-control" value="<?= htmlspecialchars($profile['education']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Occupation</label><input type="text" name="occupation" class="form-control" value="<?= htmlspecialchars($profile['occupation']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Employed In</label>
          <select name="employed_in" class="form-select"><option value="">Select</option>
            <?php foreach(['government'=>'Government','private'=>'Private','business'=>'Business','not_working'=>'Not Working','other'=>'Other'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['employed_in']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Annual Income</label>
          <select name="annual_income" class="form-select"><option value="">Not specified</option>
            <?php foreach(['no_income'=>'No Income','below_1l'=>'Below ₹1L','1l_3l'=>'₹1-3L','3l_5l'=>'₹3-5L','5l_10l'=>'₹5-10L','10l_25l'=>'₹10-25L','above_25l'=>'Above ₹25L'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($profile['annual_income']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="<?= htmlspecialchars($profile['state']??'') ?>"></div>
        <div class="col-6"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= htmlspecialchars($profile['city']??'') ?>"></div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="mat-card p-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:.75rem"><i class="bi bi-chat-quote me-2" style="color:var(--pink)"></i>About Me</h5>
      <textarea name="about_me" class="form-control" rows="4" maxlength="1000" placeholder="Describe yourself, your values, hobbies..." style="resize:none"><?= htmlspecialchars($profile['about_me']??'') ?></textarea>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mt-3">
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem"><i class="bi bi-check-circle me-1"></i> Save Basic Info</button>
</div>
</form>
</div>

<!-- ══ TAB: Family ══ -->
<div id="pane-family" class="tab-pane" style="display:none">
<form method="POST" action="<?= APP_URL ?>/profile/family">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.1rem"><i class="bi bi-people me-2" style="color:var(--pink)"></i>Parents</h5>
      <div class="row g-3">
        <div class="col-6"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($family['father_name']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Father's Status</label>
          <select name="father_status" class="form-select"><option value="">Select</option>
            <?php foreach(['employed'=>'Employed','business'=>'Business','retired'=>'Retired','deceased'=>'Deceased','not_known'=>'Not Known'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($family['father_status']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control" value="<?= htmlspecialchars($family['mother_name']??'') ?>"></div>
        <div class="col-6"><label class="form-label">Mother's Status</label>
          <select name="mother_status" class="form-select"><option value="">Select</option>
            <?php foreach(['homemaker'=>'Homemaker','employed'=>'Employed','business'=>'Business','retired'=>'Retired','deceased'=>'Deceased'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($family['mother_status']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mat-card p-4">
      <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.1rem"><i class="bi bi-house-heart me-2" style="color:var(--green)"></i>Family Background</h5>
      <div class="row g-3">
        <div class="col-6"><label class="form-label">Brothers</label><input type="number" name="brothers" class="form-control" value="<?= $family['brothers']??0 ?>" min="0" max="10"></div>
        <div class="col-6"><label class="form-label">Brothers Married</label><input type="number" name="brothers_married" class="form-control" value="<?= $family['brothers_married']??0 ?>" min="0" max="10"></div>
        <div class="col-6"><label class="form-label">Sisters</label><input type="number" name="sisters" class="form-control" value="<?= $family['sisters']??0 ?>" min="0" max="10"></div>
        <div class="col-6"><label class="form-label">Sisters Married</label><input type="number" name="sisters_married" class="form-control" value="<?= $family['sisters_married']??0 ?>" min="0" max="10"></div>
        <div class="col-6"><label class="form-label">Family Type</label>
          <select name="family_type" class="form-select"><option value="">Select</option>
            <?php foreach(['joint'=>'Joint','nuclear'=>'Nuclear','extended'=>'Extended'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($family['family_type']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Family Values</label>
          <select name="family_values" class="form-select"><option value="">Select</option>
            <?php foreach(['orthodox'=>'Orthodox','traditional'=>'Traditional','moderate'=>'Moderate','liberal'=>'Liberal'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($family['family_values']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Family Status</label>
          <select name="family_status" class="form-select"><option value="">Select</option>
            <?php foreach(['middle_class'=>'Middle Class','upper_middle'=>'Upper Middle','rich'=>'Rich','affluent'=>'Affluent'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($family['family_status']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-6"><label class="form-label">Native Place</label><input type="text" name="native_place" class="form-control" value="<?= htmlspecialchars($family['native_place']??'') ?>" placeholder="e.g. Thanjavur"></div>
      </div>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mt-3">
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem"><i class="bi bi-check-circle me-1"></i> Save Family Details</button>
</div>
</form>
</div>

<!-- ══ TAB: Horoscope ══ -->
<div id="pane-horoscope" class="tab-pane" style="display:none">
<form method="POST" action="<?= APP_URL ?>/profile/horoscope">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-calendar-event me-2" style="color:var(--pink)"></i>Birth Details</h5>
  <div class="row g-3">
    <div class="col-6 col-md-3"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control" value="<?= $horoscope['birth_date']??'' ?>"></div>
    <div class="col-6 col-md-3"><label class="form-label">Birth Time</label><input type="time" name="birth_time" class="form-control" value="<?= $horoscope['birth_time']??'' ?>"></div>
    <div class="col-12 col-md-6"><label class="form-label">Birth Place</label><input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($horoscope['birth_place']??'') ?>" placeholder="e.g. Chennai, Tamil Nadu"></div>
    <div class="col-6 col-md-3"><label class="form-label">Lagna</label><input type="text" name="lagna" class="form-control" value="<?= htmlspecialchars($horoscope['lagna']??'') ?>" placeholder="e.g. Mesham"></div>
    <div class="col-6 col-md-3"><label class="form-label">Rasi</label><input type="text" name="rasi" class="form-control" value="<?= htmlspecialchars($horoscope['rasi']??'') ?>"></div>
    <div class="col-6 col-md-3"><label class="form-label">Nakshatra</label><input type="text" name="nakshatra" class="form-control" value="<?= htmlspecialchars($horoscope['nakshatra']??'') ?>"></div>
    <div class="col-6 col-md-3"><label class="form-label">Pada</label>
      <select name="nakshatra_pada" class="form-select"><option value="">-</option>
        <?php for($i=1;$i<=4;$i++): ?><option value="<?= $i ?>" <?= ($horoscope['nakshatra_pada']??'')==$i?'selected':'' ?>><?= $i ?></option><?php endfor; ?>
      </select>
    </div>
  </div>
</div>
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-exclamation-circle me-2" style="color:#F57F17"></i>Dosham Flags</h5>
  <div class="d-flex gap-4 flex-wrap">
    <?php foreach([['chevvai_dhosam','Chevvai Dhosam'],['rahu_dhosam','Rahu Dhosam'],['kala_sarpa','Kala Sarpa']] as [$key,$label]): ?>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="1" id="e_<?= $key ?>" <?= !empty($horoscope[$key])?'checked':'' ?>>
      <label class="form-check-label" for="e_<?= $key ?>" style="font-size:.88rem"><?= $label ?></label>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:.75rem"><i class="bi bi-eye me-2" style="color:var(--green)"></i>Horoscope Visibility</h5>
  <select name="visibility" class="form-select" style="max-width:240px">
    <?php foreach(['public'=>'Everyone','members'=>'Members Only','private'=>'Private'] as $v=>$l): ?>
    <option value="<?= $v ?>" <?= ($horoscope['visibility']??'members')===$v?'selected':'' ?>><?= $l ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="d-flex justify-content-end">
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem"><i class="bi bi-check-circle me-1"></i> Save Horoscope</button>
</div>
</form>
</div>

<!-- ══ TAB: Photos ══ -->
<div id="pane-photos" class="tab-pane" style="display:none">
<div class="mat-card p-4 mb-4">
  <div id="dropZone" style="border:2.5px dashed var(--pink);border-radius:var(--radius);padding:2rem;text-align:center;cursor:pointer;background:var(--pink-pale)" onclick="document.getElementById('photoInput').click()" ondragover="event.preventDefault()" ondrop="handleDrop(event)">
    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:var(--pink)"></i>
    <div style="font-weight:700;font-size:.92rem;color:var(--dark);margin:.4rem 0">Upload Photos</div>
    <p style="font-size:.78rem;color:var(--muted)">JPEG, PNG, WEBP · Max 10 photos · 5MB each</p>
    <div id="uploadStatus" style="font-size:.8rem;color:var(--green);margin-top:.5rem"></div>
  </div>
  <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="uploadFiles(this.files)" multiple>
</div>

<div class="row g-3" id="photoGrid">
  <?php foreach ($photos as $ph): ?>
  <div class="col-6 col-md-4 col-lg-3" id="photo-<?= $ph['id'] ?>">
    <div class="mat-card overflow-hidden">
      <div style="height:150px;overflow:hidden;background:var(--pink-pale)">
        <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($ph['file_path']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
      </div>
      <div class="p-2 d-flex align-items-center justify-content-between gap-1">
        <?php if ($ph['is_primary']): ?>
          <span class="badge-green" style="font-size:.65rem"><i class="bi bi-star-fill me-1"></i>Primary</span>
        <?php else: ?>
          <button onclick="setPrimary(<?= $ph['id'] ?>)" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.68rem;padding:.18rem .5rem">Set Primary</button>
        <?php endif; ?>
        <span style="font-size:.65rem;font-weight:700;padding:.18rem .5rem;border-radius:50px;background:<?= ['pending'=>'#FEF3C7','approved'=>'#D1FAE5','rejected'=>'#FEE2E2'][$ph['is_approved']] ?>;color:<?= ['pending'=>'#92400E','approved'=>'#065F46','rejected'=>'#991B1B'][$ph['is_approved']] ?>"><?= ucfirst($ph['is_approved']) ?></span>
        <button onclick="deletePhoto(<?= $ph['id'] ?>)" class="btn btn-link btn-sm text-danger p-0" title="Delete"><i class="bi bi-trash3"></i></button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
</div>

<!-- ══ TAB: Partner Preferences ══ -->
<div id="pane-partner" class="tab-pane" style="display:none">
<form method="POST" action="<?= APP_URL ?>/profile/edit/partner">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="mat-card p-4">
  <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-heart me-2" style="color:var(--pink)"></i>What You're Looking For</h5>
  <div class="row g-3">
    <div class="col-6 col-md-3"><label class="form-label">Age From</label><input type="number" name="age_from" class="form-control" value="<?= $partnerPref['age_from']??18 ?>" min="18" max="70"></div>
    <div class="col-6 col-md-3"><label class="form-label">Age To</label><input type="number" name="age_to" class="form-control" value="<?= $partnerPref['age_to']??50 ?>" min="18" max="70"></div>
    <div class="col-12 col-md-6"><label class="form-label">Annual Income (min)</label>
      <select name="annual_income" class="form-select"><option value="">Not specified</option>
        <?php foreach(['no_income'=>'No minimum','1l_3l'=>'₹1-3L','3l_5l'=>'₹3-5L','5l_10l'=>'₹5-10L','10l_25l'=>'₹10-25L','above_25l'=>'₹25L+'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($partnerPref['annual_income']??'')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-12"><label class="form-label">About Partner (what you're looking for)</label>
      <textarea name="about_partner" class="form-control" rows="4" maxlength="500" placeholder="Describe the kind of person you're looking for..."><?= htmlspecialchars($partnerPref['about_partner']??'') ?></textarea>
    </div>
  </div>
</div>
<div class="d-flex justify-content-end mt-3">
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem"><i class="bi bi-check-circle me-1"></i> Save Preferences</button>
</div>
</form>
</div>

<!-- ══ TAB: Privacy ══ -->
<div id="pane-privacy" class="tab-pane" style="display:none">
<form method="POST" action="<?= APP_URL ?>/profile/privacy">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.92rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-shield-lock me-2" style="color:var(--pink)"></i>Profile Visibility</h5>
  <div class="mb-3">
    <label class="form-label">Who can see my profile?</label>
    <select name="profile_visible" class="form-select" style="max-width:260px">
      <?php foreach(['all'=>'Everyone','members'=>'Registered Members Only','none'=>'Hide My Profile'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= ($profile['profile_visible']??'all')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" name="show_contact" value="1" id="showContact" <?= !empty($profile['show_contact'])?'checked':'' ?>>
    <label class="form-check-label" for="showContact">Show my contact details to accepted matches</label>
  </div>
  <hr style="border-color:var(--border)">
  <div class="mb-3">
    <label class="form-label">Horoscope Visibility</label>
    <select name="horoscope_visibility" class="form-select" style="max-width:260px">
      <?php foreach(['public'=>'Everyone','members'=>'Members Only','private'=>'Private (Only Me)'] as $v=>$l): ?>
      <option value="<?= $v ?>" <?= ($horoscope['visibility']??'members')===$v?'selected':'' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<div class="d-flex justify-content-end">
  <button type="submit" class="btn-pink" style="padding:.65rem 2rem"><i class="bi bi-shield-check me-1"></i> Save Privacy Settings</button>
</div>
</form>
</div>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">

<style>
.tab-pane{animation:fadeIn .2s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
</style>

<script>
function showTab(id) {
  document.querySelectorAll('.tab-pane').forEach(p => p.style.display='none');
  document.querySelectorAll('#editTabs button').forEach(b => {
    b.style.color='var(--muted)';
    b.style.borderBottomColor='transparent';
  });
  document.getElementById('pane-'+id).style.display='block';
  const btn = document.getElementById('tab-'+id);
  btn.style.color='var(--pink)';
  btn.style.borderBottomColor='var(--pink)';
  location.hash = id;
}

// Init tab from URL hash or default to basic
const hash = location.hash.replace('#','');
showTab(['basic','family','horoscope','photos','partner','privacy'].includes(hash) ? hash : 'basic');

// Photo upload
const csrf = document.getElementById('csrfToken').value;
function uploadFiles(files) { [...files].forEach(uploadSingle); }
function handleDrop(e) { e.preventDefault(); uploadFiles(e.dataTransfer.files); }
function uploadSingle(file) {
  const fd = new FormData(); fd.append('_csrf', csrf); fd.append('photo', file);
  document.getElementById('uploadStatus').textContent = 'Uploading '+file.name+'...';
  fetch('<?= APP_URL ?>/profile/photos/upload', {method:'POST',body:fd})
    .then(r=>r.json()).then(d=>{
      if(d.success){
        document.getElementById('uploadStatus').textContent = '✓ Uploaded! Awaiting review.';
        const grid = document.getElementById('photoGrid');
        const div = document.createElement('div');
        div.className='col-6 col-md-4 col-lg-3'; div.id='photo-'+d.photo_id;
        div.innerHTML=`<div class="mat-card overflow-hidden"><div style="height:150px;overflow:hidden;background:var(--pink-pale)"><img src="${d.path}" style="width:100%;height:100%;object-fit:cover"></div><div class="p-2 d-flex align-items-center justify-content-between gap-1"><button onclick="setPrimary(${d.photo_id})" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.68rem;padding:.18rem .5rem">Set Primary</button><span style="font-size:.65rem;background:#FEF3C7;color:#92400E;padding:.18rem .5rem;border-radius:50px">Pending</span><button onclick="deletePhoto(${d.photo_id})" class="btn btn-link btn-sm text-danger p-0"><i class="bi bi-trash3"></i></button></div></div>`;
        grid.appendChild(div);
      } else { document.getElementById('uploadStatus').textContent = '✗ '+d.message; }
    });
}
function deletePhoto(id) {
  if(!confirm('Delete this photo?')) return;
  fetch('<?= APP_URL ?>/profile/photos/delete',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`_csrf=${csrf}&photo_id=${id}`})
    .then(r=>r.json()).then(d=>{ if(d.success) document.getElementById('photo-'+id)?.remove(); });
}
function setPrimary(id) {
  fetch('<?= APP_URL ?>/profile/photos/primary',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`_csrf=${csrf}&photo_id=${id}`})
    .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
