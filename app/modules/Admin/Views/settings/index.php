<?php $pageTitle = 'Settings'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2 text-secondary"></i>Site Settings</h5>
</div>

<?php if(isset($flash) && $flash): ?>
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
  <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/admin/settings/save">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">

  <div class="row g-3">
  <?php
  $groupIcons = ['general'=>'bi-globe','auth'=>'bi-shield-lock','limits'=>'bi-sliders','media'=>'bi-image','email'=>'bi-envelope'];
  foreach($settings as $group => $rows): ?>
  <div class="col-lg-6">
    <div class="card table-card h-100">
      <div class="card-header bg-white border-0 pt-3 px-4">
        <h6 class="mb-0 fw-bold text-capitalize">
          <i class="bi <?= $groupIcons[$group]??'bi-gear' ?> me-2 text-primary"></i><?= ucfirst($group) ?> Settings
        </h6>
      </div>
      <div class="card-body px-4">
        <?php foreach($rows as $s): ?>
        <div class="mb-3">
          <label class="form-label small fw-semibold mb-1"><?= htmlspecialchars($s['label']??$s['key']) ?></label>
          <?php if($s['type']==='boolean'): ?>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="<?= $s['key'] ?>" value="1" <?= $s['value']?'checked':'' ?>>
          </div>
          <?php else: ?>
          <input type="<?= $s['type']==='integer'?'number':'text' ?>" name="<?= $s['key'] ?>"
            class="form-control form-control-sm" value="<?= htmlspecialchars($s['value']??'') ?>">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  </div>

  <div class="mt-4">
    <button type="submit" class="btn btn-primary px-4">
      <i class="bi bi-floppy me-2"></i>Save All Settings
    </button>
  </div>
</form>
