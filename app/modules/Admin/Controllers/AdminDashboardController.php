<?php
// app/modules/Admin/Controllers/AdminDashboardController.php

class AdminDashboardController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();

        // Stats cards
        $stats = [
            'total_users'     => Database::fetchOne("SELECT COUNT(*) c FROM users WHERE role='user'")['c'] ?? 0,
            'active_users'    => Database::fetchOne("SELECT COUNT(*) c FROM users WHERE role='user' AND status='active'")['c'] ?? 0,
            'pending_profiles'=> Database::fetchOne("SELECT COUNT(*) c FROM profiles WHERE admin_approved='pending'")['c'] ?? 0,
            'pending_photos'  => Database::fetchOne("SELECT COUNT(*) c FROM photos WHERE is_approved='pending'")['c'] ?? 0,
            'total_revenue'   => Database::fetchOne("SELECT COALESCE(SUM(amount),0) c FROM payments WHERE status='success'")['c'] ?? 0,
            'this_month_rev'  => Database::fetchOne("SELECT COALESCE(SUM(amount),0) c FROM payments WHERE status='success' AND MONTH(paid_at)=MONTH(NOW()) AND YEAR(paid_at)=YEAR(NOW())")['c'] ?? 0,
            'open_reports'    => Database::fetchOne("SELECT COUNT(*) c FROM reports WHERE status='open'")['c'] ?? 0,
            'active_subs'     => Database::fetchOne("SELECT COUNT(*) c FROM user_subscriptions WHERE status='active'")['c'] ?? 0,
        ];

        // Recent registrations (last 7 days chart data)
        $regChart = Database::fetchAll(
            "SELECT DATE(created_at) AS day, COUNT(*) AS cnt
             FROM users WHERE role='user' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at) ORDER BY day ASC"
        );

        // Recently joined users
        $recentUsers = Database::fetchAll(
            "SELECT u.id, u.profile_id, u.name, u.gender, u.status, u.created_at,
                    p.city, p.religion
             FROM users u
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE u.role='user'
             ORDER BY u.created_at DESC LIMIT 8"
        );

        // Pending profile approvals
        $pendingProfiles = Database::fetchAll(
            "SELECT u.id, u.profile_id, u.name, u.gender, p.city, p.religion, p.admin_approved, u.created_at
             FROM profiles p
             JOIN users u ON u.id = p.user_id
             WHERE p.admin_approved = 'pending'
             ORDER BY u.created_at DESC LIMIT 6"
        );

        // Revenue by plan
        $revByPlan = Database::fetchAll(
            "SELECT sp.name, SUM(py.amount) AS total
             FROM payments py JOIN subscription_plans sp ON sp.id = py.plan_id
             WHERE py.status = 'success'
             GROUP BY sp.id ORDER BY total DESC"
        );

        $this->view(
            'Admin/Views/dashboard/index.php',
            compact('stats','regChart','recentUsers','pendingProfiles','revByPlan'),
            'Admin/Views/layouts/main.php'
        );
    }
}
