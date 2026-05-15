<!-- checkout.php -->
<div class="row justify-content-center">
  <div class="col-12 col-lg-8 col-xl-7">
    <div class="page-header mb-4">
      <a href="<?= APP_URL ?>/subscription" style="font-size:.85rem;color:var(--muted)"><i class="bi bi-arrow-left me-1"></i> Back to Plans</a>
      <div class="section-tag mt-2">Secure Payment</div>
      <h1 style="font-size:1.3rem">Checkout — <?= htmlspecialchars($plan['name']) ?> Plan</h1>
    </div>

    <div class="row g-4">
      <!-- Order Summary -->
      <div class="col-12 col-md-5">
        <div class="mat-card p-4">
          <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:1rem">Order Summary</h5>
          <div style="background:var(--pink-pale);border-radius:12px;padding:1rem;text-align:center;margin-bottom:1rem">
            <div style="font-size:.8rem;color:var(--muted)"><?= htmlspecialchars($plan['name']) ?> Plan</div>
            <div style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">₹<?= number_format($plan['price'], 0) ?></div>
            <div style="font-size:.78rem;color:var(--muted)"><?= $plan['duration_days'] ?> days validity</div>
          </div>
          <ul class="list-unstyled" style="font-size:.83rem">
            <?php
            $f = [
              [$plan['interests_limit']<0?'Unlimited':$plan['interests_limit'].' Interests','bi-heart-fill','var(--pink)'],
              [$plan['chat_limit']?'Unlimited Chat':'Chat Locked','bi-chat-dots-fill',$plan['chat_limit']?'var(--green)':'#CBD5E1'],
              [$plan['contact_view']?'Contact Details':'No Contact','bi-telephone-fill',$plan['contact_view']?'var(--green)':'#CBD5E1'],
              [$plan['advanced_search']?'Advanced Search':'Basic Search','bi-search',$plan['advanced_search']?'var(--green)':'#CBD5E1'],
              [$plan['highlight']?'Profile Highlighted':'No Highlight','bi-star-fill',$plan['highlight']?'#F57F17':'#CBD5E1'],
            ];
            foreach ($f as [$label, $icon, $color]): ?>
            <li class="d-flex align-items-center gap-2 mb-2">
              <i class="<?= $icon ?>" style="color:<?= $color ?>;width:16px"></i>
              <span style="color:<?= $color==='#CBD5E1'?'var(--muted)':'var(--text)' ?>"><?= $label ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <hr style="border-color:var(--border)">
          <div class="d-flex justify-content-between">
            <span style="font-weight:700">Total</span>
            <span style="font-weight:900;color:var(--pink)">₹<?= number_format($plan['price'], 0) ?></span>
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="col-12 col-md-7">
        <div class="mat-card p-4">
          <h5 style="font-size:.95rem;font-weight:700;color:var(--dark);margin-bottom:.5rem">Secure Payment</h5>
          <p style="font-size:.8rem;color:var(--muted);margin-bottom:1.2rem">
            <i class="bi bi-shield-lock me-1" style="color:var(--green)"></i>256-bit SSL encrypted · Powered by Razorpay
          </p>

          <div id="paymentError" class="flash-error mb-3" style="display:none"></div>
          <div id="paymentSuccess" class="flash-success mb-3" style="display:none"></div>

          <button id="payBtn" onclick="initiatePayment()" class="btn-pink w-100" style="padding:.75rem;font-size:1rem">
            <i class="bi bi-credit-card me-2"></i> Pay ₹<?= number_format($plan['price'], 0) ?> Securely
          </button>

          <div class="d-flex justify-content-center gap-3 mt-3">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/visa.svg" height="20" alt="Visa" style="opacity:.5">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/mastercard.svg" height="20" alt="Mastercard" style="opacity:.5">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/razorpay.svg" height="20" alt="Razorpay" style="opacity:.5">
          </div>
          <p style="font-size:.72rem;color:var(--muted);text-align:center;margin-top:.75rem">
            By completing payment you agree to our Terms of Service. 7-day money-back guarantee.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="csrfToken" value="<?= $csrf ?>">
<input type="hidden" id="planId" value="<?= $plan['id'] ?>">

<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function initiatePayment() {
  const btn = document.getElementById('payBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Processing...';

  try {
    const res = await fetch('<?= APP_URL ?>/subscription/initiate', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `_csrf=${document.getElementById('csrfToken').value}&plan_id=${document.getElementById('planId').value}`
    });
    const data = await res.json();

    if (!data.success) throw new Error(data.message || 'Failed to create order.');

    const options = {
      key: '<?= htmlspecialchars($razorpayKey) ?>',
      amount: data.amount,
      currency: data.currency,
      name: '<?= APP_NAME ?>',
      description: data.plan_name + ' Plan',
      order_id: data.order_id,
      prefill: { name: data.user_name, email: data.user_email, contact: data.user_mobile },
      theme: { color: '#E91E8C' },
      handler: function(response) {
        verifyPayment(data.payment_id, response);
      },
      modal: {
        ondismiss: function() {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-credit-card me-2"></i> Pay ₹<?= number_format($plan['price'],0) ?> Securely';
        }
      }
    };

    const rzp = new Razorpay(options);
    rzp.open();

  } catch(e) {
    showError(e.message);
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-credit-card me-2"></i> Pay ₹<?= number_format($plan['price'],0) ?> Securely';
  }
}

async function verifyPayment(paymentDbId, rzpResponse) {
  const btn = document.getElementById('payBtn');
  btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Verifying...';

  try {
    const res = await fetch('<?= APP_URL ?>/subscription/verify', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `_csrf=${document.getElementById('csrfToken').value}&payment_db_id=${paymentDbId}&razorpay_payment_id=${rzpResponse.razorpay_payment_id}&razorpay_order_id=${rzpResponse.razorpay_order_id}&razorpay_signature=${rzpResponse.razorpay_signature}`
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      showError(data.message);
    }
  } catch(e) {
    showError('Verification failed. Contact support.');
  }
}

function showError(msg) {
  const el = document.getElementById('paymentError');
  el.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + msg;
  el.style.display = 'flex';
}
</script>
