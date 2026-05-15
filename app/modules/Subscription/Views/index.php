<div class="page-header text-center">
  <div class="section-tag mx-auto" style="display:inline-block">Upgrade</div>
  <h1 style="font-size:1.6rem;margin-bottom:.5rem">Choose Your Plan</h1>
  <p style="color:var(--muted);font-size:.92rem">Unlock premium features to find your perfect match faster</p>
</div>

<?php if (!empty($flash)): ?>
<div class="flash-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Plan Cards -->
<div class="row g-4 justify-content-center mb-5">
  <?php foreach ($plans as $plan):
    $isCurrent = ($currentPlan['plan_code'] ?? 'FREE') === $plan['code'];
    $isPop = $plan['code'] === 'GOLD';
  ?>
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="mat-card p-0 h-100 d-flex flex-column position-relative overflow-hidden <?= $isPop ? '' : '' ?>"
         style="<?= $isPop ? 'border:2px solid var(--pink);box-shadow:var(--shadow-pink)' : '' ?>">

      <?php if ($isPop): ?>
      <div style="background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;text-align:center;font-size:.75rem;font-weight:700;padding:.35rem;letter-spacing:.1em">
        ⭐ MOST POPULAR
      </div>
      <?php endif; ?>

      <div class="p-4 flex-grow-1">
        <!-- Plan Name & Icon -->
        <div class="text-center mb-3">
          <?php
          $icons = ['FREE'=>'bi-person','SILVER'=>'bi-gem','GOLD'=>'bi-star-fill','PLAT'=>'bi-award-fill'];
          $colors= ['FREE'=>'var(--muted)','SILVER'=>'#94A3B8','GOLD'=>'var(--green)','PLAT'=>'#7C3AED'];
          ?>
          <i class="<?= $icons[$plan['code']]??'bi-gem' ?>" style="font-size:2rem;color:<?= $colors[$plan['code']]??'var(--pink)' ?>"></i>
          <h3 style="font-size:1.15rem;margin:.5rem 0 0;color:var(--dark)"><?= htmlspecialchars($plan['name']) ?></h3>
          <?php if ($plan['duration_days'] > 0): ?>
          <div style="font-size:.75rem;color:var(--muted)"><?= $plan['duration_days'] ?> days</div>
          <?php endif; ?>
        </div>

        <!-- Price -->
        <div class="text-center mb-4">
          <?php if ($plan['price'] > 0): ?>
          <div style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1">
            ₹<?= number_format($plan['price'],0) ?>
          </div>
          <?php else: ?>
          <div style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;color:var(--green)">Free</div>
          <?php endif; ?>
        </div>

        <!-- Features -->
        <?php
        $features = [
          ['label'=>'Interests',        'val'=> $plan['interests_limit'] < 0 ? 'Unlimited' : $plan['interests_limit'].' max'],
          ['label'=>'Chat',             'val'=> $plan['chat_limit'] ? 'Unlimited' : 'Not included', 'inc'=>$plan['chat_limit']],
          ['label'=>'Contact View',     'val'=> $plan['contact_view'] ? 'Yes' : 'No',               'inc'=>$plan['contact_view']],
          ['label'=>'Advanced Search',  'val'=> $plan['advanced_search'] ? 'Yes' : 'No',            'inc'=>$plan['advanced_search']],
          ['label'=>'Profile Highlight','val'=> $plan['highlight'] ? 'Yes' : 'No',                  'inc'=>$plan['highlight']],
          ['label'=>'Photo Request',    'val'=> $plan['photo_request'] ? 'Yes' : 'No',              'inc'=>$plan['photo_request']],
        ];
        ?>
        <ul class="list-unstyled" style="font-size:.85rem">
          <?php foreach ($features as $f): ?>
          <li class="d-flex align-items-center gap-2 mb-2">
            <?php $inc = $f['inc'] ?? true; ?>
            <i class="bi bi-<?= $inc ? 'check-circle-fill' : 'x-circle-fill' ?>"
               style="color:<?= $inc ? 'var(--green)' : '#CBD5E1' ?>;flex-shrink:0"></i>
            <span style="color:<?= $inc ? 'var(--text)' : 'var(--muted)' ?>">
              <strong><?= $f['label'] ?></strong>: <?= $f['val'] ?>
            </span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- CTA -->
      <div class="p-4 pt-0">
        <?php if ($isCurrent): ?>
        <div class="text-center p-2 rounded-3" style="background:var(--green-pale);color:var(--green);font-weight:700;font-size:.88rem">
          <i class="bi bi-check-circle-fill me-1"></i> Current Plan
          <?php if (!empty($currentPlan['end_date'])): ?>
          <div style="font-size:.75rem;font-weight:400">Expires: <?= date('d M Y', strtotime($currentPlan['end_date'])) ?></div>
          <?php endif; ?>
        </div>
        <?php elseif ($plan['price'] > 0): ?>
        <a href="<?= APP_URL ?>/subscription/checkout/<?= $plan['id'] ?>"
           class="<?= $isPop ? 'btn-pink' : 'btn-outline-pink' ?> d-block text-center w-100"
           style="padding:.65rem">
          Get <?= htmlspecialchars($plan['name']) ?> <i class="bi bi-arrow-right ms-1"></i>
        </a>
        <?php else: ?>
        <a href="<?= APP_URL ?>/register" class="btn-outline-pink d-block text-center w-100" style="padding:.65rem">
          Start Free <i class="bi bi-arrow-right ms-1"></i>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- FAQ -->
<div class="mat-card p-4 mb-4">
  <h3 style="font-size:1.1rem;color:var(--dark);margin-bottom:1rem">Frequently Asked Questions</h3>
  <div class="row g-3">
    <div class="col-12 col-md-6">
      <div class="mb-3">
        <div style="font-weight:700;font-size:.88rem;color:var(--dark)">Can I upgrade anytime?</div>
        <div style="font-size:.83rem;color:var(--muted);margin-top:.2rem">Yes! You can upgrade your plan at any time. Your new plan starts immediately.</div>
      </div>
      <div class="mb-3">
        <div style="font-weight:700;font-size:.88rem;color:var(--dark)">Is payment secure?</div>
        <div style="font-size:.83rem;color:var(--muted);margin-top:.2rem">Absolutely. We use Razorpay — a PCI-DSS compliant payment gateway.</div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="mb-3">
        <div style="font-weight:700;font-size:.88rem;color:var(--dark)">Do you offer refunds?</div>
        <div style="font-size:.83rem;color:var(--muted);margin-top:.2rem">We offer a 7-day money-back guarantee if you're not satisfied.</div>
      </div>
      <div>
        <div style="font-weight:700;font-size:.88rem;color:var(--dark)">Can I cancel anytime?</div>
        <div style="font-size:.83rem;color:var(--muted);margin-top:.2rem">Your plan remains active until the end of the subscription period.</div>
      </div>
    </div>
  </div>
</div>

<!-- History link -->
<div class="text-center">
  <a href="<?= APP_URL ?>/subscription/history" style="color:var(--muted);font-size:.85rem">
    <i class="bi bi-clock-history me-1"></i> View Subscription History
  </a>
</div>
