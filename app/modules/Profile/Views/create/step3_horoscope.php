<?php $currentStep = 3;
$steps=[['label'=>'Basic Info','step'=>1],['label'=>'Family','step'=>2],['label'=>'Horoscope','step'=>3],['label'=>'Photos','step'=>4]];
$planets = ['Su'=>'Sun','Mo'=>'Moon','Ma'=>'Mars','Me'=>'Mercury','Ju'=>'Jupiter','Ve'=>'Venus','Sa'=>'Saturn','Ra'=>'Rahu','Ke'=>'Ketu','La'=>'Lagna'];

// Decode existing chart JSON
$rasiData    = !empty($horoscope['rasi_chart'])    ? json_decode($horoscope['rasi_chart'],    true) : [];
$navamsaData = !empty($horoscope['navamsa_chart']) ? json_decode($horoscope['navamsa_chart'], true) : [];
?>
<!-- Wizard Steps -->
<div class="mat-card p-3 mb-4">
  <div class="d-flex align-items-center justify-content-between position-relative">
    <div style="position:absolute;top:50%;left:12%;right:12%;height:2px;background:var(--border);z-index:0"></div>
    <?php foreach ($steps as $s): ?>
    <div class="d-flex flex-column align-items-center gap-1 position-relative" style="z-index:1;flex:1">
      <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;
        <?= $s['step']<$currentStep?'background:var(--green);color:#fff':($s['step']==$currentStep?'background:var(--pink);color:#fff':'background:var(--border);color:var(--muted)') ?>">
        <i class="<?= $s['step']<$currentStep?'bi bi-check2-circle':['bi-person-fill','bi-house-heart-fill','bi-stars','bi-images'][$s['step']-1] ?>"></i>
      </div>
      <span style="font-size:.68rem;font-weight:<?= $s['step']==$currentStep?700:500 ?>;color:<?= $s['step']==$currentStep?'var(--pink)':'var(--muted)' ?>;white-space:nowrap"><?= $s['label'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="page-header mb-4">
  <div class="section-tag">Step 3 of 4</div>
  <h1 style="font-size:1.3rem">Horoscope (ஜாதக கட்டம்)</h1>
  <p style="color:var(--muted);font-size:.85rem;margin-top:.3rem">Enter your South Indian Rasi & Navamsa chart details.</p>
</div>

<style>
/* South Indian 4×4 grid chart */
.si-chart{display:grid;grid-template-columns:repeat(4,1fr);border:2.5px solid var(--dark);border-radius:8px;overflow:hidden;background:#fff}
.si-house{border:1.5px solid var(--border);min-height:80px;padding:4px 5px;position:relative;font-size:.7rem}
.si-house.center{background:var(--pink-pale);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-weight:700;color:var(--pink);font-size:.9rem;grid-column:span 2;min-height:160px;border:1.5px solid var(--border)}
.si-house .house-num{position:absolute;top:3px;left:5px;font-size:.65rem;font-weight:700;color:var(--muted);opacity:.6}
.si-house select{width:100%;font-size:.7rem;border:1px solid var(--border);border-radius:4px;padding:1px 2px;margin-top:10px;background:#fff}
.planet-tag{display:inline-block;background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border-radius:4px;padding:1px 4px;font-size:.62rem;font-weight:700;margin:1px}
.planet-tag.green{background:linear-gradient(135deg,var(--green),var(--green-light))}
.chart-label{font-weight:700;font-size:.9rem;color:var(--dark);text-align:center;margin-bottom:.5rem}
</style>

<form method="POST" action="<?= APP_URL ?>/profile/horoscope">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">

<!-- Birth Details -->
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem"><i class="bi bi-calendar-event me-2" style="color:var(--pink)"></i>Birth Details</h5>
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <label class="form-label">Birth Date</label>
      <input type="date" name="birth_date" class="form-control" value="<?= $horoscope['birth_date'] ?? '' ?>">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Birth Time</label>
      <input type="time" name="birth_time" class="form-control" value="<?= $horoscope['birth_time'] ?? '' ?>">
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Birth Place</label>
      <input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($horoscope['birth_place'] ?? '') ?>" placeholder="e.g. Chennai, Tamil Nadu">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Lagna (Ascendant)</label>
      <input type="text" name="lagna" class="form-control" value="<?= htmlspecialchars($horoscope['lagna'] ?? '') ?>" placeholder="e.g. Mesham">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Rasi (Moon Sign)</label>
      <input type="text" name="rasi" class="form-control" value="<?= htmlspecialchars($horoscope['rasi'] ?? '') ?>" placeholder="e.g. Rishabam">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Nakshatra (Star)</label>
      <input type="text" name="nakshatra" class="form-control" value="<?= htmlspecialchars($horoscope['nakshatra'] ?? '') ?>" placeholder="e.g. Rohini">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">Nakshatra Pada</label>
      <select name="nakshatra_pada" class="form-select">
        <option value="">-</option>
        <?php for($i=1;$i<=4;$i++): ?>
        <option value="<?= $i ?>" <?= ($horoscope['nakshatra_pada']??'')==$i?'selected':'' ?>><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
  </div>
</div>

<!-- Dosham Flags -->
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-exclamation-circle me-2" style="color:#F57F17"></i>Dosham Flags</h5>
  <div class="d-flex flex-wrap gap-3">
    <?php
    $doshas = [
      ['key'=>'chevvai_dhosam','label'=>'Chevvai Dosham (Mars)'],
      ['key'=>'rahu_dhosam',   'label'=>'Rahu Dosham'],
      ['key'=>'kala_sarpa',    'label'=>'Kala Sarpa Dosham'],
    ];
    foreach ($doshas as $d): ?>
    <div class="form-check form-switch" style="font-size:.88rem">
      <input class="form-check-input" type="checkbox" name="<?= $d['key'] ?>" value="1" id="<?= $d['key'] ?>"
             <?= !empty($horoscope[$d['key']]) ? 'checked' : '' ?>>
      <label class="form-check-label" for="<?= $d['key'] ?>"><?= $d['label'] ?></label>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
  <?php
  // South Indian chart: house positions in a 4×4 grid
  // Layout (by house number):
  // [12][1][2][3]
  // [11][   ][4]
  // [10][   ][5]
  // [9][8][7][6]
  $layoutRasi = [
    [12,1,2,3],
    [11,'c1','c2',4],
    [10,'c3','c4',5],
    [9,8,7,6],
  ];

  foreach (['rasi'=>['data'=>$rasiData,'label'=>'Rasi Chart (ராசி கட்டம்)','color'=>'var(--pink)'],
             'navamsa'=>['data'=>$navamsaData,'label'=>'Navamsa Chart (நவாம்சம்)','color'=>'var(--green)']] as $chartKey=>$chart):
  ?>
  <div class="col-12 col-md-6">
    <div class="mat-card p-3">
      <div class="chart-label" style="color:<?= $chart['color'] ?>"><?= $chart['label'] ?></div>
      <div class="si-chart">
        <?php foreach ($layoutRasi as $row): foreach ($row as $cell):
          if (in_array($cell, ['c1','c2','c3','c4'])) {
            if ($cell === 'c1') echo "<div class='si-house center' style='grid-row:span 2'><span>".APP_NAME."<br><small style='font-size:.6rem;font-weight:400'>".($chartKey==='rasi'?'ராசி':'நவாம்சம்')."</small></span></div>";
            continue;
          }
          $houseVal = $chart['data'][$cell] ?? [];
          if (!is_array($houseVal)) $houseVal = [$houseVal];
          ?>
          <div class="si-house">
            <span class="house-num"><?= $cell ?></span>
            <div class="mt-2">
              <?php foreach (array_keys($planets) as $pKey):
                $checked = in_array($pKey, $houseVal) ? 'selected' : '';
              ?>
              <span class="planet-tag <?= $chartKey==='navamsa'?'green':'' ?>"
                    style="cursor:pointer;opacity:<?= $checked?1:.35 ?>"
                    onclick="togglePlanet('<?= $chartKey ?>',<?= $cell ?>,'<?= $pKey ?>',this)"
                    title="<?= $planets[$pKey] ?>"><?= $pKey ?></span>
              <input type="hidden" name="<?= $chartKey ?>[<?= $cell ?>][]" id="hid_<?= $chartKey ?>_<?= $cell ?>_<?= $pKey ?>" value="" <?= $checked?'':'disabled' ?>>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; endforeach; ?>
      </div>
      <p style="font-size:.72rem;color:var(--muted);margin-top:.5rem;text-align:center">Click planet abbreviations to add/remove from house</p>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Visibility + Horoscope Visibility -->
<div class="mat-card p-4 mb-4">
  <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-eye me-2" style="color:var(--green)"></i>Horoscope Visibility</h5>
  <select name="visibility" class="form-select" style="max-width:260px">
    <?php foreach (['public'=>'Everyone','members'=>'Members Only','private'=>'Private (Only Me)'] as $v=>$l): ?>
    <option value="<?= $v ?>" <?= ($horoscope['visibility']??'members')===$v?'selected':'' ?>><?= $l ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div class="d-flex justify-content-between">
  <a href="<?= APP_URL ?>/profile/create/family" class="btn-outline-pink"><i class="bi bi-arrow-left me-1"></i> Back</a>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/profile/create/photos" class="btn-outline-pink">Skip</a>
    <button type="submit" class="btn-pink">Next: Photos <i class="bi bi-arrow-right ms-1"></i></button>
  </div>
</div>
</form>

<script>
function togglePlanet(chart, house, planet, el) {
  const hidId = `hid_${chart}_${house}_${planet}`;
  const hid = document.getElementById(hidId);
  const active = hid && !hid.disabled;
  if (active) {
    if (hid) hid.disabled = true;
    el.style.opacity = '.35';
  } else {
    if (hid) { hid.disabled = false; hid.value = planet; }
    el.style.opacity = '1';
  }
}
</script>
