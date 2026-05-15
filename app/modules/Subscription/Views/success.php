<!-- success.php -->
<div class="text-center" style="max-width:520px;margin:2rem auto">
  <?php if (!empty($flash)): ?>
  <div class="flash-success mb-4"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>
  <div style="width:96px;height:96px;border-radius:50%;background:var(--green-pale);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:3rem;color:var(--green)">
    <i class="bi bi-gem"></i>
  </div>
  <h1 style="font-size:1.6rem;color:var(--dark);margin-bottom:.5rem">Welcome to <?= htmlspecialchars($plan['plan_name'] ?? 'Premium') ?>! 🎉</h1>
  <p style="color:var(--muted);font-size:.92rem;margin-bottom:2rem">Your subscription is active. Enjoy all premium features to find your perfect match.</p>

  <?php if (!empty($plan['end_date'])): ?>
  <div class="mat-card p-4 mb-4" style="background:linear-gradient(135deg,var(--green-pale),var(--pink-pale))">
    <div style="font-size:.82rem;color:var(--muted)">Valid Until</div>
    <div style="font-size:1.3rem;font-weight:900;color:var(--green)"><?= date('d F Y', strtotime($plan['end_date'])) ?></div>
  </div>
  <?php endif; ?>

  <div class="d-flex flex-column gap-3">
    <a href="<?= APP_URL ?>/search" class="btn-pink" style="padding:.7rem;font-size:1rem">
      <i class="bi bi-search me-1"></i> Browse Matches Now
    </a>
    <a href="<?= APP_URL ?>/dashboard" class="btn-outline-pink" style="padding:.65rem">
      <i class="bi bi-house me-1"></i> Go to Dashboard
    </a>
  </div>
</div>
