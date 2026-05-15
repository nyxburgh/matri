<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php $pageTitle = 'Profiles'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-person-vcard me-2 text-primary"></i>Profile Moderation</h5>
  <span class="badge bg-primary rounded-pill"><?= number_format($total) ?> profiles</span>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-3">
      <label class="form-label form-label-sm mb-1 fw-semibold">Search</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Name, Profile ID, City…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label form-label-sm mb-1 fw-semibold">Approval</label>
      <select name="status" class="form-select form-select-sm">
        <option value="">All</option>
        <option value="pending"  <?= $filters['status']==='pending' ?'selected':'' ?>>Pending</option>
        <option value="approved" <?= $filters['status']==='approved'?'selected':'' ?>>Approved</option>
        <option value="rejected" <?= $filters['status']==='rejected'?'selected':'' ?>>Rejected</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label form-label-sm mb-1 fw-semibold">Gender</label>
      <select name="gender" class="form-select form-select-sm">
        <option value="">All</option>
        <option value="male"   <?= $filters['gender']==='male'  ?'selected':'' ?>>Male</option>
        <option value="female" <?= $filters['gender']==='female'?'selected':'' ?>>Female</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label form-label-sm mb-1 fw-semibold">Religion</label>
      <input type="text" name="religion" class="form-control form-control-sm" placeholder="Hindu, Muslim…" value="<?= htmlspecialchars($filters['religion']) ?>">
    </div>
    <div class="col-6 col-md-auto d-flex gap-2 align-items-end">
      <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
      <a href="<?= APP_URL ?>/admin/profiles" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">Member</th><th>Gender</th><th>Religion / Caste</th>
          <th>Education</th><th>Location</th><th>Photos</th><th>Approval</th><th>Joined</th><th class="text-center">Actions</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No profiles found.</td></tr>
        <?php else: foreach($data as $p): ?>
        <tr>
          <td class="ps-4">
            <div class="fw-semibold" style="font-size:.86rem"><?= htmlspecialchars($p['name']) ?></div>
            <small class="text-muted"><?= $p['profile_id'] ?></small>
          </td>
          <td><span class="badge rounded-pill px-2" style="background:<?= $p['gender']==='female'?'#fce4ec':'#e3f2fd' ?>;color:<?= $p['gender']==='female'?'#880e4f':'#1565c0' ?>"><?= ucfirst($p['gender']) ?></span></td>
          <td>
            <div style="font-size:.83rem"><?= htmlspecialchars($p['religion']??'—') ?></div>
            <small class="text-muted"><?= htmlspecialchars($p['caste']??'') ?></small>
          </td>
          <td><small><?= htmlspecialchars($p['education']??'—') ?></small></td>
          <td><small><?= htmlspecialchars(($p['city']??'').(isset($p['state'])&&$p['state']?', '.$p['state']:'')) ?></small></td>
          <td><span class="badge bg-secondary"><?= $p['photo_count'] ?></span></td>
          <td>
            <span class="badge badge-<?= $p['admin_approved']==='approved'?'approved':($p['admin_approved']==='rejected'?'rejected':'pending') ?> px-2 py-1 rounded-pill" style="font-size:.72rem">
              <?= ucfirst($p['admin_approved']??'pending') ?>
            </span>
          </td>
          <td><small class="text-muted"><?= date('d M y',strtotime($p['created_at'])) ?></small></td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <a href="<?= APP_URL ?>/admin/profiles/view/<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:.75rem"><i class="bi bi-eye"></i></a>
              <?php if($p['admin_approved']!=='approved'): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/profiles/approve" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size:.75rem" data-confirm="Approve this profile?"><i class="bi bi-check-lg"></i></button>
              </form>
              <?php endif; ?>
              <?php if($p['admin_approved']!=='rejected'): ?>
              <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:.75rem"
                data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?= $p['id'] ?>"><i class="bi bi-x-lg"></i></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4">
    <?php include __DIR__ . '/../layouts/_pagination.php'; ?>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Reject Profile</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="<?= APP_URL ?>/admin/profiles/reject">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="rejectId">
        <div class="modal-body">
          <label class="form-label small fw-semibold">Reason for rejection</label>
          <textarea name="note" class="form-control form-control-sm" rows="3" placeholder="Provide a reason to notify the user…" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-danger">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('rejectModal')?.addEventListener('show.bs.modal', function(e){
  this.querySelector('#rejectId').value = e.relatedTarget.dataset.id;
});
</script>
