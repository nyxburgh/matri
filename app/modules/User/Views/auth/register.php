<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Free — <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;--green:#1B8C5E;--green-light:#27C47F;--green-pale:#E5F7F0;--dark:#1A1028;--text:#3D3046;--muted:#8B7E97}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;color:var(--text);min-height:100vh;background:linear-gradient(135deg,#fff 0%,var(--pink-pale) 50%,var(--green-pale) 100%);display:flex;align-items:center;justify-content:center;padding:1.5rem 1rem}
h1,h2,h3{font-family:'Playfair Display',serif}
.auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(233,30,140,.15);width:100%;max-width:480px;padding:2.5rem 2rem}
.brand{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center;margin-bottom:.5rem}
.form-control,.form-select{border-radius:12px;border:1.5px solid #EDD7EA;padding:.65rem 1rem;font-size:.9rem;transition:border-color .2s,box-shadow .2s}
.form-control:focus,.form-select:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(233,30,140,.12);outline:none}
.form-label{font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:.4rem}
.btn-pink{width:100%;background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.75rem;font-weight:700;font-size:.95rem;transition:transform .2s,box-shadow .2s;box-shadow:0 6px 20px rgba(233,30,140,.3)}
.btn-pink:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(233,30,140,.4)}
.gender-radio input{display:none}
.gender-radio label{cursor:pointer;border:1.5px solid #EDD7EA;border-radius:10px;padding:.5rem 1rem;font-size:.85rem;font-weight:600;color:var(--muted);transition:all .2s;display:flex;align-items:center;gap:.4rem}
.gender-radio input:checked+label{border-color:var(--pink);background:var(--pink-pale);color:var(--pink)}
.alert-err{background:#FEE2E2;color:#991B1B;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1.2rem;display:flex;align-items:center;gap:.5rem}
.pwd-strength{height:4px;border-radius:4px;margin-top:.4rem;background:#EDD7EA;transition:all .3s}
.step-badge{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:var(--pink-pale);color:var(--pink);font-size:.78rem;font-weight:700;flex-shrink:0}
</style>
</head>
<body>
<div class="auth-card">
  <div class="brand"><?= APP_NAME ?></div>
  <h2 class="text-center mb-1" style="font-size:1.3rem;color:var(--dark)">Create Free Account</h2>
  <p class="text-center mb-4" style="color:var(--muted);font-size:.85rem">Find your life partner — 100% free to join</p>

  <?php if (!empty($error)): ?>
  <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/register" id="regForm">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">

    <!-- Account for -->
    <div class="mb-3">
      <label class="form-label">Registering for</label>
      <select name="account_for" class="form-select">
        <option value="self" <?= ($old['for']??'self')==='self'?'selected':'' ?>>Myself</option>
        <option value="son"  <?= ($old['for']??'')==='son'?'selected':'' ?>>My Son</option>
        <option value="daughter" <?= ($old['for']??'')==='daughter'?'selected':'' ?>>My Daughter</option>
        <option value="relative" <?= ($old['for']??'')==='relative'?'selected':'' ?>>My Relative</option>
      </select>
    </div>

    <!-- Gender -->
    <div class="mb-3">
      <label class="form-label">Bride / Groom</label>
      <div class="d-flex gap-2">
        <div class="gender-radio flex-grow-1">
          <input type="radio" name="gender" id="gBride" value="female" <?= ($old['gender']??'')==='female'?'checked':'' ?>>
          <label for="gBride" class="w-100 justify-content-center"><i class="bi bi-gender-female"></i> Bride</label>
        </div>
        <div class="gender-radio flex-grow-1">
          <input type="radio" name="gender" id="gGroom" value="male" <?= ($old['gender']??'')==='male'?'checked':'' ?>>
          <label for="gGroom" class="w-100 justify-content-center"><i class="bi bi-gender-male"></i> Groom</label>
        </div>
      </div>
    </div>

    <!-- Name -->
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" placeholder="Enter full name" value="<?= htmlspecialchars($old['name']??'') ?>" required minlength="2">
    </div>

    <!-- Email -->
    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" placeholder="you@email.com" value="<?= htmlspecialchars($old['email']??'') ?>" required autocomplete="email">
    </div>

    <!-- Mobile -->
    <div class="mb-3">
      <label class="form-label">Mobile Number</label>
      <div class="input-group">
        <span class="input-group-text" style="background:var(--pink-pale);border-color:#EDD7EA;color:var(--pink);font-weight:600">+91</span>
        <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile" value="<?= htmlspecialchars($old['mobile']??'') ?>" required pattern="[0-9]{10}" maxlength="10">
      </div>
    </div>

    <!-- Password -->
    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="position-relative">
        <input type="password" name="password" id="pwdInp" class="form-control pe-5" placeholder="Min. 8 characters" required minlength="8" oninput="checkStrength(this.value)">
        <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 p-1" onclick="togglePwd('pwdInp','eye1')" style="color:var(--muted)">
          <i class="bi bi-eye" id="eye1"></i>
        </button>
      </div>
      <div class="pwd-strength" id="pwdBar"></div>
      <div id="pwdHint" style="font-size:.75rem;color:var(--muted);margin-top:.25rem"></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <div class="position-relative">
        <input type="password" name="confirm_password" id="cpwdInp" class="form-control pe-5" placeholder="Repeat password" required minlength="8">
        <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 p-1" onclick="togglePwd('cpwdInp','eye2')" style="color:var(--muted)">
          <i class="bi bi-eye" id="eye2"></i>
        </button>
      </div>
    </div>

    <!-- Language -->
    <div class="mb-3">
      <label class="form-label">Preferred Language</label>
      <select name="lang" class="form-select">
        <option value="en">English</option>
        <option value="ta">தமிழ் (Tamil)</option>
      </select>
    </div>

    <div class="mb-3" style="font-size:.78rem;color:var(--muted)">
      By registering you agree to our <a href="#" style="color:var(--pink)">Terms of Service</a> and <a href="#" style="color:var(--pink)">Privacy Policy</a>.
    </div>

    <button type="submit" class="btn-pink mb-3">
      <i class="bi bi-envelope-check me-1"></i> Register & Verify OTP
    </button>
  </form>

  <p class="text-center" style="font-size:.85rem;color:var(--muted)">
    Already have an account? <a href="<?= APP_URL ?>/login" style="color:var(--pink);font-weight:600">Sign In</a>
  </p>
</div>

<script>
function togglePwd(id, iconId) {
  const inp = document.getElementById(id);
  const ico = document.getElementById(iconId);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
  const bar  = document.getElementById('pwdBar');
  const hint = document.getElementById('pwdHint');
  let score  = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const colors = ['#fee2e2','#fef3c7','#d1fae5','#059669'];
  const labels = ['Too short','Weak','Good','Strong'];
  bar.style.width = (score * 25) + '%';
  bar.style.background = colors[score - 1] || '#EDD7EA';
  hint.textContent = val ? labels[score - 1] || '' : '';
  hint.style.color = colors[score - 1] || 'var(--muted)';
}
</script>
</body>
</html>
