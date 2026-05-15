<div class="page-header">
  <div class="section-tag">Insights</div>
  <h1 style="font-size:1.35rem">Who Viewed My Profile</h1>
  <p style="color:var(--muted);font-size:.85rem;margin-top:.25rem"><?= number_format($total) ?> people viewed your profile</p>
</div>

<?php if (empty($data)): ?>
<div class="mat-card p-5 text-center">
  <i class="bi bi-eye-slash" style="font-size:3rem;color:var(--pink-pale)"></i>
  <h4 class="mt-3" style="color:var(--dark)">No profile views yet</h4>
  <p style="color:var(--muted)">Complete your profile and add photos to attract more visitors.</p>
  <a href="<?= APP_URL ?>/profile/edit" class="btn-pink mt-2" style="display:inline-block;padding:.6rem 1.5rem">Improve Profile</a>
</div>
<?php else: ?>
<div class="row g-3 mb-4">
  <?php foreach ($data as $v): ?>
  <div class="col-12 col-sm-6 col-lg-4">
    <a href="<?= APP_URL ?>/profile/<?= htmlspecialchars($v['profile_id']) ?>" class="mat-card p-3 d-flex align-items-center gap-3 text-decoration-none">
      <div style="width:56px;height:56px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--pink-pale)">
        <?php if (!empty($v['photo'])): ?>
          <img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($v['photo']) ?>" style="width:100%;height:100%;object-fit:cover" alt="">
        <?php else: ?>
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:var(--muted)"><i class="bi bi-person-circle"></i></div>
        <?php endif; ?>
      </div>
      <div class="flex-grow-1 overflow-hidden">
        <div style="font-weight:700;font-size:.92rem;color:var(--dark)"><?= htmlspecialchars($v['name']) ?></div>
        <div style="font-size:.78rem;color:var(--muted)"><?= $v['age'] ?? '' ?><?= !empty($v['city']) ? ' · '.htmlspecialchars($v['city']) : '' ?></div>
        <div style="font-size:.78rem;color:var(--muted)"><?= htmlspecialchars($v['religion'] ?? '') ?></div>
      </div>
      <div style="font-size:.72rem;color:var(--muted);text-align:right;flex-shrink:0">
        <?= date('d M', strtotime($v['viewed_at'])) ?><br>
        <span style="font-size:.68rem"><?= date('g:i a', strtotime($v['viewed_at'])) ?></span>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<?php if ($last_page > 1): ?>
<nav><ul class="pagination justify-content-center gap-1">
  <?php if ($current_page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page-1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
  <?php for ($i=max(1,$current_page-2);$i<=min($last_page,$current_page+2);$i++): ?>
  <li class="page-item <?= $i===$current_page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>" style="border-radius:8px;<?= $i===$current_page?'background:linear-gradient(135deg,var(--pink),var(--pink-light));border-color:var(--pink)':'border-color:var(--border)' ?>"><?= $i ?></a></li>
  <?php endfor; ?>
  <?php if ($current_page < $last_page): ?><li class="page-item"><a class="page-link" href="?page=<?= $current_page+1 ?>" style="border-radius:8px;border-color:var(--border)"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>
