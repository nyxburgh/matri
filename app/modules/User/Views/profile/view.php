<?php
$age       = $user['age'] ?? null;
$isOnline  = !empty($user['last_active']) && strtotime($user['last_active']) > time() - 300;
$primaryPhoto = null;
foreach ($photos as $ph) { if ($ph['is_primary']) { $primaryPhoto = $ph; break; } }
if (!$primaryPhoto && !empty($photos)) $primaryPhoto = $photos[0];
?>

<div class="row g-4">
  <!-- Left: Photo + Actions -->
  <div class="col-12 col-lg-4">
    <!-- Main Photo -->
    <div class="mat-card overflow-hidden mb-3" style="border-radius:var(--radius-lg)">
      <div style="position:relative;height:340px;background:var(--pink-pale)">
        <?php if ($primaryPhoto): ?>
          <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($primaryPhoto['file_path']) ?>"
               style="width:100%;height:100%;object-fit:cover" alt="<?= htmlspecialchars($user['name']) ?>" id="mainPhoto">
        <?php else: ?>
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:6rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
        <?php endif; ?>
        <?php if (!empty($user['is_highlighted'])): ?>
          <div style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;font-size:.72rem;font-weight:700;padding:.25rem .7rem;border-radius:50px">
            <i class="bi bi-gem me-1"></i> Premium
          </div>
        <?php endif; ?>
        <div style="position:absolute;top:12px;right:12px;background:<?= $isOnline?'var(--green)':'rgba(0,0,0,.5)' ?>;color:#fff;font-size:.72rem;font-weight:600;padding:.25rem .7rem;border-radius:50px">
          <?= $isOnline ? '● Online' : 'Offline' ?>
        </div>
      </div>
      <!-- Photo Thumbnails -->
      <?php if (count($photos) > 1): ?>
      <div class="d-flex gap-2 p-2 overflow-auto" style="scrollbar-width:none">
        <?php foreach ($photos as $ph): ?>
        <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($ph['file_path']) ?>"
             onclick="document.getElementById('mainPhoto').src=this.src"
             style="width:52px;height:52px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid <?= $ph['is_primary']?'var(--pink)':'var(--border)' ?>;flex-shrink:0" alt="">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <?php if (!$isOwner): ?>
    <div class="mat-card p-3 mb-3">
      <div class="d-grid gap-2">
        <?php if ($interestStatus === 'accepted'): ?>
          <a href="<?= APP_URL ?>/chat/<?= $user['id'] ?>" class="btn-green d-flex align-items-center justify-content-center gap-2" style="padding:.65rem">
            <i class="bi bi-chat-dots-fill"></i> Send Message
          </a>
        <?php elseif ($interestStatus === 'pending'): ?>
          <button class="btn-pink" style="padding:.65rem;opacity:.7" disabled>
            <i class="bi bi-clock me-1"></i> Interest Sent — Awaiting Response
          </button>
        <?php elseif ($interestStatus === 'rejected'): ?>
          <button class="btn-outline-pink" style="padding:.65rem;opacity:.6" disabled>
            <i class="bi bi-x-circle me-1"></i> Interest Declined
          </button>
        <?php else: ?>
          <button id="interestBtn" onclick="sendInterest(<?= $user['id'] ?>)" class="btn-pink d-flex align-items-center justify-content-center gap-2" style="padding:.65rem">
            <i class="bi bi-heart-fill"></i> Send Interest
          </button>
        <?php endif; ?>

        <div class="d-flex gap-2">
          <button id="shortlistBtn" onclick="toggleShortlist(<?= $user['id'] ?>)" class="flex-grow-1 <?= $isShortlisted?'btn-outline-green':'btn-outline-pink' ?> d-flex align-items-center justify-content-center gap-1" style="font-size:.85rem;padding:.5rem">
            <i class="bi bi-bookmark<?= $isShortlisted?'-fill':'' ?>"></i>
            <?= $isShortlisted ? 'Shortlisted' : 'Shortlist' ?>
          </button>
          <button type="button" class="btn btn-outline-secondary rounded-pill" style="font-size:.85rem;padding:.5rem .9rem" data-bs-toggle="modal" data-bs-target="#reportModal" title="Report">
            <i class="bi bi-flag"></i>
          </button>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="mat-card p-3 mb-3">
      <div class="d-grid gap-2">
        <a href="<?= APP_URL ?>/profile/edit" class="btn-pink d-flex align-items-center justify-content-center gap-2" style="padding:.65rem">
          <i class="bi bi-pencil-square"></i> Edit Profile
        </a>
        <a href="<?= APP_URL ?>/profile/create/photos" class="btn-outline-green d-flex align-items-center justify-content-center gap-2" style="font-size:.85rem;padding:.5rem">
          <i class="bi bi-images"></i> Manage Photos
        </a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Profile Completeness (own profile) -->
    <?php if ($isOwner):
      $fields = ['dob','religion','education','occupation','city','about_me','caste'];
      $filled = count(array_filter($fields, fn($f) => !empty($user[$f])));
      $pct = (int)(($filled/count($fields))*100);
    ?>
    <div class="mat-card p-3 mb-3">
      <div class="d-flex justify-content-between mb-1">
        <span style="font-size:.82rem;font-weight:700">Profile Completeness</span>
        <span style="font-size:.82rem;color:var(--pink)"><?= $pct ?>%</span>
      </div>
      <div style="height:6px;background:var(--border);border-radius:6px;overflow:hidden">
        <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,var(--pink),var(--green-light));border-radius:6px"></div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right: Profile Details -->
  <div class="col-12 col-lg-8">
    <!-- Header -->
    <div class="mat-card p-4 mb-3">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <h1 style="font-size:1.5rem;color:var(--dark);margin-bottom:.3rem"><?= htmlspecialchars($user['name']) ?></h1>
          <div style="color:var(--muted);font-size:.9rem">
            <?= $age ? $age.' years' : '' ?>
            <?= !empty($user['city']) ? ' · '.htmlspecialchars($user['city']) : '' ?>
            <?= !empty($user['state']) ? ', '.htmlspecialchars($user['state']) : '' ?>
          </div>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <span class="badge-pink"><?= htmlspecialchars($user['profile_id']) ?></span>
            <?php if (!empty($user['marital_status']) && $user['marital_status'] !== 'never_married'): ?>
            <span class="badge-gold"><?= ucwords(str_replace('_',' ',$user['marital_status'])) ?></span>
            <?php endif; ?>
            <?php if (!empty($user['admin_approved']) && $user['admin_approved'] === 'approved'): ?>
            <span class="badge-green"><i class="bi bi-shield-check me-1"></i>Verified</span>
            <?php endif; ?>
            <?php if ($user['email_verified']): ?><span class="badge-green"><i class="bi bi-envelope-check me-1"></i>Email</span><?php endif; ?>
          </div>
        </div>
        <div style="text-align:right">
          <div style="font-size:.78rem;color:var(--muted)">Member since</div>
          <div style="font-weight:700;font-size:.88rem"><?= date('M Y', strtotime($user['created_at'])) ?></div>
        </div>
      </div>
      <?php if (!empty($user['about_me'])): ?>
      <p style="color:var(--text);font-size:.9rem;line-height:1.65;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
        <?= nl2br(htmlspecialchars($user['about_me'])) ?>
      </p>
      <?php endif; ?>
    </div>

    <!-- Basic Details -->
    <div class="mat-card p-4 mb-3">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-person-badge me-2" style="color:var(--pink)"></i>Basic Details</h5>
      <div class="row g-2">
        <?php
        $details = [
          ['label'=>'Age',           'val'=>$age?$age.' years':''],
          ['label'=>'Height',        'val'=>!empty($user['height_cm'])?$user['height_cm'].' cm':''],
          ['label'=>'Complexion',    'val'=>ucfirst(str_replace('_',' ',$user['complexion']??''))],
          ['label'=>'Body Type',     'val'=>ucfirst($user['body_type']??'')],
          ['label'=>'Mother Tongue', 'val'=>$user['mother_tongue']??''],
          ['label'=>'Diet',          'val'=>ucfirst(str_replace('_',' ',$user['diet']??''))],
          ['label'=>'Smoking',       'val'=>ucfirst($user['smoking']??'')],
          ['label'=>'Drinking',      'val'=>ucfirst($user['drinking']??'')],
        ];
        foreach ($details as $d): if (empty($d['val'])) continue; ?>
        <div class="col-6 col-md-4">
          <div style="font-size:.75rem;color:var(--muted)"><?= $d['label'] ?></div>
          <div style="font-size:.88rem;font-weight:600;color:var(--dark)"><?= htmlspecialchars($d['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Religion & Community -->
    <div class="mat-card p-4 mb-3">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-globe-asia-australia me-2" style="color:var(--green)"></i>Religion & Community</h5>
      <div class="row g-2">
        <?php
        $rel = [
          ['label'=>'Religion',  'val'=>$user['religion']??''],
          ['label'=>'Caste',     'val'=>$user['caste']??''],
          ['label'=>'Sub-Caste', 'val'=>$user['sub_caste']??''],
          ['label'=>'Gothram',   'val'=>$user['gothram']??''],
          ['label'=>'Star',      'val'=>$user['star']??''],
          ['label'=>'Raasi',     'val'=>$user['raasi']??''],
          ['label'=>'Dhosam',    'val'=>!empty($user['dhosam'])&&$user['dhosam']!=='none'?ucwords(str_replace('_',' ',$user['dhosam'])):'None'],
        ];
        foreach ($rel as $d): if (empty($d['val'])) continue; ?>
        <div class="col-6 col-md-4">
          <div style="font-size:.75rem;color:var(--muted)"><?= $d['label'] ?></div>
          <div style="font-size:.88rem;font-weight:600;color:var(--dark)"><?= htmlspecialchars($d['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Education & Career -->
    <div class="mat-card p-4 mb-3">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-mortarboard me-2" style="color:var(--pink)"></i>Education & Career</h5>
      <div class="row g-2">
        <?php
        $edu = [
          ['label'=>'Education',     'val'=>$user['education']??''],
          ['label'=>'Institute',     'val'=>$user['education_detail']??''],
          ['label'=>'Employed In',   'val'=>ucfirst(str_replace('_',' ',$user['employed_in']??''))],
          ['label'=>'Occupation',    'val'=>$user['occupation']??''],
          ['label'=>'Annual Income', 'val'=>str_replace(['_','1l','3l','5l','10l','25l'],['‑','₹1','₹3','₹5','₹10','₹25'],$user['annual_income']??'')],
        ];
        foreach ($edu as $d): if (empty($d['val'])) continue; ?>
        <div class="col-6 col-md-4">
          <div style="font-size:.75rem;color:var(--muted)"><?= $d['label'] ?></div>
          <div style="font-size:.88rem;font-weight:600;color:var(--dark)"><?= htmlspecialchars($d['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Family Details -->
    <?php if ($family): ?>
    <div class="mat-card p-4 mb-3">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-house-heart me-2" style="color:var(--green)"></i>Family Details</h5>
      <div class="row g-2">
        <?php
        $fam = [
          ['label'=>'Father',        'val'=>trim(($family['father_name']??'').' '.ucfirst(str_replace('_',' ',$family['father_status']??'')))],
          ['label'=>'Mother',        'val'=>trim(($family['mother_name']??'').' '.ucfirst(str_replace('_',' ',$family['mother_status']??'')))],
          ['label'=>'Brothers',      'val'=>$family['brothers']>0?$family['brothers'].' ('.$family['brothers_married'].' married)':''],
          ['label'=>'Sisters',       'val'=>$family['sisters']>0?$family['sisters'].' ('.$family['sisters_married'].' married)':''],
          ['label'=>'Family Type',   'val'=>ucfirst(str_replace('_',' ',$family['family_type']??''))],
          ['label'=>'Family Status', 'val'=>ucfirst(str_replace('_',' ',$family['family_status']??''))],
          ['label'=>'Family Values', 'val'=>ucfirst($family['family_values']??'')],
          ['label'=>'Native Place',  'val'=>$family['native_place']??''],
        ];
        foreach ($fam as $d): if (empty(trim($d['val']))) continue; ?>
        <div class="col-6 col-md-4">
          <div style="font-size:.75rem;color:var(--muted)"><?= $d['label'] ?></div>
          <div style="font-size:.88rem;font-weight:600;color:var(--dark)"><?= htmlspecialchars($d['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Horoscope (if visible) -->
    <?php if ($horoscope && ($horoscope['visibility'] === 'public' || ($horoscope['visibility'] === 'members' && Session::has('user_id')))): ?>
    <div class="mat-card p-4 mb-3">
      <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem"><i class="bi bi-stars me-2" style="color:#F57F17"></i>Horoscope Details</h5>
      <div class="row g-2 mb-3">
        <?php
        $hor = [
          ['label'=>'Birth Date',    'val'=>$horoscope['birth_date']?date('d M Y',strtotime($horoscope['birth_date'])):''],
          ['label'=>'Birth Time',    'val'=>$horoscope['birth_time']?date('g:i a',strtotime($horoscope['birth_time'])):''],
          ['label'=>'Birth Place',   'val'=>$horoscope['birth_place']??''],
          ['label'=>'Lagna',         'val'=>$horoscope['lagna']??''],
          ['label'=>'Rasi',          'val'=>$horoscope['rasi']??''],
          ['label'=>'Nakshatra',     'val'=>$horoscope['nakshatra']??''],
          ['label'=>'Chevvai',       'val'=>$horoscope['chevvai_dhosam']?'Yes':'No'],
          ['label'=>'Rahu Dhosam',   'val'=>$horoscope['rahu_dhosam']?'Yes':'No'],
          ['label'=>'Kala Sarpa',    'val'=>$horoscope['kala_sarpa']?'Yes':'No'],
        ];
        foreach ($hor as $d): if (empty($d['val'])) continue; ?>
        <div class="col-6 col-md-3">
          <div style="font-size:.75rem;color:var(--muted)"><?= $d['label'] ?></div>
          <div style="font-size:.88rem;font-weight:600;color:var(--dark)"><?= htmlspecialchars($d['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<!-- Report Modal -->
<?php if (!$isOwner): ?>
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none">
      <div class="modal-header border-0"><h5 class="modal-title" style="color:#991B1B"><i class="bi bi-flag me-2"></i>Report Profile</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Reason</label>
          <select id="reportReason" class="form-select">
            <?php foreach (['fake_profile'=>'Fake Profile','abusive'=>'Abusive Behaviour','spam'=>'Spam','inappropriate_photo'=>'Inappropriate Photo','other'=>'Other'] as $v=>$l): ?>
            <option value="<?= $v ?>"><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Description (optional)</label>
          <textarea id="reportDesc" class="form-control" rows="3" maxlength="500" placeholder="Describe the issue..."></textarea>
        </div>
        <div id="reportMsg" style="display:none" class="flash-success mb-2"></div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger flex-grow-1 rounded-pill" onclick="submitReport()">Submit Report</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
const csrf = document.getElementById('csrfToken').value;

<?php if (!$isOwner): ?>
function sendInterest(uid) {
  const btn = document.getElementById('interestBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sending...';
  fetch('<?= APP_URL ?>/interest/send', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrf}&receiver_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) {
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Interest Sent!';
      btn.style.opacity = '.7';
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-heart-fill"></i> Send Interest';
      alert(d.message);
    }
  });
}

function toggleShortlist(uid) {
  const btn = document.getElementById('shortlistBtn');
  fetch('<?= APP_URL ?>/profile/shortlist', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrf}&target_id=${uid}`
  }).then(r=>r.json()).then(d=>{
    const added = d.action === 'added';
    btn.className = `flex-grow-1 ${added?'btn-outline-green':'btn-outline-pink'} d-flex align-items-center justify-content-center gap-1`;
    btn.innerHTML = `<i class="bi bi-bookmark${added?'-fill':''}"></i> ${added?'Shortlisted':'Shortlist'}`;
    btn.style.fontSize = '.85rem'; btn.style.padding = '.5rem';
  });
}

function submitReport() {
  fetch('<?= APP_URL ?>/profile/report', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrf}&reported_id=<?= $user['id'] ?>&reason=${document.getElementById('reportReason').value}&description=${encodeURIComponent(document.getElementById('reportDesc').value)}`
  }).then(r=>r.json()).then(d=>{
    const msg = document.getElementById('reportMsg');
    msg.style.display = 'flex';
    msg.className = d.success ? 'flash-success mb-2' : 'flash-error mb-2';
    msg.innerHTML = (d.success?'<i class="bi bi-check-circle-fill"></i>':'<i class="bi bi-exclamation-circle-fill"></i>') + ' ' + d.message;
    if (d.success) setTimeout(()=>bootstrap.Modal.getInstance(document.getElementById('reportModal'))?.hide(), 2000);
  });
}
<?php endif; ?>
</script>
