<?php
// app/modules/Admin/Controllers/AdminAuthController.php

class AdminAuthController extends Controller {

    public function loginForm(array $params = []): void {
        if (Session::has('admin_id')) {
            $this->redirect('admin/dashboard');
        }
        $error = Session::getFlash('error');
        $this->view('Admin/Views/auth/login.php', compact('error'));
    }

    public function loginPost(array $params = []): void {
        $this->verifyCsrf();

        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            $this->redirect('admin/login');
        }

        $user = Database::fetchOne(
            "SELECT * FROM users WHERE email = ? AND role IN ('admin','moderator') AND status = 'active' LIMIT 1",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Log failed attempt
            Database::execute(
                "INSERT INTO activity_logs (user_id, action, module, ip_address, user_agent) VALUES (?,?,?,?,?)",
                [null, 'admin_login_failed', 'Admin', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
            );
            Session::flash('error', 'Invalid credentials. Please try again.');
            $this->redirect('admin/login');
        }

        // Regenerate session ID on login
        Session::regenerate();
        Session::set('admin_id',   $user['id']);
        Session::set('admin_name', $user['name']);
        Session::set('admin_role', $user['role']);
        Session::set('admin_email',$user['email']);

        // Update last login
        Database::execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

        // Log success
        Database::execute(
            "INSERT INTO activity_logs (user_id, action, module, ip_address, user_agent) VALUES (?,?,?,?,?)",
            [$user['id'], 'admin_login', 'Admin', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
        );

        $this->redirect('admin/dashboard');
    }

    public function logout(array $params = []): void {
        $adminId = Session::get('admin_id');
        if ($adminId) {
            Database::execute(
                "INSERT INTO activity_logs (user_id, action, module, ip_address) VALUES (?,?,?,?)",
                [$adminId, 'admin_logout', 'Admin', $_SERVER['REMOTE_ADDR'] ?? '']
            );
        }
        Session::destroy();
        $this->redirect('admin/login');
    }
}
