<?php
$gender = Session::get('user_gender');
$userName = Session::get('user_name');
?>
<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Dashboard</div>
    <h1>Hello, <?= htmlspecialchars(explode(' ', $userName)[0]) ?> 👋</h1>
    <p style="color:var(--muted);font-size:.9rem;margin-top:.25rem">Here's what's happening with your profile today.</p>
  </div>
  <?php if (($subscription['plan_code'] ?? 'FREE') === 'FREE'): ?>
  <a href="<?= APP_URL ?>/subscription" class="btn-green d-inline-flex align-items-center gap-2">
    <i class="bi bi-gem"></i> Upgrade Plan
  </a>
  <?php else: ?>
  <span class="badge-gold" style="font-size:.82rem;padding:.4rem 1rem">
    <i class="bi bi-gem me-1"></i> <?= htmlspecialchars($subscription['plan_name'] ?? 'Active') ?> Member
  </span>
  <?php endif; ?>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
  <?php
  $statItems = [
    ['icon'=>'bi-heart-fill','label'=>'Interests Sent','val'=>$stats['interests_sent'],'color'=>'var(--pink)','link'=>'interests/sent'],
    ['icon'=>'bi-heart-arrow','label'=>'Received','val'=>$stats['interests_received'],'color'=>'#F57F17','link'=>'interests/received'],
    ['icon'=>'bi-people-fill','label'=>'Matches','val'=>$stats['matches'],'color'=>'var(--green)','link'=>'interests'],
    ['icon'=>'bi-eye-fill','label'=>'Profile Views','val'=>$stats['profile_views'],'color'=>'#7C3AED','link'=>'dashboard/views'],
    ['icon'=>'bi-chat-dots-fill','label'=>'Unread Chats','val'=>$stats['unread_messages'],'color'=>'#0EA5E9','link'=>'chat'],
    ['icon'=>'bi-bell-fill','label'=>'Notifications','val'=>$stats['unread_notifs'],'color'=>'#EF4444','link'=>'notifications'],
  ];
  foreach ($statItems as $s): ?>
  <div class="col-6 col-md-4 col-lg-2">
    <a href="<?= APP_URL ?>/<?= $s['link'] ?>" class="stat-pill d-block text-decoration-none">
      <i class="<?= $s['icon'] ?>" style="font-size:1.3rem;color:<?= $s['color'] ?>"></i>
      <div class="stat-num"><?= number_format((int)$s['val']) ?></div>
      <div class="stat-label"><?= $s['label'] ?></div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<!-- Profile Completeness -->
<?php if ($completeness < 100): ?>
<div class="mat-card p-3 mb-4">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <span style="font-weight:700;font-size:.92rem;color:var(--dark)"><i class="bi bi-person-check me-1" style="color:var(--pink)"></i> Profile Completeness</span>
    <span style="font-size:.85rem;font-weight:700;color:var(--pink)"><?= $completeness ?>%</span>
  </div>
  <div style="height:8px;background:var(--pink-pale);border-radius:8px;overflow:hidden">
    <div style="height:100%;width:<?= $completeness ?>%;background:linear-gradient(90deg,var(--pink),var(--green-light));border-radius:8px;transition:width .6s"></div>
  </div>
  <p style="font-size:.8rem;color:var(--muted);margin-top:.5rem">Complete your profile to get <?= 100 - $completeness ?>% more visibility.
    <a href="<?= APP_URL ?>/profile/edit" style="color:var(--pink);font-weight:600">Complete Now →</a>
  </p>
</div>
<?php endif; ?>

<div class="row g-4">
  <!-- Recommended Matches -->
  <div class="col-12 col-lg-8">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <span class="section-tag">Today's Picks</span>
        <h3 style="font-size:1.15rem;color:var(--dark);margin:0">Recommended Matches</h3>
      </div>
      <a href="<?= APP_URL ?>/search" class="btn-outline-pink btn-sm" style="font-size:.8rem;padding:.35rem 1rem">View All</a>
    </div>

    <?php if (empty($recommended)): ?>
    <div class="mat-card p-4 text-center">
      <i class="bi bi-hearts" style="font-size:2.5rem;color:var(--pink-pale)"></i>
      <p class="mt-2" style="color:var(--muted)">No matches found yet. Complete your profile first!</p>
      <a href="<?= APP_URL ?>/profile/create" class="btn-pink" style="font-size:.85rem;padding:.5rem 1.5rem">Complete Profile</a>
    </div>
    <?php else: ?>
    <div class="row g-3">
      <?php foreach ($recommended as $match): ?>
      <div class="col-6 col-md-4">
        <div class="mat-card profile-card">
          <div class="photo-wrap">
            <?php if (!empty($match['photo'])): ?>
            <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($match['photo']) ?>" alt="<?= htmlspecialchars($match['name']) ?>">
            <?php else: ?>
            <div class="no-photo"><i class="bi bi-person-circle"></i></div>
            <?php endif; ?>
            <?php if (!empty($match['is_highlighted'])): ?>
            <span class="plan-badge"><i class="bi bi-star-fill me-1"></i> Premium</span>
            <?php endif; ?>
            <?php
            $lastActive = $match['last_active'] ?? null;
            $isOnline   = $lastActive && strtotime($lastActive) > time() - 300;
            ?>
            <span class="status-dot <?= $isOnline ? 'online' : '' ?>"></span>
          </div>
          <div class="card-body">
            <div class="profile-name"><?= htmlspecialchars($match['name']) ?></div>
            <div class="profile-meta">
              <?= $match['age'] ? $match['age'].' yrs' : '' ?>
              <?= !empty($match['city']) ? '· '.htmlspecialchars($match['city']) : '' ?>
            </div>
            <div class="profile-meta mt-1">
              <?= !empty($match['religion']) ? htmlspecialchars($match['religion']) : '' ?>
              <?= !empty($match['education']) ? '· '.htmlspecialchars($match['education']) : '' ?>
            </div>
          </div>
          <div class="action-bar">
            <?php if (empty($match['interest_status'])): ?>
            <button class="btn-pink flex-grow-1" style="font-size:.78rem;padding:.35rem .5rem" onclick="sendInterest(<?= $match['id'] ?>, this)">
              <i class="bi bi-heart"></i> Interest
            </button>
            <?php elseif ($match['interest_status'] === 'accepted'): ?>
            <a href="<?= APP_URL ?>/chat/<?= $match['id'] ?>" class="btn-green flex-grow-1 text-center" style="font-size:.78rem;padding:.35rem .5rem">
              <i class="bi bi-chat-dots"></i> Chat
            </a>
            <?php else: ?>
            <span class="badge-pink flex-grow-1 text-center" style="padding:.4rem">
              <?= ucfirst($match['interest_status']) ?>
            </span>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($match['profile_id']) ?>" class="btn-outline-pink" style="font-size:.78rem;padding:.35rem .7rem">View</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right Sidebar -->
  <div class="col-12 col-lg-4">

    <!-- Recent Interests Received -->
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="section-tag">New</span>
        <a href="<?= APP_URL ?>/interests/received" style="font-size:.8rem;color:var(--pink)">See all</a>
      </div>
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:.75rem">Interests Received</h5>

      <?php if (empty($recentInterests)): ?>
      <div class="mat-card p-3 text-center">
        <p style="color:var(--muted);font-size:.85rem">No pending interests yet.</p>
      </div>
      <?php else: ?>
      <div class="d-flex flex-column gap-2">
        <?php foreach ($recentInterests as $int): ?>
        <div class="mat-card p-3 d-flex align-items-center gap-3">
          <div style="width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--pink-pale)">
            <?php if (!empty($int['photo'])): ?>
            <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($int['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
            <?php endif; ?>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div style="font-weight:700;font-size:.88rem;color:var(--dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($int['name']) ?></div>
            <div style="font-size:.75rem;color:var(--muted)"><?= $int['age']??'' ?> <?= !empty($int['city']) ? '· '.htmlspecialchars($int['city']) : '' ?></div>
          </div>
          <div class="d-flex flex-column gap-1">
            <button class="btn-green" style="font-size:.7rem;padding:.25rem .6rem" onclick="respondInterest(<?= $int['id'] ?>,'accepted',this)"><i class="bi bi-check"></i></button>
            <button class="btn-outline-pink" style="font-size:.7rem;padding:.25rem .6rem" onclick="respondInterest(<?= $int['id'] ?>,'rejected',this)"><i class="bi bi-x"></i></button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Who Viewed Me -->
    <?php if (!empty($recentViewers)): ?>
    <div>
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="section-tag green">Views</span>
        <a href="<?= APP_URL ?>/dashboard/views" style="font-size:.8rem;color:var(--green)">See all</a>
      </div>
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:.75rem">Who Viewed My Profile</h5>
      <div class="d-flex flex-column gap-2">
        <?php foreach ($recentViewers as $viewer): ?>
        <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($viewer['profile_id']) ?>" class="mat-card p-3 d-flex align-items-center gap-3 text-decoration-none">
          <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--green-pale)">
            <?php if (!empty($viewer['photo'])): ?>
            <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($viewer['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--green)"><i class="bi bi-person-circle"></i></div>
            <?php endif; ?>
          </div>
          <div class="flex-grow-1">
            <div style="font-weight:700;font-size:.88rem;color:var(--dark)"><?= htmlspecialchars($viewer['name']) ?></div>
            <div style="font-size:.73rem;color:var(--muted)"><?= $viewer['age']??'' ?> <?= !empty($viewer['city']) ? '· '.htmlspecialchars($viewer['city']) : '' ?></div>
          </div>
          <div style="font-size:.72rem;color:var(--muted)"><?= date('d M', strtotime($viewer['viewed_at'])) ?></div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">

<script>
function sendInterest(userId, btn) {
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
  fetch('<?= APP_URL ?>/interest/send', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&receiver_id=${userId}`
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      btn.outerHTML = '<span class="badge-pink flex-grow-1 text-center" style="padding:.4rem"><i class="bi bi-check me-1"></i>Sent</span>';
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-heart"></i> Interest';
      alert(d.message);
    }
  });
}

function respondInterest(interestId, action, btn) {
  btn.disabled = true;
  fetch('<?= APP_URL ?>/interest/respond', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `_csrf=${document.getElementById('csrfToken').value}&interest_id=${interestId}&action=${action}`
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      btn.closest('.mat-card').innerHTML =
        `<div class="text-center w-100" style="font-size:.8rem;color:${action==='accepted'?'var(--green)':'var(--muted)'}">
          <i class="bi bi-${action==='accepted'?'check-circle':'x-circle'}-fill me-1"></i>${action==='accepted'?'Accepted':'Declined'}
        </div>`;
    }
  });
}
</script>
