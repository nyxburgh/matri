<?php
// Null-safe pagination defaults
$data         = $data         ?? [];
$total        = $total        ?? 0;
$per_page     = $per_page     ?? 20;
$current_page = $current_page ?? 1;
$last_page    = $last_page    ?? 1;
// ═══ PAYMENTS ═════════════════════════════════════════
$pageTitle = 'Payments'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-credit-card me-2 text-info"></i>Payment Records</h5>
  <span class="badge bg-info text-dark rounded-pill"><?= number_format($total) ?> payments</span>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-3">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Name, ID, Gateway TXN…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-6 col-md-2">
      <select name="status" class="form-select form-select-sm">
        <option value="">All Status</option>
        <option value="success"   <?= $filters['status']==='success'  ?'selected':'' ?>>Success</option>
        <option value="failed"    <?= $filters['status']==='failed'   ?'selected':'' ?>>Failed</option>
        <option value="initiated" <?= $filters['status']==='initiated'?'selected':'' ?>>Initiated</option>
        <option value="refunded"  <?= $filters['status']==='refunded' ?'selected':'' ?>>Refunded</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <select name="gateway" class="form-select form-select-sm">
        <option value="">All Gateways</option>
        <option value="razorpay" <?= $filters['gateway']==='razorpay'?'selected':'' ?>>Razorpay</option>
        <option value="stripe"   <?= $filters['gateway']==='stripe'  ?'selected':'' ?>>Stripe</option>
        <option value="manual"   <?= $filters['gateway']==='manual'  ?'selected':'' ?>>Manual</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="from" class="form-control form-control-sm" value="<?= $filters['from'] ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="to" class="form-control form-control-sm" value="<?= $filters['to'] ?>">
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/payments" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">Member</th><th>Plan</th><th>Amount</th><th>Gateway</th><th>TXN ID</th><th>Status</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No payments found.</td></tr>
        <?php else: foreach($data as $p): ?>
        <tr>
          <td class="ps-4">
            <div class="fw-semibold" style="font-size:.86rem"><?= htmlspecialchars($p['name']) ?></div>
            <small class="text-muted"><?= $p['profile_id'] ?></small>
          </td>
          <td><small><?= htmlspecialchars($p['plan_name']) ?></small></td>
          <td class="fw-bold text-success">₹<?= number_format($p['amount'],2) ?></td>
          <td><span class="badge bg-secondary-subtle text-secondary" style="font-size:.72rem"><?= ucfirst($p['gateway']) ?></span></td>
          <td><small class="font-monospace text-muted" title="<?= htmlspecialchars($p['gateway_payment_id']??'') ?>"><?= $p['gateway_payment_id'] ? substr($p['gateway_payment_id'],0,16).'…' : '—' ?></small></td>
          <td>
            <?php $sc=['success'=>'success','failed'=>'danger','initiated'=>'warning','refunded'=>'info']; ?>
            <span class="badge bg-<?= $sc[$p['status']]??'secondary' ?> rounded-pill" style="font-size:.72rem"><?= ucfirst($p['status']) ?></span>
          </td>
          <td><small class="text-muted"><?= date('d M y H:i',strtotime($p['created_at'])) ?></small></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>
