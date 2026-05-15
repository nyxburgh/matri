<?php $pageTitle = 'Dashboard'; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Total Users',      'value'=>$stats['total_users'],      'icon'=>'bi-people-fill',      'color'=>'#6a0572','bg'=>'#f3e5f5'],
    ['label'=>'Active Members',   'value'=>$stats['active_users'],     'icon'=>'bi-person-check-fill','color'=>'#0d6efd','bg'=>'#e3f2fd'],
    ['label'=>'Pending Profiles', 'value'=>$stats['pending_profiles'], 'icon'=>'bi-person-vcard',     'color'=>'#fd7e14','bg'=>'#fff3e0'],
    ['label'=>'Pending Photos',   'value'=>$stats['pending_photos'],   'icon'=>'bi-images',           'color'=>'#6610f2','bg'=>'#ede7f6'],
    ['label'=>'Active Subs',      'value'=>$stats['active_subs'],      'icon'=>'bi-gem',              'color'=>'#20c997','bg'=>'#e0f7fa'],
    ['label'=>'Open Reports',     'value'=>$stats['open_reports'],     'icon'=>'bi-flag-fill',        'color'=>'#dc3545','bg'=>'#fde8e8'],
    ['label'=>'Total Revenue',    'value'=>'₹'.number_format($stats['total_revenue'],2), 'icon'=>'bi-currency-rupee','color'=>'#198754','bg'=>'#e8f5e9'],
    ['label'=>'This Month Rev',   'value'=>'₹'.number_format($stats['this_month_rev'],2),'icon'=>'bi-graph-up-arrow','color'=>'#0dcaf0','bg'=>'#e0f7fa'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-md-4 col-xl-3">
    <div class="card stat-card h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="stat-icon flex-shrink-0" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>">
          <i class="bi <?= $c['icon'] ?>"></i>
        </div>
        <div>
          <div class="fw-bold fs-5 lh-1"><?= $c['value'] ?></div>
          <div class="text-muted" style="font-size:.78rem"><?= $c['label'] ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
  <!-- Registrations Chart -->
  <div class="col-lg-7">
    <div class="card table-card h-100">
      <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
        <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2 text-primary"></i>Registrations — Last 7 Days</h6>
      </div>
      <div class="card-body px-4 pb-3">
        <canvas id="regChart" height="120"></canvas>
      </div>
    </div>
  </div>
  <!-- Revenue by Plan -->
  <div class="col-lg-5">
    <div class="card table-card h-100">
      <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
        <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-success"></i>Revenue by Plan</h6>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <canvas id="revChart" height="150"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Recent Users + Pending Approvals -->
<div class="row g-3">
  <!-- Recent Users -->
  <div class="col-lg-7">
    <div class="card table-card">
      <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 px-4">
        <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>Recently Joined</h6>
        <a href="<?= APP_URL ?>/admin/users" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr>
              <th class="ps-4">Profile</th><th>Gender</th><th>Location</th><th>Status</th><th>Joined</th>
            </tr></thead>
            <tbody>
            <?php if(empty($recentUsers)): ?>
              <tr><td colspan="5" class="text-center text-muted py-4">No users yet</td></tr>
            <?php else: foreach($recentUsers as $u): ?>
            <tr>
              <td class="ps-4">
                <div class="fw-semibold" style="font-size:.85rem"><?= htmlspecialchars($u['name']) ?></div>
                <small class="text-muted"><?= $u['profile_id'] ?></small>
              </td>
              <td><span class="badge" style="background:<?= $u['gender']==='female'?'#fce4ec':'#e3f2fd' ?>;color:<?= $u['gender']==='female'?'#880e4f':'#1565c0' ?>"><?= ucfirst($u['gender']) ?></span></td>
              <td><small><?= htmlspecialchars($u['city'] ?? '—') ?></small></td>
              <td><span class="badge badge-<?= $u['status'] ?> px-2 py-1 rounded-pill"><?= ucfirst($u['status']) ?></span></td>
              <td><small class="text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></small></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Approvals -->
  <div class="col-lg-5">
    <div class="card table-card">
      <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between pt-3 px-4">
        <h6 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pending Approvals</h6>
        <a href="<?= APP_URL ?>/admin/profiles?status=pending" class="btn btn-sm btn-outline-warning">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th class="ps-4">User</th><th>Religion</th><th>Action</th></tr></thead>
            <tbody>
            <?php if(empty($pendingProfiles)): ?>
              <tr><td colspan="3" class="text-center text-muted py-4"><i class="bi bi-check-circle text-success me-2"></i>All clear!</td></tr>
            <?php else: foreach($pendingProfiles as $p): ?>
            <tr>
              <td class="ps-4">
                <div class="fw-semibold" style="font-size:.83rem"><?= htmlspecialchars($p['name']) ?></div>
                <small class="text-muted"><?= $p['profile_id'] ?> · <?= htmlspecialchars($p['city']??'') ?></small>
              </td>
              <td><small><?= htmlspecialchars($p['religion']??'—') ?></small></td>
              <td>
                <a href="<?= APP_URL ?>/admin/profiles/view/<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:.75rem">Review</a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // Registration chart
  const regLabels = <?= json_encode(array_column($regChart,'day')) ?>;
  const regData   = <?= json_encode(array_column($regChart,'cnt')) ?>;
  new Chart(document.getElementById('regChart'),{
    type:'bar',
    data:{labels:regLabels,datasets:[{label:'Registrations',data:regData,
      backgroundColor:'rgba(106,5,114,.15)',borderColor:'#6a0572',borderWidth:2,borderRadius:6}]},
    options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}},responsive:true}
  });

  // Revenue pie
  const revLabels = <?= json_encode(array_column($revByPlan,'name')) ?>;
  const revData   = <?= json_encode(array_column($revByPlan,'total')) ?>;
  if(revData.length){
    new Chart(document.getElementById('revChart'),{
      type:'doughnut',
      data:{labels:revLabels,datasets:[{data:revData,
        backgroundColor:['#6a0572','#c0392b','#3498db','#2ecc71'],hoverOffset:8}]},
      options:{plugins:{legend:{position:'bottom'}},cutout:'65%',responsive:true}
    });
  }
});
</script>
