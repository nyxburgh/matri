<!DOCTYPE html>
<html lang="<?= Session::get('user_lang','en') ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Namma Matrimony') ?> — <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;
  --green:#1B8C5E;--green-light:#27C47F;--green-pale:#E5F7F0;
  --dark:#1A1028;--text:#3D3046;--muted:#8B7E97;--border:#F0E6F6;
  --shadow-pink:0 8px 32px rgba(233,30,140,.18);
  --shadow-green:0 8px 32px rgba(27,140,94,.15);
  --radius:16px;--radius-lg:24px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;color:var(--text);background:#F9F3FB;overflow-x:hidden;padding-bottom:70px}
h1,h2,h3,h4,h5,h6{font-family:'Playfair Display',serif}
a{text-decoration:none;color:inherit}
@media(min-width:768px){body{padding-bottom:0}}

/* ── Buttons ── */
.btn-pink{background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.55rem 1.5rem;font-weight:600;transition:transform .2s,box-shadow .2s;box-shadow:var(--shadow-pink)}
.btn-pink:hover,.btn-pink:focus{transform:translateY(-2px);box-shadow:0 12px 32px rgba(233,30,140,.28);color:#fff}
.btn-green{background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;border:none;border-radius:50px;padding:.55rem 1.5rem;font-weight:600;transition:transform .2s,box-shadow .2s;box-shadow:var(--shadow-green)}
.btn-green:hover,.btn-green:focus{transform:translateY(-2px);color:#fff}
.btn-outline-pink{border:2px solid var(--pink);color:var(--pink);border-radius:50px;padding:.5rem 1.4rem;font-weight:600;background:transparent;transition:all .2s}
.btn-outline-pink:hover{background:var(--pink);color:#fff}
.btn-outline-green{border:2px solid var(--green);color:var(--green);border-radius:50px;padding:.5rem 1.4rem;font-weight:600;background:transparent;transition:all .2s}
.btn-outline-green:hover{background:var(--green);color:#fff}

/* ── Navbar ── */
.site-nav{
  background:rgba(255,255,255,.97);backdrop-filter:blur(14px);
  border-bottom:2px solid transparent;padding:.55rem 0;
  position:sticky;top:0;z-index:1000;transition:border-color .35s,box-shadow .35s;
}
.site-nav.scrolled{border-bottom-color:var(--pink);box-shadow:0 4px 22px rgba(233,30,140,.15)}
.nav-brand{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:900;
  background:linear-gradient(135deg,var(--pink),var(--green));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.nav-link-item{font-size:.875rem;font-weight:500;color:var(--text);padding:.3rem .7rem;border-radius:8px;transition:background .18s,color .18s;white-space:nowrap;display:flex;align-items:center;gap:.35rem}
.nav-link-item:hover,.nav-link-item.active{background:var(--pink-pale);color:var(--pink)}
.nav-notif{position:relative;background:var(--pink-pale);border:none;width:34px;height:34px;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:var(--pink);font-size:1rem;transition:background .18s;flex-shrink:0}
.nav-notif:hover{background:var(--pink);color:#fff}
.notif-dot{position:absolute;top:3px;right:3px;width:7px;height:7px;border-radius:50%;background:#FF3B3B;border:1.5px solid #fff}
.nav-avatar{width:34px;height:34px;border-radius:50%;object-fit:cover;background:linear-gradient(135deg,var(--pink),var(--green));display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.82rem;flex-shrink:0;border:2px solid var(--pink-pale)}

/* ── Mobile Bottom Nav ── */
.mob-bottom-nav{
  display:none;position:fixed;bottom:0;left:0;right:0;z-index:900;
  background:#fff;border-top:1.5px solid var(--border);
  box-shadow:0 -4px 20px rgba(233,30,140,.1);
  padding:.35rem 0 .5rem;
}
@media(max-width:767.98px){
  .mob-bottom-nav{display:flex}
  .site-nav .d-flex{gap:.15rem!important}
}
.mob-nav-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:.18rem;color:var(--muted);font-size:.65rem;font-weight:500;cursor:pointer;padding:.2rem 0;transition:color .2s;background:none;border:none}
.mob-nav-item i{font-size:1.35rem}
.mob-nav-item.active,.mob-nav-item:hover{color:var(--pink)}
.mob-nav-item .mob-badge{position:absolute;top:-3px;right:calc(50% - 18px);background:#FF3B3B;color:#fff;font-size:.58rem;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px}

/* ── Cards ── */
.mat-card{background:#fff;border-radius:var(--radius);border:1px solid var(--border);box-shadow:0 2px 12px rgba(233,30,140,.07);transition:transform .2s,box-shadow .2s}
.mat-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(233,30,140,.14)}
.profile-card{position:relative;overflow:hidden}
.profile-card .photo-wrap{position:relative;height:220px;overflow:hidden;background:var(--pink-pale)}
.profile-card .photo-wrap img{width:100%;height:100%;object-fit:cover}
.profile-card .no-photo{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:4rem;color:var(--muted)}
.profile-card .plan-badge{position:absolute;top:10px;left:10px;background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;font-size:.65rem;font-weight:700;padding:.2rem .6rem;border-radius:50px}
.profile-card .status-dot{position:absolute;top:10px;right:10px;width:10px;height:10px;border-radius:50%;background:#ccc;border:2px solid #fff}
.profile-card .status-dot.online{background:#27C47F}
.profile-card .card-body{padding:1rem}
.profile-card .profile-name{font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:.2rem}
.profile-card .profile-meta{font-size:.8rem;color:var(--muted)}
.profile-card .action-bar{display:flex;gap:.5rem;padding:.75rem 1rem;border-top:1px solid var(--border)}

/* ── Section labels ── */
.section-tag{display:inline-block;background:var(--pink-pale);color:var(--pink);font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.3rem .9rem;border-radius:50px;margin-bottom:.75rem}
.section-tag.green{background:var(--green-pale);color:var(--green)}

/* ── Stat pills ── */
.stat-pill{background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:.75rem 1rem;text-align:center;transition:border-color .2s}
.stat-pill:hover{border-color:var(--pink)}
.stat-num{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1}
.stat-label{font-size:.72rem;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em;margin-top:.25rem}

/* ── Page header ── */
.page-header{background:linear-gradient(135deg,var(--pink-pale) 0%,var(--green-pale) 100%);border-radius:var(--radius-lg);padding:1.5rem 2rem;margin-bottom:1.5rem}
.page-header h1{font-size:1.5rem;color:var(--dark)}

/* ── Badges ── */
.badge-pink{background:var(--pink-pale);color:var(--pink);font-weight:600;font-size:.72rem;padding:.3em .7em;border-radius:50px}
.badge-green{background:var(--green-pale);color:var(--green);font-weight:600;font-size:.72rem;padding:.3em .7em;border-radius:50px}
.badge-gold{background:#FFF8E1;color:#F57F17;font-weight:600;font-size:.72rem;padding:.3em .7em;border-radius:50px}

/* ── Interest status badges ── */
.status-pending{background:#FEF3C7;color:#92400E}
.status-accepted{background:#D1FAE5;color:#065F46}
.status-rejected{background:#FEE2E2;color:#991B1B}

/* ── Flash messages ── */
.flash-success,.flash-error,.flash-info{border-radius:12px;padding:.85rem 1.1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.6rem;font-size:.9rem}
.flash-success{background:#D1FAE5;color:#065F46;border:1px solid #6EE7B7}
.flash-error{background:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5}
.flash-info{background:#DBEAFE;color:#1E40AF;border:1px solid #93C5FD}

/* ── Form controls ── */
.form-control:focus,.form-select:focus{border-color:var(--pink);box-shadow:0 0 0 .2rem rgba(233,30,140,.15)}
.form-label{font-size:.875rem;font-weight:600;color:var(--text);margin-bottom:.4rem}
</style>
</head>
<body>

<!-- ══ DESKTOP NAVBAR ══ -->
<nav class="site-nav" id="siteNav">
  <div class="container-xl">
    <div class="d-flex align-items-center justify-content-between gap-3">

      <!-- Brand -->
      <a href="<?= APP_URL ?>/dashboard" class="nav-brand text-decoration-none"><?= APP_NAME ?></a>

      <!-- Desktop Nav Links -->
      <div class="d-none d-md-flex align-items-center gap-1 flex-grow-1 justify-content-center">
        <?php
        $uri = '/' . trim(strtok($_SERVER['REQUEST_URI'],'?'),'/');
        function userNavActive(string $path, string $uri): string {
          return str_starts_with($uri, $path) ? 'active' : '';
        }
        ?>
        <a href="<?= APP_URL ?>/dashboard" class="nav-link-item <?= userNavActive('/dashboard',$uri) ?>">
          <i class="bi bi-house"></i> Home
        </a>
        <a href="<?= APP_URL ?>/search" class="nav-link-item <?= userNavActive('/search',$uri) ?>">
          <i class="bi bi-search"></i> Search
        </a>
        <a href="<?= APP_URL ?>/interests" class="nav-link-item <?= userNavActive('/interest',$uri) ?>">
          <i class="bi bi-heart"></i> Interests
          <?php
          $uid = Session::get('user_id');
          $pi  = Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE receiver_id=? AND status='pending'", [$uid])['c'] ?? 0;
          if ($pi > 0) echo "<span class='badge rounded-pill' style='background:var(--pink);font-size:.6rem'>$pi</span>";
          ?>
        </a>
        <a href="<?= APP_URL ?>/chat" class="nav-link-item <?= userNavActive('/chat',$uri) ?>">
          <i class="bi bi-chat-dots"></i> Chat
          <?php
          $unreadMsg = Database::fetchOne("SELECT COUNT(*) c FROM chat_messages WHERE receiver_id=? AND is_read=0", [$uid])['c'] ?? 0;
          if ($unreadMsg > 0) echo "<span class='badge rounded-pill' style='background:var(--pink);font-size:.6rem'>$unreadMsg</span>";
          ?>
        </a>
        <a href="<?= APP_URL ?>/my-profile" class="nav-link-item <?= userNavActive('/my-profile',$uri) ?>">
          <i class="bi bi-person-circle"></i> My Profile
        </a>
      </div>

      <!-- Right Side -->
      <div class="d-flex align-items-center gap-2">
        <!-- Notification bell -->
        <a href="<?= APP_URL ?>/notifications" class="nav-notif" title="Notifications" style="text-decoration:none">
          <i class="bi bi-bell"></i>
          <?php
          $unreadNotif = Database::fetchOne("SELECT COUNT(*) c FROM notifications WHERE user_id=? AND is_read=0", [$uid])['c'] ?? 0;
          if ($unreadNotif > 0) echo "<span class='notif-dot'></span>";
          ?>
        </a>

        <!-- User dropdown -->
        <div class="dropdown">
          <button class="nav-avatar dropdown-toggle border-0" data-bs-toggle="dropdown" aria-expanded="false" style="background:linear-gradient(135deg,var(--pink),var(--green));color:#fff">
            <?= strtoupper(substr(Session::get('user_name','U'),0,1)) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius:12px;min-width:200px;border:1px solid var(--border)">
            <li class="px-3 py-2">
              <div class="fw-600" style="font-size:.9rem"><?= htmlspecialchars(Session::get('user_name','')) ?></div>
              <div style="font-size:.75rem;color:var(--muted)"><?= htmlspecialchars(Session::get('user_profile_id','')) ?></div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= APP_URL ?>/my-profile"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= APP_URL ?>/shortlist"><i class="bi bi-bookmark-heart"></i> Shortlist</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= APP_URL ?>/subscription"><i class="bi bi-gem"></i> Upgrade Plan</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= APP_URL ?>/account/settings"><i class="bi bi-gear"></i> Settings</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="<?= APP_URL ?>/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- ══ MAIN CONTENT ══ -->
<main class="container-xl py-4">

  <?php if ($flash = Session::getFlash('success')): ?>
  <div class="flash-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()" style="font-size:.7rem"></button>
  </div>
  <?php endif; ?>

  <?php if ($error = Session::getFlash('error')): ?>
  <div class="flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()" style="font-size:.7rem"></button>
  </div>
  <?php endif; ?>

  <?php if ($info = Session::getFlash('info')): ?>
  <div class="flash-info"><i class="bi bi-info-circle-fill"></i> <?= htmlspecialchars($info) ?>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()" style="font-size:.7rem"></button>
  </div>
  <?php endif; ?>

  <?= $content ?>

</main>

<!-- ══ MOBILE BOTTOM NAV ══ -->
<nav class="mob-bottom-nav d-md-none">
  <?php $uid2 = Session::get('user_id'); ?>
  <a href="<?= APP_URL ?>/dashboard" class="mob-nav-item <?= str_starts_with($uri,'/dashboard') ? 'active':'' ?> text-decoration-none">
    <i class="bi bi-house-fill"></i><span>Home</span>
  </a>
  <a href="<?= APP_URL ?>/search" class="mob-nav-item <?= str_starts_with($uri,'/search') ? 'active':'' ?> text-decoration-none">
    <i class="bi bi-search"></i><span>Search</span>
  </a>
  <a href="<?= APP_URL ?>/interests" class="mob-nav-item <?= str_starts_with($uri,'/interest') ? 'active':'' ?> text-decoration-none position-relative">
    <i class="bi bi-heart<?= str_starts_with($uri,'/interest') ? '-fill':'' ?>"></i>
    <?php $piMob = Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE receiver_id=? AND status='pending'",[$uid2])['c']??0; if($piMob>0) echo "<span class='mob-badge'>$piMob</span>"; ?>
    <span>Interests</span>
  </a>
  <a href="<?= APP_URL ?>/chat" class="mob-nav-item <?= str_starts_with($uri,'/chat') ? 'active':'' ?> text-decoration-none position-relative">
    <i class="bi bi-chat-dots<?= str_starts_with($uri,'/chat') ? '-fill':'' ?>"></i>
    <?php $umMob = Database::fetchOne("SELECT COUNT(*) c FROM chat_messages WHERE receiver_id=? AND is_read=0",[$uid2])['c']??0; if($umMob>0) echo "<span class='mob-badge'>$umMob</span>"; ?>
    <span>Chat</span>
  </a>
  <a href="<?= APP_URL ?>/my-profile" class="mob-nav-item <?= str_starts_with($uri,'/my-profile') ? 'active':'' ?> text-decoration-none">
    <i class="bi bi-person<?= str_starts_with($uri,'/my-profile') ? '-fill':'' ?>"></i><span>Profile</span>
  </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sticky nav scroll effect
const nav = document.getElementById('siteNav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 30);
}, {passive: true});

// Auto-dismiss flash after 5s
document.querySelectorAll('.flash-success,.flash-info').forEach(el => {
  setTimeout(() => el.style.opacity = '0', 4500);
  setTimeout(() => el.remove(), 5000);
});

// Notification count live update (every 60s)
(function pollNotif() {
  fetch('<?= APP_URL ?>/notifications/count')
    .then(r => r.json())
    .then(d => {
      const dot = document.querySelector('.nav-notif .notif-dot');
      if (d.notif_count > 0 || d.msg_count > 0) {
        if (!dot) {
          document.querySelector('.nav-notif').insertAdjacentHTML('beforeend','<span class="notif-dot"></span>');
        }
      } else if (dot) dot.remove();
    }).catch(()=>{});
  setTimeout(pollNotif, 60000);
})();
</script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
