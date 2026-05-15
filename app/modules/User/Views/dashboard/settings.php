<div class="page-header">
  <div class="section-tag">Account</div>
  <h1 style="font-size:1.35rem">Account Settings</h1>
</div>

<div class="row g-4">
  <!-- Left: General Settings -->
  <div class="col-12 col-lg-6">

    <!-- Basic Info -->
    <div class="mat-card p-4 mb-4">
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem">
        <i class="bi bi-person-gear me-2" style="color:var(--pink)"></i>Basic Information
      </h5>
      <form method="POST" action="<?= APP_URL ?>/account/settings">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required minlength="2">
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="background:var(--pink-pale);color:var(--muted)">
          <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
            <?php if ($user['email_verified']): ?>
            <i class="bi bi-check-circle-fill text-success me-1"></i>Verified
            <?php else: ?>
            <i class="bi bi-exclamation-circle-fill text-warning me-1"></i>Not verified
            <?php endif; ?>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile Number</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" readonly style="background:var(--pink-pale);color:var(--muted)">
          <div style="font-size:.75rem;color:var(--muted);margin-top:.3rem">
            <?php if ($user['mobile_verified']): ?>
            <i class="bi bi-check-circle-fill text-success me-1"></i>Verified
            <?php else: ?>
            <i class="bi bi-exclamation-circle-fill text-warning me-1"></i>Not verified
            <?php endif; ?>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Preferred Language</label>
          <select name="lang" class="form-select">
            <option value="en" <?= ($user['lang']??'en')==='en'?'selected':'' ?>>English</option>
            <option value="ta" <?= ($user['lang']??'')==='ta'?'selected':'' ?>>தமிழ் (Tamil)</option>
          </select>
        </div>
        <button type="submit" class="btn-green">
          <i class="bi bi-check-circle me-1"></i> Save Changes
        </button>
      </form>
    </div>

    <!-- Profile ID -->
    <div class="mat-card p-4 mb-4">
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:1rem">
        <i class="bi bi-fingerprint me-2" style="color:var(--green)"></i>Your Profile ID
      </h5>
      <div style="background:var(--green-pale);border-radius:12px;padding:.75rem 1.1rem;display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:1.15rem;font-weight:900;color:var(--green);font-family:'Playfair Display',serif"><?= htmlspecialchars($user['profile_id']) ?></div>
          <div style="font-size:.75rem;color:var(--muted)">Share this ID to help others find your profile</div>
        </div>
        <button onclick="navigator.clipboard.writeText('<?= $user['profile_id'] ?>');this.innerHTML='<i class=\'bi bi-check2\' style=\'color:var(--green)\'></i>'" class="btn border-0 p-2" title="Copy">
          <i class="bi bi-clipboard" style="color:var(--green)"></i>
        </button>
      </div>
    </div>

  </div>

  <!-- Right: Password + Danger -->
  <div class="col-12 col-lg-6">

    <!-- Change Password -->
    <div class="mat-card p-4 mb-4">
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:1.2rem">
        <i class="bi bi-shield-lock me-2" style="color:var(--pink)"></i>Change Password
      </h5>
      <form method="POST" action="<?= APP_URL ?>/account/password">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <div class="position-relative">
            <input type="password" name="current_password" id="cp" class="form-control pe-5" placeholder="Enter current password" required>
            <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 p-1" onclick="togglePwd('cp','ecp')" style="color:var(--muted)"><i class="bi bi-eye" id="ecp"></i></button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <div class="position-relative">
            <input type="password" name="new_password" id="np" class="form-control pe-5" placeholder="Min. 8 characters" required minlength="8">
            <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 p-1" onclick="togglePwd('np','enp')" style="color:var(--muted)"><i class="bi bi-eye" id="enp"></i></button>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" required minlength="8">
        </div>
        <button type="submit" class="btn-pink" style="padding:.6rem 1.5rem;width:auto">
          <i class="bi bi-lock me-1"></i> Update Password
        </button>
      </form>
    </div>

    <!-- Session Info -->
    <div class="mat-card p-4 mb-4">
      <h5 style="font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:1rem">
        <i class="bi bi-clock-history me-2" style="color:var(--muted)"></i>Session Info
      </h5>
      <div class="d-flex flex-column gap-2" style="font-size:.85rem">
        <div class="d-flex justify-content-between">
          <span style="color:var(--muted)">Last Login</span>
          <span style="font-weight:600"><?= $user['last_login'] ? date('d M Y, g:i a', strtotime($user['last_login'])) : 'N/A' ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span style="color:var(--muted)">Member Since</span>
          <span style="font-weight:600"><?= date('d M Y', strtotime($user['created_at'])) ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span style="color:var(--muted)">Account Status</span>
          <span class="badge-green"><?= ucfirst($user['status']) ?></span>
        </div>
      </div>
      <hr style="border-color:var(--border);margin:1rem 0">
      <a href="<?= APP_URL ?>/logout" class="btn-outline-pink d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.4rem 1rem">
        <i class="bi bi-box-arrow-right"></i> Logout from All Devices
      </a>
    </div>

    <!-- Danger Zone -->
    <div class="mat-card p-4" style="border:1.5px solid #FCA5A5">
      <h5 style="font-size:1rem;font-weight:700;color:#991B1B;margin-bottom:.5rem">
        <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
      </h5>
      <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">Deleting your account is permanent and cannot be undone. All your data including profile, photos, interests and chat history will be removed.</p>
      <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="bi bi-trash3 me-1"></i> Delete My Account
      </button>
    </div>

  </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" style="color:#991B1B"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p style="color:var(--text);font-size:.9rem">This action is <strong>permanent and irreversible</strong>. Please enter your password to confirm.</p>
        <form method="POST" action="<?= APP_URL ?>/account/delete" id="deleteForm">
          <input type="hidden" name="_csrf" value="<?= $csrf ?>">
          <div class="mt-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger flex-grow-1 rounded-pill">Yes, Delete Forever</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function togglePwd(id, ico) {
  const i = document.getElementById(id);
  const e = document.getElementById(ico);
  i.type = i.type === 'password' ? 'text' : 'password';
  e.className = i.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
