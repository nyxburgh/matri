<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;--green:#1B8C5E;--green-pale:#E5F7F0;--dark:#1A1028;--text:#3D3046;--muted:#8B7E97}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#fff 0%,var(--pink-pale) 50%,var(--green-pale) 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
h2,h3{font-family:'Playfair Display',serif}
.auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(233,30,140,.15);width:100%;max-width:420px;padding:2.5rem 2rem;text-align:center}
.brand{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:1.5rem}
.icon-wrap{width:72px;height:72px;border-radius:50%;background:var(--pink-pale);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1.2rem;color:var(--pink)}
.form-control{border-radius:12px;border:1.5px solid #EDD7EA;padding:.65rem 1rem;font-size:.9rem;transition:border-color .2s,box-shadow .2s;text-align:left}
.form-control:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(233,30,140,.12);outline:none}
.form-label{font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:.4rem;display:block;text-align:left}
.btn-pink{width:100%;background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.75rem;font-weight:700;font-size:.95rem;box-shadow:0 6px 20px rgba(233,30,140,.3);transition:transform .2s;cursor:pointer}
.btn-pink:hover{transform:translateY(-2px)}
.alert-err{background:#FEE2E2;color:#991B1B;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;text-align:left}
.alert-suc{background:#D1FAE5;color:#065F46;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;text-align:left}
</style>
</head>
<body>
<div class="auth-card">
  <div class="brand"><?= APP_NAME ?></div>
  <div class="icon-wrap"><i class="bi bi-key-fill"></i></div>
  <h2 style="font-size:1.25rem;color:var(--dark);margin-bottom:.4rem">Forgot Password?</h2>
  <p style="color:var(--muted);font-size:.88rem;margin-bottom:1.5rem">Enter your registered email and we'll send an OTP to reset your password.</p>

  <?php if (!empty($error)): ?>
  <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
  <div class="alert-suc"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/forgot-password">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div class="mb-4" style="text-align:left">
      <label class="form-label">Registered Email Address</label>
      <input type="email" name="email" class="form-control" placeholder="you@email.com" required autocomplete="email">
    </div>
    <button type="submit" class="btn-pink mb-3">
      <i class="bi bi-envelope me-1"></i> Send OTP
    </button>
  </form>

  <a href="<?= APP_URL ?>/login" style="font-size:.85rem;color:var(--muted);text-decoration:none">
    <i class="bi bi-arrow-left me-1"></i> Back to Login
  </a>
</div>
</body>
</html>
