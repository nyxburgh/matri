<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
?>
<?php // ═══ SUBSCRIPTIONS ════════════════════════════════════
$pageTitle = 'Subscriptions'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-gem me-2 text-success"></i>Subscription Management</h5>
  <span class="badge bg-success rounded-pill"><?= number_format($total) ?> records</span>
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
        <option value="">All Status</option>
        <option value="active"    <?= $filters['status']==='active'   ?'selected':'' ?>>Active</option>
        <option value="expired"   <?= $filters['status']==='expired'  ?'selected':'' ?>>Expired</option>
        <option value="cancelled" <?= $filters['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <select name="plan" class="form-select form-select-sm">
        <option value="">All Plans</option>
        <?php foreach($plans as $pl): ?>
        <option value="<?= $pl['id'] ?>" <?= $filters['plan']==$pl['id']?'selected':'' ?>><?= htmlspecialchars($pl['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/subscriptions" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">Member</th><th>Plan</th><th>Price</th><th>Start</th><th>End</th><th>Status</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No subscriptions found.</td></tr>
        <?php else: foreach($data as $s): ?>
        <tr>
          <td class="ps-4">
            <div class="fw-semibold" style="font-size:.86rem"><?= htmlspecialchars($s['name']) ?></div>
            <small class="text-muted"><?= $s['profile_id'] ?></small>
          </td>
          <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($s['plan_name']) ?></span></td>
          <td class="fw-semibold">₹<?= number_format($s['price'],2) ?></td>
          <td><small><?= date('d M Y',strtotime($s['start_date'])) ?></small></td>
          <td><small class="<?= strtotime($s['end_date'])<time()?'text-danger':'text-muted' ?>"><?= date('d M Y',strtotime($s['end_date'])) ?></small></td>
          <td>
            <?php $sc=['active'=>'success','expired'=>'secondary','cancelled'=>'warning','pending'=>'info']; ?>
            <span class="badge bg-<?= $sc[$s['status']]??'secondary' ?> rounded-pill" style="font-size:.72rem"><?= ucfirst($s['status']) ?></span>
          </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>
