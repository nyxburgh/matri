<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php $pageTitle = 'Reports'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-flag me-2 text-danger"></i>Report Management</h5>
  <span class="badge bg-danger rounded-pill"><?= number_format($total) ?> reports</span>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-4">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Reporter or reported name…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-6 col-md-2">
      <select name="status" class="form-select form-select-sm">
        <option value="open"         <?= $filters['status']==='open'        ?'selected':'' ?>>Open</option>
        <option value="reviewed"     <?= $filters['status']==='reviewed'    ?'selected':'' ?>>Reviewed</option>
        <option value="action_taken" <?= $filters['status']==='action_taken'?'selected':'' ?>>Action Taken</option>
        <option value="dismissed"    <?= $filters['status']==='dismissed'   ?'selected':'' ?>>Dismissed</option>
        <option value="">All</option>
      </select>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/reports" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">#</th><th>Reporter</th><th>Reported</th><th>Reason</th><th>Status</th><th>Date</th><th class="text-center">Action</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No reports found.</td></tr>
        <?php else: foreach($data as $r): ?>
        <tr>
          <td class="ps-4 text-muted" style="font-size:.8rem">#<?= $r['id'] ?></td>
          <td>
            <div style="font-size:.84rem"><?= htmlspecialchars($r['reporter_name']) ?></div>
            <small class="text-muted"><?= $r['reporter_pid'] ?></small>
          </td>
          <td>
            <div style="font-size:.84rem"><?= htmlspecialchars($r['reported_name']) ?></div>
            <small class="text-muted"><?= $r['reported_pid'] ?></small>
          </td>
          <td><span class="badge bg-secondary-subtle text-secondary" style="font-size:.75rem"><?= ucwords(str_replace('_',' ',$r['reason'])) ?></span></td>
          <td>
            <?php $sc=['open'=>'danger','reviewed'=>'warning','action_taken'=>'success','dismissed'=>'secondary']; ?>
            <span class="badge bg-<?= $sc[$r['status']]??'secondary' ?> rounded-pill" style="font-size:.72rem"><?= ucwords(str_replace('_',' ',$r['status'])) ?></span>
          </td>
          <td><small class="text-muted"><?= date('d M y',strtotime($r['created_at'])) ?></small></td>
          <td class="text-center">
            <button class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:.75rem"
              data-bs-toggle="modal" data-bs-target="#actionModal"
              data-id="<?= $r['id'] ?>" data-status="<?= $r['status'] ?>"><i class="bi bi-pencil"></i></button>
          </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Update Report Status</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="<?= APP_URL ?>/admin/reports/action">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="reportId">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Status</label>
            <select name="action" class="form-select form-select-sm" id="reportStatus">
              <option value="reviewed">Reviewed</option>
              <option value="action_taken">Action Taken</option>
              <option value="dismissed">Dismissed</option>
            </select>
          </div>
          <div class="mb-0">
            <label class="form-label small fw-semibold">Admin Note</label>
            <textarea name="admin_note" class="form-control form-control-sm" rows="3" placeholder="Internal note…"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('actionModal')?.addEventListener('show.bs.modal', function(e){
  this.querySelector('#reportId').value = e.relatedTarget.dataset.id;
  this.querySelector('#reportStatus').value = e.relatedTarget.dataset.status;
});
</script>
