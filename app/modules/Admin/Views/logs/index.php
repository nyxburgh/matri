<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php $pageTitle = 'Activity Logs'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-secondary"></i>Activity Logs</h5>
  <span class="badge bg-secondary rounded-pill"><?= number_format($total) ?> entries</span>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-3">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Name or IP…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-6 col-md-2">
      <input type="text" name="action" class="form-control form-control-sm" placeholder="Action keyword…" value="<?= htmlspecialchars($filters['action']) ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="from" class="form-control form-control-sm" value="<?= $filters['from'] ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="to" class="form-control form-control-sm" value="<?= $filters['to'] ?>">
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/logs" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">User</th><th>Action</th><th>Module</th><th>IP Address</th><th>Timestamp</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No logs found.</td></tr>
        <?php else: foreach($data as $l): ?>
        <tr>
          <td class="ps-4">
            <?php if($l['name']): ?>
            <div style="font-size:.84rem"><?= htmlspecialchars($l['name']) ?></div>
            <small class="text-muted"><?= $l['profile_id']??'Admin' ?></small>
            <?php else: ?><span class="text-muted small">System</span><?php endif; ?>
          </td>
          <td>
            <?php
            $actionColors = ['login'=>'success','logout'=>'secondary','block_user'=>'danger','delete_user'=>'danger',
              'approve_profile'=>'success','reject_profile'=>'warning','update_settings'=>'info'];
            $col = $actionColors[$l['action']] ?? 'secondary';
            ?>
            <span class="badge bg-<?= $col ?>-subtle text-<?= $col ?> font-monospace" style="font-size:.72rem">
              <?= htmlspecialchars(str_replace('_',' ',$l['action'])) ?>
            </span>
          </td>
          <td><small class="text-muted"><?= htmlspecialchars($l['module']??'—') ?></small></td>
          <td><code style="font-size:.75rem"><?= htmlspecialchars($l['ip_address']??'—') ?></code></td>
          <td><small class="text-muted"><?= date('d M y H:i:s',strtotime($l['created_at'])) ?></small></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>
