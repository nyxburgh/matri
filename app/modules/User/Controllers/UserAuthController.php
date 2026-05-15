<?php
// app/modules/User/Controllers/UserAuthController.php

class UserAuthController extends Controller {

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function generateOtp(): string {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique MAT-XXXXXX profile ID
     */
    private function generateProfileId(): string {
        do {
            $id = 'MAT-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            $exists = Database::fetchOne("SELECT id FROM users WHERE profile_id = ? LIMIT 1", [$id]);
        } while ($exists);
        return $id;
    }

    /**
     * Check OTP rate limit (max 3 per hour per target)
     */
    private function isOtpRateLimited(string $target): bool {
        $count = Database::fetchOne(
            "SELECT COUNT(*) AS c FROM otp_logs
             WHERE target = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
             AND is_used = 0",
            [$target]
        )['c'] ?? 0;
        return (int)$count >= (int)(Database::fetchOne("SELECT value FROM settings WHERE `key`='otp_max_attempts'")['value'] ?? 3);
    }

    /**
     * Store OTP in DB and send (stub — hook your SMS/email gateway here)
     */
    private function sendOtp(string $target, string $type, string $purpose): string {
        $otp     = $this->generateOtp();
        $expiry  = Database::fetchOne("SELECT value FROM settings WHERE `key`='otp_expiry_minutes'")['value'] ?? 10;

        // Invalidate previous unused OTPs for same target+purpose
        Database::execute(
            "UPDATE otp_logs SET is_used = 1 WHERE target = ? AND purpose = ? AND is_used = 0",
            [$target, $purpose]
        );

        Database::execute(
            "INSERT INTO otp_logs (target, type, purpose, otp_code, expires_at) VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))",
            [$target, $type, $purpose, $otp, $expiry]
        );

        // TODO: Integrate SMS/Email gateway
        // Example: EmailService::send($target, 'Your OTP', "Your OTP is: $otp");
        // For development, log it:
        Logger::info("OTP for $target [$purpose]: $otp");

        return $otp;
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function loginForm(array $params = []): void {
        if (Session::has('user_id')) {
            $this->redirect('dashboard');
        }
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $info    = Session::getFlash('info');
        $this->view('User/Views/auth/login.php', compact('error', 'success', 'info'));
    }

    public function loginPost(array $params = []): void {
        $this->verifyCsrf();

        $loginType = $this->input('login_type', 'password'); // password | otp
        $identifier = trim($_POST['identifier'] ?? '');
        $identifier = filter_var($identifier, FILTER_SANITIZE_EMAIL) ?: $identifier;

        if (empty($identifier)) {
            Session::flash('error', 'Email or mobile number is required.');
            $this->redirect('login');
        }

        // ── OTP Login ──
        if ($loginType === 'otp') {
            // Find user
            $user = Database::fetchOne(
                "SELECT * FROM users WHERE (email = ? OR mobile = ?) AND role = 'user' AND status = 'active' LIMIT 1",
                [$identifier, $identifier]
            );
            if (!$user) {
                Session::flash('error', 'No active account found with this email/mobile.');
                $this->redirect('login');
            }
            $target = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : $user['mobile'];
            $type   = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

            if ($this->isOtpRateLimited($target)) {
                Session::flash('error', 'Too many OTP requests. Please wait before requesting again.');
                $this->redirect('login');
            }

            $this->sendOtp($target, $type, 'login');
            Session::set('otp_target',  $target);
            Session::set('otp_type',    $type);
            Session::set('otp_purpose', 'login');
            Session::set('otp_user_id', $user['id']);
            $this->redirect('verify-otp');
        }

        // ── Password Login ──
        $password = $_POST['password'] ?? '';
        if (empty($password)) {
            Session::flash('error', 'Password is required.');
            $this->redirect('login');
        }

        $user = Database::fetchOne(
            "SELECT * FROM users WHERE (email = ? OR mobile = ?) AND role = 'user' LIMIT 1",
            [$identifier, $identifier]
        );

        if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
            $this->logActivity('login_failed', 'User');
            Session::flash('error', 'Invalid credentials. Please try again.');
            $this->redirect('login');
        }

        if ($user['status'] === 'blocked') {
            Session::flash('error', 'Your account has been suspended. Contact support.');
            $this->redirect('login');
        }
        if ($user['status'] === 'deleted') {
            Session::flash('error', 'Account not found.');
            $this->redirect('login');
        }

        $this->startUserSession($user);
        $this->redirect('dashboard');
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function registerForm(array $params = []): void {
        if (Session::has('user_id')) {
            $this->redirect('dashboard');
        }
        $error = Session::getFlash('error');
        $old   = Session::getFlash('old') ? json_decode(Session::getFlash('old'), true) : [];
        $this->view('User/Views/auth/register.php', compact('error', 'old'));
    }

    public function registerPost(array $params = []): void {
        $this->verifyCsrf();

        $name     = $this->input('name');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $mobile   = preg_replace('/\D/', '', $_POST['mobile'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $gender   = $this->input('gender');
        $for      = $this->input('account_for', 'self');
        $lang     = $this->input('lang', 'en');

        // Validation
        $errors = [];
        if (strlen($name) < 2)   $errors[] = 'Full name is required (min 2 characters).';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
        if (strlen($mobile) < 10) $errors[] = 'Please enter a valid 10-digit mobile number.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';
        if (!in_array($gender, ['male', 'female', 'other'])) $errors[] = 'Please select gender.';

        if ($errors) {
            Session::flash('error', implode(' ', $errors));
            Session::flash('old', json_encode(compact('name', 'email', 'mobile', 'gender', 'for')));
            $this->redirect('register');
        }

        // Check duplicates
        $dup = Database::fetchOne(
            "SELECT id FROM users WHERE email = ? OR mobile = ? LIMIT 1",
            [$email, $mobile]
        );
        if ($dup) {
            Session::flash('error', 'An account with this email or mobile already exists.');
            $this->redirect('register');
        }

        // Store registration data in session for post-OTP creation
        Session::set('reg_data', json_encode([
            'name'        => $name,
            'email'       => $email,
            'mobile'      => $mobile,
            'password'    => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'gender'      => $gender,
            'account_for' => $for,
            'lang'        => $lang,
        ]));

        // Send OTP to email
        $this->sendOtp($email, 'email', 'register');
        Session::set('otp_target',  $email);
        Session::set('otp_type',    'email');
        Session::set('otp_purpose', 'register');
        $this->redirect('verify-otp');
    }

    // ── OTP Verify ────────────────────────────────────────────────────────────

    public function otpForm(array $params = []): void {
        if (!Session::has('otp_target')) {
            $this->redirect('login');
        }
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $target  = Session::get('otp_target');
        $purpose = Session::get('otp_purpose');
        $this->view('User/Views/auth/otp.php', compact('error', 'success', 'target', 'purpose'));
    }

    public function otpVerify(array $params = []): void {
        $this->verifyCsrf();

        $code    = $this->input('otp_code');
        $target  = Session::get('otp_target');
        $purpose = Session::get('otp_purpose');

        if (!$target || !$code) {
            Session::flash('error', 'Invalid request. Please try again.');
            $this->redirect('login');
        }

        // Fetch valid OTP
        $otp = Database::fetchOne(
            "SELECT * FROM otp_logs
             WHERE target = ? AND purpose = ? AND otp_code = ? AND is_used = 0
               AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1",
            [$target, $purpose, $code]
        );

        if (!$otp) {
            // Increment attempt counter
            Database::execute(
                "UPDATE otp_logs SET attempts = attempts + 1
                 WHERE target = ? AND purpose = ? AND is_used = 0 AND expires_at > NOW()",
                [$target, $purpose]
            );
            Session::flash('error', 'Invalid or expired OTP. Please try again.');
            $this->redirect('verify-otp');
        }

        // Mark OTP used
        Database::execute("UPDATE otp_logs SET is_used = 1 WHERE id = ?", [$otp['id']]);

        // ── Register flow ──
        if ($purpose === 'register') {
            $regData = json_decode(Session::get('reg_data', '{}'), true);
            if (empty($regData)) {
                Session::flash('error', 'Session expired. Please register again.');
                $this->redirect('register');
            }

            $profileId = $this->generateProfileId();
            $userId = Database::insert(
                "INSERT INTO users (profile_id, name, email, mobile, password_hash, gender, account_for, status, email_verified, lang)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 1, ?)",
                [$profileId, $regData['name'], $regData['email'], $regData['mobile'],
                 $regData['password'], $regData['gender'], $regData['account_for'], $regData['lang']]
            );

            // Assign free plan
            Database::execute(
                "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status)
                 SELECT ?, id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 36500 DAY), 'active'
                 FROM subscription_plans WHERE code = 'FREE' LIMIT 1",
                [(int)$userId]
            );

            Session::delete('reg_data');
            Session::delete('otp_target');
            Session::delete('otp_purpose');

            $user = Database::fetchOne("SELECT * FROM users WHERE id = ? LIMIT 1", [(int)$userId]);
            $this->startUserSession($user);
            $this->logActivity('register', 'User');
            Session::flash('success', 'Welcome to Namma Matrimony! Complete your profile to get started.');
            $this->redirect('profile/create');
        }

        // ── Login flow ──
        if ($purpose === 'login') {
            $userId = Session::get('otp_user_id');
            $user = Database::fetchOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);
            if (!$user) {
                Session::flash('error', 'Session expired. Please login again.');
                $this->redirect('login');
            }
            Session::delete('otp_target');
            Session::delete('otp_purpose');
            Session::delete('otp_user_id');
            $this->startUserSession($user);
            $this->redirect('dashboard');
        }

        $this->redirect('login');
    }

    public function resendOtp(array $params = []): void {
        $this->verifyCsrf();

        $target  = Session::get('otp_target');
        $type    = Session::get('otp_type', 'email');
        $purpose = Session::get('otp_purpose');

        if (!$target || !$purpose) {
            $this->json(['success' => false, 'message' => 'Session expired.'], 400);
        }

        if ($this->isOtpRateLimited($target)) {
            $this->json(['success' => false, 'message' => 'Too many OTP requests. Please wait.'], 429);
        }

        $this->sendOtp($target, $type, $purpose);
        $this->json(['success' => true, 'message' => 'OTP resent successfully.']);
    }

    // ── Forgot Password ───────────────────────────────────────────────────────

    public function forgotForm(array $params = []): void {
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $this->view('User/Views/auth/forgot.php', compact('error', 'success'));
    }

    public function forgotPost(array $params = []): void {
        $this->verifyCsrf();
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        $user = Database::fetchOne(
            "SELECT * FROM users WHERE email = ? AND role = 'user' AND status = 'active' LIMIT 1",
            [$email]
        );

        // Always show success (security: don't reveal if email exists)
        if ($user) {
            if (!$this->isOtpRateLimited($email)) {
                $this->sendOtp($email, 'email', 'reset');
                Session::set('otp_target',  $email);
                Session::set('otp_type',    'email');
                Session::set('otp_purpose', 'reset');
                Session::set('reset_user_id', $user['id']);
            }
        }

        Session::flash('success', 'If an account exists, an OTP has been sent to your email.');
        $this->redirect('verify-otp');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(array $params = []): void {
        $userId = Session::get('user_id');
        if ($userId) {
            $this->logActivity('logout', 'User');
            Database::execute("UPDATE users SET last_active = NOW() WHERE id = ?", [$userId]);
        }
        Session::destroy();
        $this->redirect('login');
    }

    // ── Session Starter ───────────────────────────────────────────────────────

    private function startUserSession(array $user): void {
        Session::regenerate();
        Session::set('user_id',              $user['id']);
        Session::set('user_name',            $user['name']);
        Session::set('user_profile_id',      $user['profile_id']);
        Session::set('user_gender',          $user['gender']);
        Session::set('user_role',            $user['role']);
        Session::set('user_status',          $user['status']);
        Session::set('user_lang',            $user['lang']);
        Session::set('user_profile_complete',(bool)$user['profile_complete']);

        Database::execute("UPDATE users SET last_login = NOW(), last_active = NOW() WHERE id = ?", [$user['id']]);
        $this->logActivity('login', 'User');
    }
}
