<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php // ═══ PHOTOS VIEW ════════════════════════════════════════════════
// Save this as: app/modules/Admin/Views/photos/index.php
$pageTitle = 'Photo Moderation'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-images me-2 text-purple"></i>Photo Moderation</h5>
  <span class="badge bg-primary rounded-pill"><?= number_format($total) ?> photos</span>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-4">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Name or Profile ID…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-6 col-md-2">
      <select name="status" class="form-select form-select-sm">
        <option value="pending"  <?= $filters['status']==='pending' ?'selected':'' ?>>Pending</option>
        <option value="approved" <?= $filters['status']==='approved'?'selected':'' ?>>Approved</option>
        <option value="rejected" <?= $filters['status']==='rejected'?'selected':'' ?>>Rejected</option>
        <option value="">All</option>
      </select>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/photos" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="row g-3">
<?php if(empty($data)): ?>
  <div class="col-12 text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No photos found.</div>
<?php else: foreach($data as $ph): ?>
  <div class="col-6 col-md-4 col-lg-3 col-xl-2">
    <div class="card table-card h-100">
      <div style="aspect-ratio:1;overflow:hidden;background:#f0f2f5;position:relative">
        <!-- Photo served via proxy — never direct path -->
        <img src="<?= APP_URL ?>/admin/photo-proxy/<?= $ph['id'] ?>" alt="Photo"
             style="width:100%;height:100%;object-fit:cover"
             onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'><rect fill=\'%23e9ecef\' width=\'100\' height=\'100\'/><text y=\'50\' x=\'50\' text-anchor=\'middle\' dominant-baseline=\'middle\' font-size=\'30\'>📷</text></svg>'">
        <span class="position-absolute top-0 end-0 m-1 badge badge-<?= $ph['is_approved'] ?> rounded-pill" style="font-size:.65rem"><?= ucfirst($ph['is_approved']) ?></span>
      </div>
      <div class="card-body p-2">
        <div class="fw-semibold text-truncate" style="font-size:.78rem"><?= htmlspecialchars($ph['name']) ?></div>
        <div class="text-muted" style="font-size:.7rem"><?= $ph['profile_id'] ?></div>
        <div class="d-flex gap-1 mt-2">
          <?php if($ph['is_approved']!=='approved'): ?>
          <form method="POST" action="<?= APP_URL ?>/admin/photos/approve" class="flex-fill">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <input type="hidden" name="id" value="<?= $ph['id'] ?>">
            <button class="btn btn-success btn-sm w-100 py-0" style="font-size:.72rem" data-confirm="Approve?"><i class="bi bi-check"></i></button>
          </form>
          <?php endif; ?>
          <?php if($ph['is_approved']!=='rejected'): ?>
          <form method="POST" action="<?= APP_URL ?>/admin/photos/reject" class="flex-fill">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <input type="hidden" name="id" value="<?= $ph['id'] ?>">
            <button class="btn btn-danger btn-sm w-100 py-0" style="font-size:.72rem" data-confirm="Reject?"><i class="bi bi-x"></i></button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; endif; ?>
</div>
<div class="mt-3"><?php include __DIR__ . '/../layouts/_pagination.php'; ?></div>
