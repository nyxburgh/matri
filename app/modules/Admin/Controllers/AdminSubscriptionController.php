<?php
// app/modules/Admin/Controllers/AdminSubscriptionController.php

class AdminSubscriptionController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');
        $status = $this->sanitize($_GET['status'] ?? '');
        $plan   = $this->sanitize($_GET['plan'] ?? '');
        $where  = []; $binds = [];
        if ($search) { $where[] = "(u.name LIKE ? OR u.profile_id LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l]); }
        if ($status) { $where[] = "us.status=?"; $binds[] = $status; }
        if ($plan)   { $where[] = "us.plan_id=?"; $binds[] = $plan; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT us.*, u.name, u.profile_id, u.gender, sp.name AS plan_name, sp.price
                FROM user_subscriptions us
                JOIN users u ON u.id=us.user_id
                JOIN subscription_plans sp ON sp.id=us.plan_id
                $whereStr ORDER BY us.created_at DESC";
        $result  = Database::paginate($sql, $binds, $page, 20);
        $plans   = Database::fetchAll("SELECT * FROM subscription_plans ORDER BY sort_order");
        $filters = compact('search', 'status', 'plan');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/subscriptions/index.php', array_merge($result, compact('filters', 'plans', 'flash')), 'Admin/Views/layouts/main.php');
    }

    public function plans(array $params = []): void {
        $this->requireAdmin();
        $plans = Database::fetchAll("SELECT * FROM subscription_plans ORDER BY sort_order");
        $flash = Session::getFlash('success');
        $this->view('Admin/Views/subscriptions/plans.php', compact('plans', 'flash'), 'Admin/Views/layouts/main.php');
    }

    public function savePlan(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id     = (int)($_POST['id'] ?? 0);
        $fields = ['name','code','duration_days','price','interests_limit','chat_limit',
                   'contact_view','advanced_search','highlight','photo_request','is_active','sort_order'];
        $data = [];
        foreach ($fields as $f) $data[$f] = $this->sanitize($_POST[$f] ?? '0');
        if ($id) {
            $sets = implode(',', array_map(fn($f) => "$f=?", array_keys($data)));
            Database::execute("UPDATE subscription_plans SET $sets WHERE id=?", array_merge(array_values($data), [$id]));
        } else {
            $cols = implode(',', array_keys($data));
            $phs  = implode(',', array_fill(0, count($data), '?'));
            Database::execute("INSERT INTO subscription_plans ($cols) VALUES ($phs)", array_values($data));
        }
        Session::flash('success', 'Plan saved.');
        $this->redirect('admin/plans');
    }
}
