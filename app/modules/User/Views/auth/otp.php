<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP — <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;--green:#1B8C5E;--green-pale:#E5F7F0;--dark:#1A1028;--text:#3D3046;--muted:#8B7E97}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#fff 0%,var(--pink-pale) 50%,var(--green-pale) 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
h1,h2,h3{font-family:'Playfair Display',serif}
.otp-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(233,30,140,.15);width:100%;max-width:400px;padding:2.5rem 2rem;text-align:center}
.brand{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:1.5rem}
.otp-icon{width:72px;height:72px;border-radius:50%;background:var(--pink-pale);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1.2rem;color:var(--pink)}
.otp-inputs{display:flex;gap:.6rem;justify-content:center;margin:1.5rem 0}
.otp-input{width:52px;height:60px;border:2px solid #EDD7EA;border-radius:12px;text-align:center;font-size:1.6rem;font-weight:700;color:var(--dark);transition:border-color .2s;outline:none}
.otp-input:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(233,30,140,.12)}
.otp-input.filled{border-color:var(--pink);background:var(--pink-pale)}
.btn-pink{width:100%;background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.75rem;font-weight:700;font-size:.95rem;box-shadow:0 6px 20px rgba(233,30,140,.3);transition:transform .2s}
.btn-pink:hover{transform:translateY(-2px)}
.resend-area{margin-top:1.2rem;font-size:.85rem;color:var(--muted)}
#resendBtn{background:none;border:none;color:var(--pink);font-weight:600;cursor:pointer;font-size:.85rem;padding:0}
#resendBtn:disabled{color:var(--muted);cursor:not-allowed}
.alert-err{background:#FEE2E2;color:#991B1B;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;text-align:left}
.alert-suc{background:#D1FAE5;color:#065F46;border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;text-align:left}
</style>
</head>
<body>
<div class="otp-card">
  <div class="brand"><?= APP_NAME ?></div>

  <div class="otp-icon">
    <?php if ($purpose === 'login'): ?>
      <i class="bi bi-shield-lock"></i>
    <?php elseif ($purpose === 'register'): ?>
      <i class="bi bi-envelope-check"></i>
    <?php else: ?>
      <i class="bi bi-key"></i>
    <?php endif; ?>
  </div>

  <h2 style="font-size:1.25rem;color:var(--dark);margin-bottom:.5rem">
    <?= $purpose === 'register' ? 'Verify Your Email' : 'Enter OTP' ?>
  </h2>
  <p style="color:var(--muted);font-size:.88rem;margin-bottom:.5rem">
    We sent a 6-digit OTP to<br>
    <strong style="color:var(--dark)"><?= htmlspecialchars($target ?? '') ?></strong>
  </p>

  <?php if (!empty($error)): ?>
  <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
  <div class="alert-suc"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/verify-otp" id="otpForm">
    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <input type="hidden" name="otp_code" id="otpHidden">

    <div class="otp-inputs" id="otpInputs">
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" data-pos="<?= $i ?>" autocomplete="one-time-code">
      <?php endfor; ?>
    </div>

    <!-- Countdown -->
    <div style="font-size:.85rem;color:var(--muted);margin-bottom:.5rem">
      OTP expires in <span id="countdown" style="color:var(--pink);font-weight:700">10:00</span>
    </div>

    <button type="submit" class="btn-pink" id="verifyBtn">
      <i class="bi bi-check-circle me-1"></i> Verify OTP
    </button>
  </form>

  <div class="resend-area">
    Didn't receive it?
    <button id="resendBtn" disabled onclick="resendOtp()">Resend OTP</button>
    <span id="resendTimer">(wait <span id="resendCountdown">30</span>s)</span>
  </div>

  <div class="mt-3">
    <a href="<?= APP_URL ?>/login" style="font-size:.82rem;color:var(--muted)"><i class="bi bi-arrow-left me-1"></i> Back to Login</a>
  </div>
</div>

<script>
// OTP input auto-advance
const inputs = document.querySelectorAll('.otp-input');
inputs.forEach((inp, idx) => {
  inp.addEventListener('input', e => {
    const val = e.target.value.replace(/\D/g,'');
    e.target.value = val.slice(-1);
    e.target.classList.toggle('filled', val !== '');
    if (val && idx < inputs.length - 1) inputs[idx + 1].focus();
    updateHidden();
  });
  inp.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
      inputs[idx - 1].focus();
      inputs[idx - 1].value = '';
      inputs[idx - 1].classList.remove('filled');
      updateHidden();
    }
  });
  inp.addEventListener('paste', e => {
    e.preventDefault();
    const pasted = e.clipboardData.getData('text').replace(/\D/g,'').slice(0, 6);
    pasted.split('').forEach((ch, i) => {
      if (inputs[i]) { inputs[i].value = ch; inputs[i].classList.add('filled'); }
    });
    updateHidden();
    if (pasted.length === 6) document.getElementById('verifyBtn').focus();
  });
});

function updateHidden() {
  document.getElementById('otpHidden').value = Array.from(inputs).map(i => i.value).join('');
}

document.getElementById('otpForm').addEventListener('submit', e => {
  const code = document.getElementById('otpHidden').value;
  if (code.length !== 6) { e.preventDefault(); alert('Please enter all 6 digits.'); }
});

// Main countdown (10 min)
let secs = 600;
const cntEl = document.getElementById('countdown');
const cntInt = setInterval(() => {
  secs--;
  if (secs <= 0) { clearInterval(cntInt); cntEl.textContent = 'Expired'; return; }
  cntEl.textContent = String(Math.floor(secs/60)).padStart(2,'0')+':'+String(secs%60).padStart(2,'0');
}, 1000);

// Resend countdown (30s)
let resendSecs = 30;
const resendEl = document.getElementById('resendCountdown');
const resendTimerEl = document.getElementById('resendTimer');
const resendBtn = document.getElementById('resendBtn');
const resendInt = setInterval(() => {
  resendSecs--;
  resendEl.textContent = resendSecs;
  if (resendSecs <= 0) {
    clearInterval(resendInt);
    resendBtn.disabled = false;
    resendTimerEl.style.display = 'none';
  }
}, 1000);

function resendOtp() {
  resendBtn.disabled = true;
  resendBtn.textContent = 'Sending...';
  fetch('<?= APP_URL ?>/resend-otp', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: '_csrf=<?= $csrf ?>'
  })
  .then(r => r.json())
  .then(d => {
    resendBtn.textContent = 'Resend OTP';
    alert(d.message);
    if (d.success) { secs = 600; resendSecs = 30; resendTimerEl.style.display = ''; }
  });
}
</script>
</body>
</html>
