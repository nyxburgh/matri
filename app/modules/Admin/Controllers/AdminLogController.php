<?php
// app/modules/Admin/Controllers/AdminLogController.php

class AdminLogController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');
        $action = $this->sanitize($_GET['action'] ?? '');
        $from   = $this->sanitize($_GET['from'] ?? '');
        $to     = $this->sanitize($_GET['to'] ?? '');
        $where  = []; $binds = [];
        if ($search) { $where[] = "(u.name LIKE ? OR al.ip_address LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l]); }
        if ($action) { $where[] = "al.action LIKE ?"; $binds[] = "%$action%"; }
        if ($from)   { $where[] = "DATE(al.created_at)>=?"; $binds[] = $from; }
        if ($to)     { $where[] = "DATE(al.created_at)<=?"; $binds[] = $to; }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT al.*, u.name, u.profile_id
                FROM activity_logs al
                LEFT JOIN users u ON u.id=al.user_id
                $whereStr ORDER BY al.created_at DESC";
        $result  = Database::paginate($sql, $binds, $page, 30);
        $filters = compact('search', 'action', 'from', 'to');
        $this->view('Admin/Views/logs/index.php', array_merge($result, compact('filters')), 'Admin/Views/layouts/main.php');
    }
}
