<?php
// app/core/Controller.php

abstract class Controller {

    protected function view(string $viewPath, array $data = [], string $layout = ''): void {
        extract($data);
        $csrf = Session::generateCsrf();

        if ($layout) {
            $layoutFile = BASE_PATH . '/app/modules/' . $layout;
            if (file_exists($layoutFile)) {
                $content = $this->renderPartial($viewPath, $data);
                include $layoutFile;
                return;
            }
        }
        include BASE_PATH . '/app/modules/' . $viewPath;
    }

    private function renderPartial(string $viewPath, array $data): string {
        extract($data);
        ob_start();
        include BASE_PATH . '/app/modules/' . $viewPath;
        return ob_get_clean();
    }

    protected function redirect(string $url): void {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ── Admin guard ──────────────────────────────────────────────────────────
    protected function requireAdmin(): void {
        if (!Session::has('admin_id') || Session::get('admin_role') !== 'admin') {
            Session::flash('error', 'Please login to continue.');
            $this->redirect('admin/login');
        }
    }

    // ── User guard ───────────────────────────────────────────────────────────
    protected function requireUser(): void {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login to continue.');
            $this->redirect('login');
        }
        // Block suspended users
        $status = Session::get('user_status', 'active');
        if ($status === 'blocked') {
            Session::destroy();
            Session::flash('error', 'Your account has been suspended. Please contact support.');
            $this->redirect('login');
        }
    }

    // ── Require profile to be created ────────────────────────────────────────
    protected function requireProfile(): void {
        $this->requireUser();
        if (!Session::get('user_profile_complete', false)) {
            Session::flash('info', 'Please complete your profile first.');
            $this->redirect('profile/create');
        }
    }

    protected function verifyCsrf(): void {
        $token = $_POST['_csrf'] ?? '';
        if (!Session::verifyCsrf($token)) {
            http_response_code(403);
            die('CSRF token mismatch. Please go back and try again.');
        }
    }

    protected function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    protected function input(string $key, string $default = ''): string {
        return $this->sanitize($_POST[$key] ?? $_GET[$key] ?? $default);
    }

    protected function paginationData(int $total, int $perPage, int $currentPage): array {
        return [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    // ── Log user activity ────────────────────────────────────────────────────
    protected function logActivity(string $action, string $module, ?int $refId = null, ?array $payload = null): void {
        $userId = Session::get('user_id') ?? Session::get('admin_id');
        Database::execute(
            "INSERT INTO activity_logs (user_id, action, module, ref_id, ip_address, user_agent, payload)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $userId,
                $action,
                $module,
                $refId,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $payload ? json_encode($payload) : null,
            ]
        );
    }

    // ── Get current user's active subscription ───────────────────────────────
    protected function getUserPlan(?int $userId = null): array {
        $uid = $userId ?? Session::get('user_id');
        $sub = Database::fetchOne(
            "SELECT us.*, sp.name AS plan_name, sp.code AS plan_code,
                    sp.interests_limit, sp.chat_limit, sp.contact_view,
                    sp.advanced_search, sp.highlight, sp.photo_request
             FROM user_subscriptions us
             JOIN subscription_plans sp ON sp.id = us.plan_id
             WHERE us.user_id = ? AND us.status = 'active' AND us.end_date >= CURDATE()
             ORDER BY us.end_date DESC LIMIT 1",
            [$uid]
        );
        if (!$sub) {
            // Return free plan defaults
            $free = Database::fetchOne("SELECT * FROM subscription_plans WHERE code = 'FREE' LIMIT 1");
            return $free ?: ['plan_code' => 'FREE', 'interests_limit' => 5, 'chat_limit' => 0,
                             'contact_view' => 0, 'advanced_search' => 0, 'highlight' => 0, 'plan_name' => 'Free'];
        }
        return $sub;
    }
}
