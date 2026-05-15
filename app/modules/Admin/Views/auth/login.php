<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  body{background:linear-gradient(135deg,#6a0572 0%,#c0392b 100%);min-height:100vh;display:flex;align-items:center;}
  .login-card{border:none;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
  .login-brand{background:linear-gradient(135deg,#6a0572,#c0392b);border-radius:16px 16px 0 0;padding:2rem;text-align:center;}
  .btn-login{background:linear-gradient(135deg,#6a0572,#c0392b);border:none;padding:.75rem;}
  .btn-login:hover{opacity:.9;}
  .form-control:focus{border-color:#6a0572;box-shadow:0 0 0 .2rem rgba(106,5,114,.15);}
</style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      <div class="card login-card">
        <div class="login-brand">
          <i class="bi bi-heart-fill text-white fs-1"></i>
          <h4 class="text-white mb-0 mt-2"><?= APP_NAME ?></h4>
          <small class="text-white-50">Admin Control Panel</small>
        </div>
        <div class="card-body p-4">
          <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
          </div>
          <?php endif; ?>
          <form method="POST" action="<?= APP_URL ?>/admin/login" novalidate>
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required autofocus>
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()">
                  <i class="bi bi-eye" id="pwdIcon"></i>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-login btn-primary w-100 text-white fw-bold">
              <i class="bi bi-shield-lock me-2"></i>Sign In
            </button>
          </form>
        </div>
        <div class="card-footer text-center py-3 bg-light rounded-bottom">
          <small class="text-muted"><?= APP_NAME ?> &copy; <?= date('Y') ?> — Secure Admin Access</small>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd(){
  const p=document.getElementById('password'),i=document.getElementById('pwdIcon');
  p.type=p.type==='password'?'text':'password';
  i.className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash';
}
</script>
</body>
</html>
