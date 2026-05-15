<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;--green:#1B8C5E;--green-light:#27C47F;--green-pale:#E5F7F0;--dark:#1A1028;--text:#3D3046;--muted:#8B7E97}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;color:var(--text);min-height:100vh;background:linear-gradient(135deg,#fff 0%,var(--pink-pale) 50%,var(--green-pale) 100%);display:flex;align-items:center;justify-content:center;padding:1rem}
h1,h2,h3{font-family:'Playfair Display',serif}
.auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(233,30,140,.15);width:100%;max-width:420px;padding:2.5rem 2rem}
.brand{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center;margin-bottom:.5rem}
.auth-card h2{font-size:1.35rem;text-align:center;color:var(--dark);margin-bottom:.4rem}
.auth-subtitle{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:1.8rem}
.form-control{border-radius:12px;border:1.5px solid #EDD7EA;padding:.65rem 1rem;font-size:.9rem;transition:border-color .2s,box-shadow .2s}
.form-control:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(233,30,140,.12);outline:none}
.form-label{font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:.4rem}
.btn-pink{width:100%;background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.75rem;font-weight:700;font-size:.95rem;transition:transform .2s,box-shadow .2s;box-shadow:0 6px 20px rgba(233,30,140,.3)}
.btn-pink:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(233,30,140,.4)}
.tab-btn{flex:1;background:transparent;border:none;border-bottom:2px solid #EDD7EA;padding:.6rem;font-size:.88rem;font-weight:600;color:var(--muted);cursor:pointer;transition:all .2s;border-radius:0}
.tab-btn.active{color:var(--pink);border-bottom-color:var(--pink)}
.social-btn{width:100%;display:flex;align-items:center;justify-content:center;gap:.6rem;border:1.5px solid #EDD7EA;border-radius:12px;padding:.65rem;background:#fff;font-weight:600;font-size:.88rem;cursor:pointer;transition:border-color .2s,background .2s;color:var(--text)}
.social-btn:hover{border-color:var(--pink);background:var(--pink-pale)}
.divider{display:flex;align-items:center;gap:1rem;margin:1rem 0;color:var(--muted);font-size:.8rem}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#EDD7EA}
.alert-err{background:#FEE2E2;color:#991B1B;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem}
.alert-suc{background:#D1FAE5;color:#065F46;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem}
</style>
</head>
<body>
<div class="auth-card">
  <div class="brand"><?= APP_NAME ?></div>
  <h2>Welcome Back</h2>
  <p class="auth-subtitle">Sign in to find your perfect match</p>

  <?php if (!empty($error)): ?>
  <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
  <div class="alert-suc"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- Tab switcher -->
  <div class="d-flex mb-3">
    <button class="tab-btn active" id="tabPassword" onclick="switchTab('password')">Password</button>
    <button class="tab-btn" id="tabOtp" onclick="switchTab('otp')">OTP Login</button>
  </div>

  <form method="POST" action="<?= APP_URL ?>/login" id="loginForm">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <input type="hidden" name="login_type" id="loginType" value="password">

    <div class="mb-3">
      <label class="form-label">Email / Mobile</label>
      <input type="text" name="identifier" class="form-control" placeholder="you@email.com or 9876543210" required autocomplete="username">
    </div>

    <div id="passwordField" class="mb-3">
      <label class="form-label">Password</label>
      <div class="position-relative">
        <input type="password" name="password" id="pwdInput" class="form-control pe-5" placeholder="Enter password" autocomplete="current-password">
        <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 p-1" onclick="togglePwd()" style="color:var(--muted)">
          <i class="bi bi-eye" id="eyeIcon"></i>
        </button>
      </div>
      <div class="text-end mt-1">
        <a href="<?= APP_URL ?>/forgot-password" style="font-size:.8rem;color:var(--pink)">Forgot Password?</a>
      </div>
    </div>

    <button type="submit" class="btn-pink mb-3" id="submitBtn">
      <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
    </button>
  </form>

  <div class="divider">or continue with</div>

  <div class="d-flex flex-column gap-2 mb-3">
    <a href="<?= APP_URL ?>/auth/google" class="social-btn">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Continue with Google
    </a>
    <a href="<?= APP_URL ?>/auth/facebook" class="social-btn">
      <i class="bi bi-facebook" style="color:#1877F2;font-size:1.1rem"></i>
      Continue with Facebook
    </a>
  </div>

  <p class="text-center" style="font-size:.85rem;color:var(--muted)">
    Don't have an account? <a href="<?= APP_URL ?>/register" style="color:var(--pink);font-weight:600">Register Free</a>
  </p>
</div>

<script>
function switchTab(type) {
  document.getElementById('loginType').value = type;
  const isOtp = type === 'otp';
  document.getElementById('tabPassword').classList.toggle('active', !isOtp);
  document.getElementById('tabOtp').classList.toggle('active', isOtp);
  document.getElementById('passwordField').style.display = isOtp ? 'none' : '';
  document.getElementById('submitBtn').innerHTML = isOtp
    ? '<i class="bi bi-phone me-1"></i> Send OTP'
    : '<i class="bi bi-box-arrow-in-right me-1"></i> Sign In';
  document.querySelector('[name="password"]').required = !isOtp;
}

function togglePwd() {
  const inp = document.getElementById('pwdInput');
  const ico = document.getElementById('eyeIcon');
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  ico.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
</body>
</html>
