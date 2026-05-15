<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <div class="section-tag">Incoming</div>
    <h1 style="font-size:1.35rem">Interests Received</h1>
    <p style="color:var(--muted);font-size:.85rem;margin-top:.2rem"><?= number_format($total) ?> interests received</p>
  </div>
  <a href="<?= APP_URL ?>/interests" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.38rem .9rem">
    <i class="bi bi-arrow-left"></i> All Interests
  </a>
</div>

<!-- Status Filter -->
<div class="d-flex gap-2 mb-4 flex-wrap">
  <?php
  $statuses = [''  =>'All',  'pending'=>'Pending',  'accepted'=>'Accepted',  'rejected'=>'Declined'];
  foreach ($statuses as $val=>$label): ?>
  <a href="?status=<?= $val ?>" class="btn btn-sm"
     style="border-radius:50px;font-size:.8rem;padding:.3rem .9rem;<?= $status===$val?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border:none;color:#fff':'border:1.5px solid var(--border);color:var(--muted);background:#fff' ?>">
    <?= $label ?>
    <?php if ($val==='pending'): ?>
    <?php $pcount = \Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE receiver_id=? AND status='pending'", [Session::get('user_id')])['c']??0; ?>
    <?php if ($pcount>0): ?><span style="background:var(--pink);color:#fff;border-radius:50%;font-size:.6rem;padding:0 4px;margin-left:3px"><?= $pcount ?></span><?php endif; ?>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-heart-arrow" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No interests received yet</h4>
  <p style="color:var(--muted)">Complete your profile and add photos to attract more matches.</p>
  <a href="<?= APP_URL ?>/profile/edit" class="btn-pink mt-2" style="display:inline-block;padding:.6rem 1.5rem">Improve Profile</a>
</div>
<?php else: ?>
<div class="row g-3 mb-4">
  <?php foreach ($data as $int): ?>
  <div class="col-12 col-md-6" id="int-<?= $int['id'] ?>">
    <div class="mat-card p-3 d-flex align-items-start gap-3">
      <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>">
        <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--pink-pale)">
          <?php if (!empty($int['photo'])): ?>
            <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($int['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
          <?php endif; ?>
        </div>
      </a>
      <div class="flex-grow-1 overflow-hidden">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" style="font-weight:700;font-size:.95rem;color:var(--dark)"><?= htmlspecialchars($int['name']) ?></a>
          <span style="font-size:.72rem;font-weight:700;padding:.22rem .65rem;border-radius:50px;flex-shrink:0;margin-left:.5rem;background:<?= ['pending'=>'#FEF3C7','accepted'=>'#D1FAE5','rejected'=>'#FEE2E2'][$int['status']]??'#F3F4F6' ?>;color:<?= ['pending'=>'#92400E','accepted'=>'#065F46','rejected'=>'#991B1B'][$int['status']]??'#6B7280' ?>">
            <?= ucfirst($int['status']) ?>
          </span>
        </div>
        <div style="font-size:.78rem;color:var(--muted)">
          <?= $int['age']??'' ?><?= !empty($int['city'])?' · '.htmlspecialchars($int['city']):'' ?>
          <?= !empty($int['religion'])?' · '.htmlspecialchars($int['religion']):'' ?>
        </div>
        <div style="font-size:.76rem;color:var(--muted)"><?= htmlspecialchars($int['education']??'') ?><?= !empty($int['occupation'])?' · '.htmlspecialchars($int['occupation']):'' ?></div>
        <?php if (!empty($int['message'])): ?>
        <div style="font-size:.78rem;color:var(--text);margin-top:.4rem;background:var(--pink-pale);border-radius:8px;padding:.35rem .6rem;font-style:italic">
          "<?= htmlspecialchars(mb_substr($int['message'],0,120)) ?>"
        </div>
        <?php endif; ?>
        <div class="mt-2">
          <?php if ($int['status'] === 'pending'): ?>
          <div class="d-flex gap-2 flex-wrap">
            <button onclick="respond(<?= $int['id'] ?>,'accepted')" class="btn-green d-inline-flex align-items-center gap-1" style="font-size:.8rem;padding:.3rem .9rem">
              <i class="bi bi-check-circle"></i> Accept
            </button>
            <button onclick="respond(<?= $int['id'] ?>,'rejected')" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.8rem;padding:.3rem .9rem">
              <i class="bi bi-x-circle"></i> Decline
            </button>
            <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.78rem;padding:.3rem .7rem">
              View Profile
            </a>
          </div>
          <?php elseif ($int['status'] === 'accepted'): ?>
          <div class="d-flex gap-2 align-items-center">
            <a href="<?= APP_URL ?>/chat/<?= $int['uid'] ?>" class="btn-green d-inline-flex align-items-center gap-1" style="font-size:.8rem;padding:.3rem .9rem">
              <i class="bi bi-chat-dots-fill"></i> Chat Now
            </a>
            <span style="font-size:.72rem;color:var(--green)"><i class="bi bi-check-circle-fill me-1"></i>Connected</span>
          </div>
          <?php else: ?>
          <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($int['profile_id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:.78rem;padding:.3rem .8rem">View Profile</a>
          <?php endif; ?>
          <div style="font-size:.7rem;color:var(--muted);margin-top:.35rem"><i class="bi bi-clock me-1"></i><?= date('d M Y, g:i a', strtotime($int['sent_at'])) ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page>1): ?><li class="page-item"><a class="page-link" href="?status=<?= $status ?>&page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?status=<?= $status ?>&page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page<$last_page): ?><li class="page-item"><a class="page-link" href="?status=<?= $status ?>&page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<script>
function respond(id, action) {
  const card = document.getElementById('int-'+id);
  if(!confirm(action==='accepted'?'Accept this interest?':'Decline this interest?')) return;

  fetch('<?= APP_URL ?>/interest/respond', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf=${document.getElementById('csrfToken').value}&interest_id=${id}&action=${action}`
  }).then(r=>r.json()).then(d=>{
    if(d.success) {
      // Update status badge
      const badge = card.querySelector('[style*="font-weight:700;padding:.22rem"]');
      if (badge) {
        const colors = {accepted:{bg:'#D1FAE5',color:'#065F46'},rejected:{bg:'#FEE2E2',color:'#991B1B'}};
        const c = colors[action];
        badge.style.background = c.bg;
        badge.style.color = c.color;
        badge.textContent = action==='accepted'?'Accepted':'Declined';
      }
      // Update action buttons
      const btns = card.querySelector('.d-flex.gap-2.flex-wrap');
      if (btns) {
        if (action==='accepted' && d.can_chat) {
          btns.innerHTML=`<a href="<?= APP_URL ?>/chat/${d.partner_id||''}" class="btn-green d-inline-flex align-items-center gap-1" style="font-size:.8rem;padding:.3rem .9rem"><i class="bi bi-chat-dots-fill"></i> Chat Now</a>`;
        } else {
          btns.innerHTML='<span style="color:var(--muted);font-size:.8rem">Declined</span>';
        }
      }
    } else {
      alert(d.message);
    }
  });
}
</script>
