<?php $currentStep = 4;
$steps=[['label'=>'Basic Info','step'=>1],['label'=>'Family','step'=>2],['label'=>'Horoscope','step'=>3],['label'=>'Photos','step'=>4]];
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
  <div class="section-tag">Step 4 of 4</div>
  <h1 style="font-size:1.3rem">Upload Photos</h1>
  <p style="color:var(--muted);font-size:.85rem;margin-top:.3rem">Profiles with photos get 10× more responses. Up to <?= $maxPhotos ?> photos allowed.</p>
</div>

<!-- Upload Area -->
<div class="mat-card p-4 mb-4">
  <div id="dropZone"
       style="border:2.5px dashed var(--pink);border-radius:var(--radius);padding:2.5rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--pink-pale)"
       onclick="document.getElementById('photoInput').click()"
       ondragover="event.preventDefault();this.style.background='#FBCFE8'"
       ondragleave="this.style.background='var(--pink-pale)'"
       ondrop="handleDrop(event)">
    <i class="bi bi-cloud-arrow-up" style="font-size:2.5rem;color:var(--pink)"></i>
    <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin:.5rem 0 .2rem">Drag & Drop or Click to Upload</h5>
    <p style="color:var(--muted);font-size:.82rem">JPEG, PNG, WEBP — Max <?= $maxPhotos ?> photos, 5MB each</p>
    <div id="uploadProgress" style="display:none;margin-top:.75rem">
      <div style="height:6px;background:var(--border);border-radius:6px;overflow:hidden">
        <div id="progressBar" style="height:100%;width:0;background:linear-gradient(90deg,var(--pink),var(--green-light));transition:width .3s"></div>
      </div>
      <small class="text-muted" id="uploadStatus">Uploading...</small>
    </div>
  </div>
  <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="uploadFiles(this.files)" multiple>
</div>

<!-- Photo Grid -->
<div class="row g-3 mb-4" id="photoGrid">
  <?php foreach ($photos as $photo): ?>
  <div class="col-6 col-md-4 col-lg-3" id="photo-<?= $photo['id'] ?>">
    <div class="mat-card overflow-hidden h-100" style="position:relative">
      <div style="height:160px;overflow:hidden;background:var(--pink-pale)">
        <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($photo['file_path']) ?>"
             style="width:100%;height:100%;object-fit:cover" alt="">
      </div>
      <div class="p-2 d-flex align-items-center justify-content-between gap-1">
        <?php if ($photo['is_primary']): ?>
          <span class="badge-green" style="font-size:.68rem"><i class="bi bi-star-fill me-1"></i>Primary</span>
        <?php else: ?>
          <button onclick="setPrimary(<?= $photo['id'] ?>,this)" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.7rem;padding:.18rem .5rem">Set Primary</button>
        <?php endif; ?>
        <span class="badge" style="font-size:.65rem;background:<?= ['pending'=>'#FEF3C7','approved'=>'#D1FAE5','rejected'=>'#FEE2E2'][$photo['is_approved']] ?>;color:<?= ['pending'=>'#92400E','approved'=>'#065F46','rejected'=>'#991B1B'][$photo['is_approved']] ?>">
          <?= ucfirst($photo['is_approved']) ?>
        </span>
        <button onclick="deletePhoto(<?= $photo['id'] ?>)" class="btn btn-sm btn-link text-danger p-0" title="Delete">
          <i class="bi bi-trash3"></i>
        </button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div style="font-size:.8rem;color:var(--muted);margin-bottom:1.5rem">
  <i class="bi bi-info-circle me-1"></i>
  Photos are reviewed by our team before being visible to others. Primary photo appears in search results.
</div>

<div class="d-flex justify-content-between">
  <a href="<?= APP_URL ?>/profile/create/horoscope" class="btn-outline-pink"><i class="bi bi-arrow-left me-1"></i> Back</a>
  <a href="<?= APP_URL ?>/dashboard" class="btn-green">
    <i class="bi bi-check-circle me-1"></i> Finish & Go to Dashboard
  </a>
</div>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
const csrf = document.getElementById('csrfToken').value;
const maxPhotos = <?= $maxPhotos ?>;

function uploadFiles(files) {
  [...files].forEach(f => uploadSingle(f));
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.background = 'var(--pink-pale)';
  uploadFiles(e.dataTransfer.files);
}

function uploadSingle(file) {
  const prog = document.getElementById('uploadProgress');
  const bar  = document.getElementById('progressBar');
  const stat = document.getElementById('uploadStatus');
  prog.style.display = 'block';
  stat.textContent = 'Uploading ' + file.name + '...';
  bar.style.width = '30%';

  const fd = new FormData();
  fd.append('_csrf', csrf);
  fd.append('photo', file);

  fetch('<?= APP_URL ?>/profile/photos/upload', { method:'POST', body:fd })
    .then(r=>r.json())
    .then(d=>{
      bar.style.width = '100%';
      if (d.success) {
        stat.textContent = '✓ Uploaded! Pending review.';
        stat.style.color = 'var(--green)';
        addPhotoCard(d.photo_id, d.path);
      } else {
        stat.textContent = '✗ ' + d.message;
        stat.style.color = '#EF4444';
      }
      setTimeout(()=>{ prog.style.display='none'; bar.style.width='0'; stat.style.color=''; }, 2500);
    }).catch(()=>{ stat.textContent='Upload failed. Try again.'; });
}

function addPhotoCard(id, path) {
  const grid = document.getElementById('photoGrid');
  const div  = document.createElement('div');
  div.className = 'col-6 col-md-4 col-lg-3';
  div.id = 'photo-'+id;
  div.innerHTML = `
    <div class="mat-card overflow-hidden h-100" style="position:relative">
      <div style="height:160px;overflow:hidden;background:var(--pink-pale)">
        <img src="${path}" style="width:100%;height:100%;object-fit:cover">
      </div>
      <div class="p-2 d-flex align-items-center justify-content-between gap-1">
        <button onclick="setPrimary(${id},this)" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.7rem;padding:.18rem .5rem">Set Primary</button>
        <span class="badge" style="font-size:.65rem;background:#FEF3C7;color:#92400E">Pending</span>
        <button onclick="deletePhoto(${id})" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash3"></i></button>
      </div>
    </div>`;
  grid.appendChild(div);
}

function deletePhoto(id) {
  if (!confirm('Delete this photo?')) return;
  fetch('<?= APP_URL ?>/profile/photos/delete', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrf}&photo_id=${id}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) document.getElementById('photo-'+id)?.remove();
  });
}

function setPrimary(id, btn) {
  fetch('<?= APP_URL ?>/profile/photos/primary', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${csrf}&photo_id=${id}`
  }).then(r=>r.json()).then(d=>{
    if (d.success) location.reload();
  });
}
</script>
