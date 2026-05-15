<?php
// app/modules/User/Controllers/UserDashboardController.php

class UserDashboardController extends Controller {

    public function home(array $params = []): void {
        // If logged in → dashboard
        if (Session::has('user_id')) {
            $this->redirect('dashboard');
        }
        // Public home page (serve the landing page view)
        $this->view('User/Views/home/index.php', []);
    }

    public function index(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $gender = Session::get('user_gender');

        // Stats
        $stats = [
            'interests_sent'     => Database::fetchOne(
                "SELECT COUNT(*) c FROM interests WHERE sender_id = ?", [$userId])['c'] ?? 0,
            'interests_received' => Database::fetchOne(
                "SELECT COUNT(*) c FROM interests WHERE receiver_id = ? AND status = 'pending'", [$userId])['c'] ?? 0,
            'matches'            => Database::fetchOne(
                "SELECT COUNT(*) c FROM interests WHERE (sender_id = ? OR receiver_id = ?) AND status = 'accepted'",
                [$userId, $userId])['c'] ?? 0,
            'profile_views'      => Database::fetchOne(
                "SELECT COUNT(*) c FROM profile_views WHERE viewed_id = ?", [$userId])['c'] ?? 0,
            'unread_messages'    => Database::fetchOne(
                "SELECT COUNT(*) c FROM chat_messages WHERE receiver_id = ? AND is_read = 0", [$userId])['c'] ?? 0,
            'unread_notifs'      => Database::fetchOne(
                "SELECT COUNT(*) c FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])['c'] ?? 0,
        ];

        // Current subscription
        $subscription = $this->getUserPlan($userId);

        // Profile completeness
        $profile = Database::fetchOne(
            "SELECT p.*, u.email, u.mobile, u.email_verified, u.mobile_verified, u.profile_complete
             FROM users u LEFT JOIN profiles p ON p.user_id = u.id
             WHERE u.id = ? LIMIT 1",
            [$userId]
        );

        $completeness = $this->calcCompleteness($profile ?? []);

        // Recommended matches (opposite gender, same religion, active)
        $oppGender = $gender === 'male' ? 'female' : 'male';
        $recommended = Database::fetchAll(
            "SELECT u.id, u.profile_id, u.name, u.gender, u.last_active,
                    p.age, p.city, p.state, p.religion, p.caste, p.education, p.occupation,
                    p.marital_status, p.admin_approved,
                    (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                     AND ph.is_approved='approved' LIMIT 1) AS photo,
                    (SELECT i.status FROM interests i WHERE (i.sender_id=? AND i.receiver_id=u.id)
                     OR (i.sender_id=u.id AND i.receiver_id=?) LIMIT 1) AS interest_status
             FROM users u
             JOIN profiles p ON p.user_id = u.id
             WHERE u.gender = ? AND u.status = 'active' AND u.id != ?
               AND p.admin_approved = 'approved'
             ORDER BY u.last_active DESC, p.is_highlighted DESC
             LIMIT 8",
            [$userId, $userId, $oppGender, $userId]
        );

        // Recent interests received
        $recentInterests = Database::fetchAll(
            "SELECT i.*, u.name, u.profile_id, u.gender,
                    p.age, p.city, p.religion,
                    (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                     AND ph.is_approved='approved' LIMIT 1) AS photo
             FROM interests i
             JOIN users u ON u.id = i.sender_id
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE i.receiver_id = ? AND i.status = 'pending'
             ORDER BY i.sent_at DESC LIMIT 5",
            [$userId]
        );

        // Recent notifications
        $notifications = Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
            [$userId]
        );

        // Who viewed my profile recently
        $recentViewers = Database::fetchAll(
            "SELECT pv.viewed_at, u.id, u.name, u.profile_id, u.gender,
                    p.age, p.city,
                    (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                     AND ph.is_approved='approved' LIMIT 1) AS photo
             FROM profile_views pv
             JOIN users u ON u.id = pv.viewer_id
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE pv.viewed_id = ? AND pv.viewer_id != ?
             GROUP BY pv.viewer_id ORDER BY pv.viewed_at DESC LIMIT 5",
            [$userId, $userId]
        );

        $flash   = Session::getFlash('success');
        $info    = Session::getFlash('info');
        $pageTitle = 'Dashboard';

        $this->view(
            'User/Views/dashboard/index.php',
            compact('stats','subscription','profile','completeness','recommended',
                    'recentInterests','notifications','recentViewers','flash','info','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function settings(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $user   = Database::fetchOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);
        $error  = Session::getFlash('error');
        $flash  = Session::getFlash('success');
        $pageTitle = 'Account Settings';

        $this->view(
            'User/Views/dashboard/settings.php',
            compact('user', 'error', 'flash', 'pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function saveSettings(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $name = $this->input('name');
        $lang = $this->input('lang', 'en');

        if (strlen($name) < 2) {
            Session::flash('error', 'Name is too short.');
            $this->redirect('account/settings');
        }

        Database::execute(
            "UPDATE users SET name = ?, lang = ?, updated_at = NOW() WHERE id = ?",
            [$name, $lang, $userId]
        );
        Session::set('user_name', $name);
        Session::set('user_lang', $lang);
        $this->logActivity('update_settings', 'User');
        Session::flash('success', 'Settings updated successfully.');
        $this->redirect('account/settings');
    }

    public function changePassword(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId  = Session::get('user_id');
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = Database::fetchOne("SELECT password_hash FROM users WHERE id = ?", [$userId]);

        if (!$user || !password_verify($current, $user['password_hash'] ?? '')) {
            Session::flash('error', 'Current password is incorrect.');
            $this->redirect('account/settings');
        }
        if (strlen($new) < 8) {
            Session::flash('error', 'New password must be at least 8 characters.');
            $this->redirect('account/settings');
        }
        if ($new !== $confirm) {
            Session::flash('error', 'New passwords do not match.');
            $this->redirect('account/settings');
        }

        Database::execute(
            "UPDATE users SET password_hash = ? WHERE id = ?",
            [password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]), $userId]
        );
        $this->logActivity('change_password', 'User');
        Session::flash('success', 'Password changed successfully.');
        $this->redirect('account/settings');
    }

    public function deleteAccount(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId   = Session::get('user_id');
        $password = $_POST['password'] ?? '';
        $user     = Database::fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
            Session::flash('error', 'Password is incorrect. Account not deleted.');
            $this->redirect('account/settings');
        }

        Database::execute("UPDATE users SET status = 'deleted', updated_at = NOW() WHERE id = ?", [$userId]);
        $this->logActivity('delete_account', 'User');
        Session::destroy();
        $this->redirect('login');
    }

    public function profileViews(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $sql = "SELECT pv.viewed_at, u.id, u.name, u.profile_id, u.gender,
                       p.age, p.city, p.religion,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo
                FROM profile_views pv
                JOIN users u ON u.id = pv.viewer_id
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE pv.viewed_id = ? AND pv.viewer_id != ?
                GROUP BY pv.viewer_id ORDER BY pv.viewed_at DESC";

        $result    = Database::paginate($sql, [$userId, $userId], $page, 20);
        $pageTitle = 'Who Viewed My Profile';

        $this->view(
            'User/Views/dashboard/profile_views.php',
            array_merge($result, compact('pageTitle')),
            'User/Views/layouts/main.php'
        );
    }

    // ── Profile completeness calculator ─────────────────────────────────────
    private function calcCompleteness(array $p): int {
        $fields = ['dob', 'height_cm', 'religion', 'caste', 'education',
                   'occupation', 'annual_income', 'about_me', 'city', 'state'];
        $filled = 0;
        foreach ($fields as $f) {
            if (!empty($p[$f])) $filled++;
        }
        $base = (int)(($filled / count($fields)) * 80);
        // +10 for verified email, +10 for photo
        $extra = 0;
        if (!empty($p['email_verified'])) $extra += 10;
        if (!empty($p['photo'])) $extra += 10;
        return min(100, $base + $extra);
    }

    public function matches(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $gender = Session::get('user_gender');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $oppGender = $gender === 'male' ? 'female' : 'male';

        $sql = "SELECT u.id, u.profile_id, u.name, u.gender, u.last_active,
                       p.age, p.city, p.state, p.religion, p.caste, p.education,
                       p.occupation, p.marital_status, p.admin_approved,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo
                FROM users u
                JOIN profiles p ON p.user_id = u.id
                WHERE u.gender = ? AND u.status = 'active' AND u.id != ?
                  AND p.admin_approved = 'approved'
                ORDER BY u.last_active DESC, p.is_highlighted DESC";

        $result    = Database::paginate($sql, [$oppGender, $userId], $page, 12);
        $pageTitle = 'Daily Matches';

        $this->view(
            'User/Views/dashboard/matches.php',
            array_merge($result, compact('pageTitle')),
            'User/Views/layouts/main.php'
        );
    }
}
