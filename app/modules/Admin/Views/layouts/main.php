<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $pageTitle ?? 'Admin Panel' ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
:root{
  --sidebar-w:260px;
  --header-h:60px;
  --brand-grad:linear-gradient(135deg,#6a0572 0%,#c0392b 100%);
  --sidebar-bg:#1a0a1e;
  --sidebar-hover:rgba(255,255,255,.07);
  --sidebar-active:linear-gradient(90deg,rgba(192,57,43,.8),rgba(106,5,114,.6));
  --text-muted-sidebar:rgba(255,255,255,.45);
}

/* ── Reset ── */
*{box-sizing:border-box;}
body{margin:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f5;overflow-x:hidden;}

/* ── Sidebar ── */
#sidebar{
  position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-w);
  background:var(--sidebar-bg);display:flex;flex-direction:column;
  z-index:1040;transition:transform .3s ease;overflow-y:auto;overflow-x:hidden;
}
#sidebar::-webkit-scrollbar{width:4px;}
#sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

.sidebar-brand{
  background:var(--brand-grad);padding:0 1.25rem;height:var(--header-h);
  display:flex;align-items:center;gap:.75rem;flex-shrink:0;
}
.sidebar-brand span{color:#fff;font-weight:700;font-size:1.1rem;letter-spacing:.3px;}

.nav-section{padding:.5rem 1rem .25rem;font-size:.65rem;font-weight:700;
  letter-spacing:1.5px;color:var(--text-muted-sidebar);text-transform:uppercase;margin-top:.5rem;}

.sidebar-nav{list-style:none;padding:.25rem 0;margin:0;}
.sidebar-nav li a{
  display:flex;align-items:center;gap:.75rem;padding:.6rem 1.25rem;
  color:rgba(255,255,255,.75);text-decoration:none;font-size:.88rem;
  border-left:3px solid transparent;transition:all .2s;
}
.sidebar-nav li a:hover{background:var(--sidebar-hover);color:#fff;border-left-color:rgba(255,255,255,.3);}
.sidebar-nav li a.active{background:var(--sidebar-active);color:#fff;border-left-color:#e74c3c;}
.sidebar-nav li a i{font-size:1rem;width:20px;text-align:center;opacity:.85;}
.sidebar-nav li a .badge-pill{margin-left:auto;font-size:.65rem;padding:.25em .5em;}

.sidebar-footer{margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.08);}
.sidebar-footer a{color:rgba(255,255,255,.55);font-size:.82rem;text-decoration:none;display:flex;align-items:center;gap:.5rem;}
.sidebar-footer a:hover{color:#fff;}

/* ── Sticky Header ── */
#topbar{
  position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--header-h);
  background:#fff;border-bottom:1px solid #e5e7eb;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 1.5rem;z-index:1030;transition:left .3s ease;
  box-shadow:0 1px 4px rgba(0,0,0,.06);
}

/* ── Main Content ── */
#main-content{
  margin-left:var(--sidebar-w);margin-top:var(--header-h);
  padding:1.5rem;min-height:calc(100vh - var(--header-h));
  transition:margin-left .3s ease;
}

/* ── Sidebar collapsed (mobile) ── */
@media(max-width:991.98px){
  #sidebar{transform:translateX(calc(-1 * var(--sidebar-w)));}
  #sidebar.open{transform:translateX(0);}
  #topbar,#main-content{left:0;margin-left:0;}
  #overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1039;}
  #overlay.show{display:block;}
}

/* ── Cards ── */
.stat-card{border:none;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);transition:transform .2s;}
.stat-card:hover{transform:translateY(-2px);}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;}

/* ── Tables ── */
.table-card{border:none;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);overflow:hidden;}
.table thead th{background:#f8f9fa;font-size:.78rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.6px;color:#6c757d;border-bottom:2px solid #dee2e6;white-space:nowrap;}
.table tbody tr:hover{background:#fafbff;}
.table td{vertical-align:middle;font-size:.875rem;}

/* ── Filters bar ── */
.filter-bar{background:#fff;border-radius:10px;padding:1rem 1.25rem;
  box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:1rem;}
.filter-bar .form-control,.filter-bar .form-select{font-size:.85rem;}

/* ── Pagination ── */
.pagination .page-link{font-size:.83rem;color:#6a0572;border-radius:6px!important;margin:0 2px;}
.pagination .page-item.active .page-link{background:var(--brand-grad);border-color:transparent;}

/* ── Status badges ── */
.badge-active  {background:#d1fae5;color:#065f46;}
.badge-blocked {background:#fee2e2;color:#991b1b;}
.badge-pending {background:#fef3c7;color:#92400e;}
.badge-deleted {background:#f3f4f6;color:#6b7280;}
.badge-approved{background:#d1fae5;color:#065f46;}
.badge-rejected{background:#fee2e2;color:#991b1b;}
.badge-success {background:#d1fae5;color:#065f46;}
.badge-failed  {background:#fee2e2;color:#991b1b;}

/* ── Session timer ── */
#session-timer{font-size:.78rem;color:#6c757d;}
#session-timer.warning{color:#dc3545;font-weight:600;}

/* ── Avatar ── */
.avatar-sm{width:36px;height:36px;border-radius:50%;object-fit:cover;background:#e9ecef;
  display:inline-flex;align-items:center;justify-content:center;font-weight:600;font-size:.8rem;}
</style>
</head>
<body>

<!-- Overlay (mobile) -->
<div id="overlay" onclick="closeSidebar()"></div>

<!-- ══ SIDEBAR ══ -->
<nav id="sidebar">
  <div class="sidebar-brand">
    <i class="bi bi-heart-fill text-white fs-5"></i>
    <span><?= APP_NAME ?></span>
  </div>

  <?php
  $uri = '/' . trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
  function isActive(string $path, string $uri): string {
      return str_starts_with($uri, $path) ? 'active' : '';
  }
  ?>

  <div class="nav-section">Main</div>
  <ul class="sidebar-nav">
    <li><a href="<?= APP_URL ?>/admin/dashboard" class="<?= isActive('/admin/dashboard',$uri) ?: isActive('/admin',$uri) ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a></li>
  </ul>

  <div class="nav-section">Members</div>
  <ul class="sidebar-nav">
    <li><a href="<?= APP_URL ?>/admin/users" class="<?= isActive('/admin/users',$uri) ?>">
      <i class="bi bi-people"></i> Users
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/profiles" class="<?= isActive('/admin/profiles',$uri) ?>">
      <i class="bi bi-person-vcard"></i> Profiles
      <?php $pc=Database::fetchOne("SELECT COUNT(*) c FROM profiles WHERE admin_approved='pending'")['c']??0;
        if($pc>0) echo "<span class='badge bg-danger badge-pill'>$pc</span>"; ?>
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/photos" class="<?= isActive('/admin/photos',$uri) ?>">
      <i class="bi bi-images"></i> Photos
      <?php $ph=Database::fetchOne("SELECT COUNT(*) c FROM photos WHERE is_approved='pending'")['c']??0;
        if($ph>0) echo "<span class='badge bg-warning text-dark badge-pill'>$ph</span>"; ?>
    </a></li>
  </ul>

  <div class="nav-section">Activity</div>
  <ul class="sidebar-nav">
    <li><a href="<?= APP_URL ?>/admin/reports" class="<?= isActive('/admin/reports',$uri) ?>">
      <i class="bi bi-flag"></i> Reports
      <?php $rp=Database::fetchOne("SELECT COUNT(*) c FROM reports WHERE status='open'")['c']??0;
        if($rp>0) echo "<span class='badge bg-danger badge-pill'>$rp</span>"; ?>
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/logs" class="<?= isActive('/admin/logs',$uri) ?>">
      <i class="bi bi-journal-text"></i> Activity Logs
    </a></li>
  </ul>

  <div class="nav-section">Commerce</div>
  <ul class="sidebar-nav">
    <li><a href="<?= APP_URL ?>/admin/subscriptions" class="<?= isActive('/admin/subscriptions',$uri) ?>">
      <i class="bi bi-gem"></i> Subscriptions
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/plans" class="<?= isActive('/admin/plans',$uri) ?>">
      <i class="bi bi-layers"></i> Plans
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/payments" class="<?= isActive('/admin/payments',$uri) ?>">
      <i class="bi bi-credit-card"></i> Payments
    </a></li>
  </ul>

  <div class="nav-section">Content</div>
  <ul class="sidebar-nav">
    <li><a href="<?= APP_URL ?>/admin/cms/stories" class="<?= isActive('/admin/cms/stories',$uri) ?>">
      <i class="bi bi-hearts"></i> Success Stories
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/cms/seo" class="<?= isActive('/admin/cms/seo',$uri) ?>">
      <i class="bi bi-search"></i> SEO Pages
    </a></li>
    <li><a href="<?= APP_URL ?>/admin/settings" class="<?= isActive('/admin/settings',$uri) ?>">
      <i class="bi bi-gear"></i> Settings
    </a></li>
  </ul>

  <div class="sidebar-footer">
    <div class="d-flex align-items-center gap-2 mb-2">
      <div class="avatar-sm bg-secondary text-white"><?= strtoupper(substr(Session::get('admin_name','A'),0,1)) ?></div>
      <div>
        <div class="text-white" style="font-size:.82rem;font-weight:600"><?= htmlspecialchars(Session::get('admin_name','Admin')) ?></div>
        <div style="font-size:.72rem;color:rgba(255,255,255,.45)"><?= ucfirst(Session::get('admin_role','admin')) ?></div>
      </div>
    </div>
    <a href="<?= APP_URL ?>/admin/logout"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
  </div>
</nav>

<!-- ══ STICKY TOPBAR ══ -->
<header id="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleSidebar()">
      <i class="bi bi-list fs-5"></i>
    </button>
    <nav aria-label="breadcrumb" class="d-none d-md-block">
      <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/admin/dashboard" class="text-decoration-none">Admin</a></li>
        <li class="breadcrumb-item active"><?= $pageTitle ?? 'Dashboard' ?></li>
      </ol>
    </nav>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div id="session-timer" title="Session expires in">
      <i class="bi bi-clock me-1"></i><span id="timer-display">30:00</span>
    </div>
    <!-- Notification bell -->
    <div class="dropdown">
      <button class="btn btn-sm btn-light position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-5"></i>
        <?php $nb=Database::fetchOne("SELECT COUNT(*) c FROM reports WHERE status='open'")['c']??0;
          if($nb>0) echo "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger' style='font-size:.6rem'>$nb</span>"; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:280px">
        <li><h6 class="dropdown-header">Pending Actions</h6></li>
        <?php
        $pProf=Database::fetchOne("SELECT COUNT(*) c FROM profiles WHERE admin_approved='pending'")['c']??0;
        $pPho =Database::fetchOne("SELECT COUNT(*) c FROM photos WHERE is_approved='pending'")['c']??0;
        if($pProf>0): ?>
        <li><a class="dropdown-item small" href="<?= APP_URL ?>/admin/profiles?status=pending">
          <i class="bi bi-person-vcard text-warning me-2"></i><?= $pProf ?> profiles pending approval
        </a></li>
        <?php endif; if($pPho>0): ?>
        <li><a class="dropdown-item small" href="<?= APP_URL ?>/admin/photos?status=pending">
          <i class="bi bi-images text-info me-2"></i><?= $pPho ?> photos pending review
        </a></li>
        <?php endif; if($nb>0): ?>
        <li><a class="dropdown-item small" href="<?= APP_URL ?>/admin/reports">
          <i class="bi bi-flag text-danger me-2"></i><?= $nb ?> open reports
        </a></li>
        <?php endif;
        if(!$pProf && !$pPho && !$nb) echo '<li><span class="dropdown-item small text-muted">All clear ✓</span></li>'; ?>
      </ul>
    </div>
    <a href="<?= APP_URL ?>/admin/logout" class="btn btn-sm btn-outline-danger">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</header>

<!-- ══ MAIN CONTENT ══ -->
<main id="main-content">

  <?php if ($flash = Session::getFlash('success')): ?>
  <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?= $content ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Sidebar toggle ──
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('show');
}

// ── Session countdown timer ──
(function(){
  let secs = <?= SESSION_TIMEOUT * 60 ?>;
  const el  = document.getElementById('timer-display');
  const box = document.getElementById('session-timer');
  const tick = setInterval(()=>{
    secs--;
    if(secs <= 0){ clearInterval(tick); window.location = '<?= APP_URL ?>/admin/logout'; return; }
    const m = String(Math.floor(secs/60)).padStart(2,'0');
    const s = String(secs % 60).padStart(2,'0');
    el.textContent = m+':'+s;
    if(secs < 300) box.classList.add('warning');
    // Reset on any user activity
  }, 1000);
  ['mousemove','keydown','click'].forEach(ev => document.addEventListener(ev, ()=>{ secs = <?= SESSION_TIMEOUT * 60 ?>; box.classList.remove('warning'); }, {passive:true}));
})();

// ── Confirm delete / block ──
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-confirm]').forEach(btn=>{
    btn.addEventListener('click', function(e){
      if(!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });
});
</script>
</body>
</html>
