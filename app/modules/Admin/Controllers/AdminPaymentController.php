<?php
// app/modules/Admin/Controllers/AdminPaymentController.php

class AdminPaymentController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = $this->sanitize($_GET['search'] ?? '');
        $status  = $this->sanitize($_GET['status'] ?? '');
        $gateway = $this->sanitize($_GET['gateway'] ?? '');
        $from    = $this->sanitize($_GET['from'] ?? '');
        $to      = $this->sanitize($_GET['to'] ?? '');
        $where   = []; $binds = [];
        if ($search)  { $where[] = "(u.name LIKE ? OR u.profile_id LIKE ? OR py.gateway_payment_id LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l, $l]); }
        if ($status)  { $where[] = "py.status=?";  $binds[] = $status; }
        if ($gateway) { $where[] = "py.gateway=?"; $binds[] = $gateway; }
        if ($from)    { $where[] = "DATE(py.created_at)>=?"; $binds[] = $from; }
        if ($to)      { $where[] = "DATE(py.created_at)<=?"; $binds[] = $to; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT py.*, u.name, u.profile_id, sp.name AS plan_name
                FROM payments py
                JOIN users u ON u.id=py.user_id
                JOIN subscription_plans sp ON sp.id=py.plan_id
                $whereStr ORDER BY py.created_at DESC";
        $result  = Database::paginate($sql, $binds, $page, 20);
        $filters = compact('search', 'status', 'gateway', 'from', 'to');
        $this->view('Admin/Views/payments/index.php', array_merge($result, compact('filters')), 'Admin/Views/layouts/main.php');
    }
}
