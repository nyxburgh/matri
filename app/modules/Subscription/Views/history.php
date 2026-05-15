<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">History</div>
    <h1 style="font-size:1.35rem">Subscription History</h1>
  </div>
  <a href="<?= APP_URL ?>/subscription" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.38rem .9rem">
    <i class="bi bi-gem"></i> View Plans
  </a>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-clock-history" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No subscription history</h4>
  <p style="color:var(--muted)">You haven't subscribed to any plan yet.</p>
  <a href="<?= APP_URL ?>/subscription" class="btn-pink mt-2" style="display:inline-block;padding:.6rem 1.5rem">View Plans</a>
</div>
<?php else: ?>
<div class="mat-card overflow-hidden mb-4">
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem">
      <thead>
        <tr style="background:var(--pink-pale)">
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">Plan</th>
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">Amount</th>
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">Start</th>
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">End</th>
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">Status</th>
          <th style="padding:.85rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--pink);border:none">Payment</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $row):
          $statusColors = ['active'=>'var(--green)','expired'=>'var(--muted)','cancelled'=>'#EF4444','pending'=>'#F57F17'];
          $statusBg     = ['active'=>'var(--green-pale)','expired'=>'#F3F4F6','cancelled'=>'#FEE2E2','pending'=>'#FFF8E1'];
        ?>
        <tr style="vertical-align:middle">
          <td style="padding:.85rem 1rem;border-color:var(--border)">
            <div style="font-weight:700;color:var(--dark)"><?= htmlspecialchars($row['plan_name']) ?></div>
          </td>
          <td style="padding:.85rem 1rem;border-color:var(--border);font-weight:700;color:var(--pink)">
            <?= $row['price'] > 0 ? '₹'.number_format($row['price'],0) : 'Free' ?>
          </td>
          <td style="padding:.85rem 1rem;border-color:var(--border);color:var(--muted)">
            <?= date('d M Y', strtotime($row['start_date'])) ?>
          </td>
          <td style="padding:.85rem 1rem;border-color:var(--border);color:var(--muted)">
            <?= date('d M Y', strtotime($row['end_date'])) ?>
          </td>
          <td style="padding:.85rem 1rem;border-color:var(--border)">
            <span style="background:<?= $statusBg[$row['status']]??'#F3F4F6' ?>;color:<?= $statusColors[$row['status']]??'var(--muted)' ?>;font-size:.72rem;font-weight:700;padding:.25rem .65rem;border-radius:50px">
              <?= ucfirst($row['status']) ?>
            </span>
          </td>
          <td style="padding:.85rem 1rem;border-color:var(--border)">
            <?php if (!empty($row['gateway_payment_id'])): ?>
            <div style="font-size:.75rem;color:var(--muted)"><?= ucfirst($row['gateway'] ?? '') ?></div>
            <div style="font-size:.72rem;color:var(--muted);font-family:monospace"><?= htmlspecialchars(substr($row['gateway_payment_id'],0,20)).'...' ?></div>
            <?php else: ?>
            <span style="color:var(--muted);font-size:.78rem">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page>1): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page<$last_page): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>
