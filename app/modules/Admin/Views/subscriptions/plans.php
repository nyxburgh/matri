<?php $pageTitle = 'Subscription Plans'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-layers me-2 text-primary"></i>Subscription Plans</h5>
  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#planModal" data-id="0">
    <i class="bi bi-plus-lg me-1"></i>New Plan
  </button>
</div>

<?php if(isset($flash)&&$flash): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3">
<?php foreach($plans as $pl): ?>
<div class="col-md-6 col-xl-3">
  <div class="card table-card h-100">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 px-4">
      <div>
        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($pl['name']) ?></h6>
        <small class="text-muted font-monospace"><?= $pl['code'] ?></small>
      </div>
      <span class="badge <?= $pl['is_active']?'bg-success':'bg-secondary' ?> rounded-pill"><?= $pl['is_active']?'Active':'Inactive' ?></span>
    </div>
    <div class="card-body px-4">
      <div class="fs-4 fw-bold text-primary mb-3">₹<?= number_format($pl['price'],2) ?>
        <small class="fs-6 text-muted fw-normal">/<?= $pl['duration_days'] ?> days</small>
      </div>
      <?php $features=[
        ['Interests',     $pl['interests_limit']==-1?'Unlimited':$pl['interests_limit'], 'bi-heart'],
        ['Chat',          $pl['chat_limit']?'Enabled':'No',     'bi-chat'],
        ['Contact View',  $pl['contact_view']?'Yes':'No',       'bi-telephone'],
        ['Adv. Search',   $pl['advanced_search']?'Yes':'No',    'bi-funnel'],
        ['Highlighted',   $pl['highlight']?'Yes':'No',          'bi-star'],
        ['Photo Request', $pl['photo_request']?'Yes':'No',      'bi-images'],
      ];
      foreach($features as [$label,$val,$icon]): ?>
      <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:.82rem">
        <span class="text-muted"><i class="bi <?= $icon ?> me-2"></i><?= $label ?></span>
        <span class="fw-semibold <?= in_array($val,['Yes','Enabled','Unlimited'])?'text-success':'text-secondary' ?>"><?= $val ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="card-footer bg-white border-0 pb-3 px-4">
      <button class="btn btn-sm btn-outline-primary w-100"
        data-bs-toggle="modal" data-bs-target="#planModal"
        data-id="<?= $pl['id'] ?>"
        data-name="<?= htmlspecialchars($pl['name']) ?>"
        data-code="<?= $pl['code'] ?>"
        data-days="<?= $pl['duration_days'] ?>"
        data-price="<?= $pl['price'] ?>"
        data-int="<?= $pl['interests_limit'] ?>"
        data-chat="<?= $pl['chat_limit'] ?>"
        data-contact="<?= $pl['contact_view'] ?>"
        data-search="<?= $pl['advanced_search'] ?>"
        data-highlight="<?= $pl['highlight'] ?>"
        data-photo="<?= $pl['photo_request'] ?>"
        data-active="<?= $pl['is_active'] ?>"
        data-sort="<?= $pl['sort_order'] ?>">
        <i class="bi bi-pencil me-1"></i>Edit Plan
      </button>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>

<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Subscription Plan</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="<?= APP_URL ?>/admin/plans/save">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="planId" value="0">
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Plan Name *</label><input type="text" name="name" id="planName" class="form-control form-control-sm" required></div>
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Code *</label><input type="text" name="code" id="planCode" class="form-control form-control-sm" required></div>
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Duration (days)</label><input type="number" name="duration_days" id="planDays" class="form-control form-control-sm" min="0"></div>
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Price (₹)</label><input type="number" step="0.01" name="price" id="planPrice" class="form-control form-control-sm" min="0"></div>
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Interests Limit (-1=∞)</label><input type="number" name="interests_limit" id="planInt" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label form-label-sm fw-semibold">Sort Order</label><input type="number" name="sort_order" id="planSort" class="form-control form-control-sm"></div>
            <?php foreach([
              ['chat_limit','Chat Enabled','planChat'],['contact_view','Contact View','planContact'],
              ['advanced_search','Advanced Search','planSearch'],['highlight','Profile Highlight','planHighlight'],
              ['photo_request','Photo Request','planPhoto'],['is_active','Active','planActive']
            ] as [$fname,$flabel,$fid]): ?>
            <div class="col-6">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="<?= $fname ?>" id="<?= $fid ?>" value="1">
                <label class="form-check-label small" for="<?= $fid ?>"><?= $flabel ?></label>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary">Save Plan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('planModal')?.addEventListener('show.bs.modal', function(e){
  const d = e.relatedTarget?.dataset || {};
  this.querySelector('#planId').value     = d.id || '0';
  this.querySelector('#planName').value   = d.name || '';
  this.querySelector('#planCode').value   = d.code || '';
  this.querySelector('#planDays').value   = d.days || '0';
  this.querySelector('#planPrice').value  = d.price || '0';
  this.querySelector('#planInt').value    = d.int || '5';
  this.querySelector('#planSort').value   = d.sort || '0';
  this.querySelector('#planChat').checked     = d.chat === '1';
  this.querySelector('#planContact').checked  = d.contact === '1';
  this.querySelector('#planSearch').checked   = d.search === '1';
  this.querySelector('#planHighlight').checked= d.highlight === '1';
  this.querySelector('#planPhoto').checked    = d.photo === '1';
  this.querySelector('#planActive').checked   = d.active === '1';
});
</script>
