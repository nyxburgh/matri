<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php $pageTitle = 'Users'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>User Management</h5>
  <span class="badge bg-primary rounded-pill"><?= number_format($total) ?> users</span>
</div>

<!-- Filter Bar -->
<div class="filter-bar mb-3">
  <form method="GET" action="" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-3">
      <label class="form-label form-label-sm mb-1 fw-semibold">Search</label>
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Name, email, mobile, ID…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
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
      <label class="form-label form-label-sm mb-1 fw-semibold">Status</label>
      <select name="status" class="form-select form-select-sm">
        <option value="">All</option>
        <option value="active"  <?= $filters['status']==='active' ?'selected':'' ?>>Active</option>
        <option value="pending" <?= $filters['status']==='pending'?'selected':'' ?>>Pending</option>
        <option value="blocked" <?= $filters['status']==='blocked'?'selected':'' ?>>Blocked</option>
        <option value="deleted" <?= $filters['status']==='deleted'?'selected':'' ?>>Deleted</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label form-label-sm mb-1 fw-semibold">From</label>
      <input type="date" name="from" class="form-control form-control-sm" value="<?= $filters['from'] ?>">
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label form-label-sm mb-1 fw-semibold">To</label>
      <input type="date" name="to" class="form-control form-control-sm" value="<?= $filters['to'] ?>">
    </div>
    <div class="col-12 col-md-1 d-flex gap-2">
      <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<!-- Table -->
<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th class="ps-4">Profile</th>
            <th>Contact</th>
            <th>Gender</th>
            <th>Location</th>
            <th>Profile</th>
            <th>Verified</th>
            <th>Status</th>
            <th>Joined</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="9" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>No users found.
          </td></tr>
        <?php else: foreach($data as $u): ?>
        <tr>
          <td class="ps-4">
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm bg-secondary text-white flex-shrink-0" style="background:<?= $u['gender']==='female'?'#e91e63':'#1976d2' ?>!important">
                <?= strtoupper(substr($u['name'],0,1)) ?>
              </div>
              <div>
                <div class="fw-semibold" style="font-size:.86rem"><?= htmlspecialchars($u['name']) ?></div>
                <small class="text-muted"><?= $u['profile_id'] ?></small>
              </div>
            </div>
          </td>
          <td>
            <div style="font-size:.82rem"><?= htmlspecialchars($u['email'] ?? '—') ?></div>
            <small class="text-muted"><?= htmlspecialchars($u['mobile'] ?? '') ?></small>
          </td>
          <td><span class="badge rounded-pill px-2" style="background:<?= $u['gender']==='female'?'#fce4ec':'#e3f2fd' ?>;color:<?= $u['gender']==='female'?'#880e4f':'#1565c0' ?>"><?= ucfirst($u['gender']) ?></span></td>
          <td><small><?= htmlspecialchars($u['city'] ?? '—') ?></small></td>
          <td>
            <?php $ap=$u['admin_approved']??'pending'; ?>
            <span class="badge badge-<?= $ap === 'approved'?'approved':($ap==='rejected'?'rejected':'pending') ?> px-2 py-1 rounded-pill" style="font-size:.72rem">
              <?= ucfirst($ap) ?>
            </span>
          </td>
          <td>
            <div class="d-flex gap-1">
              <?php if($u['email_verified']): ?><span class="badge rounded-pill bg-success-subtle text-success" style="font-size:.68rem"><i class="bi bi-envelope-check"></i></span><?php endif; ?>
              <?php if($u['mobile_verified']): ?><span class="badge rounded-pill bg-info-subtle text-info" style="font-size:.68rem"><i class="bi bi-phone"></i></span><?php endif; ?>
            </div>
          </td>
          <td><span class="badge badge-<?= $u['status'] ?> px-2 py-1 rounded-pill" style="font-size:.72rem"><?= ucfirst($u['status']) ?></span></td>
          <td><small class="text-muted"><?= date('d M y', strtotime($u['created_at'])) ?></small></td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <a href="<?= APP_URL ?>/admin/users/view/<?= $u['id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" title="View" style="font-size:.75rem"><i class="bi bi-eye"></i></a>
              <?php if($u['status']==='blocked'): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/users/unblock" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-success py-0 px-2" title="Unblock" style="font-size:.75rem" data-confirm="Unblock this user?"><i class="bi bi-unlock"></i></button>
              </form>
              <?php elseif($u['status']!=='deleted'): ?>
              <button type="button" class="btn btn-xs btn-outline-warning py-0 px-2" title="Block" style="font-size:.75rem" data-bs-toggle="modal" data-bs-target="#blockModal" data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['name']) ?>"><i class="bi bi-slash-circle"></i></button>
              <?php endif; ?>
              <?php if($u['status']!=='deleted'): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/users/delete" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" title="Delete" style="font-size:.75rem" data-confirm="Delete user <?= htmlspecialchars($u['name']) ?>? This cannot be undone."><i class="bi bi-trash"></i></button>
              </form>
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

<!-- Block Modal -->
<div class="modal fade" id="blockModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Block User</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="<?= APP_URL ?>/admin/users/block">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="blockUserId">
        <div class="modal-body">
          <p class="mb-2 small">Block <strong id="blockUserName"></strong>?</p>
          <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (optional)">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-warning">Block</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('blockModal')?.addEventListener('show.bs.modal', function(e){
  this.querySelector('#blockUserId').value = e.relatedTarget.dataset.id;
  this.querySelector('#blockUserName').textContent = e.relatedTarget.dataset.name;
});
</script>
