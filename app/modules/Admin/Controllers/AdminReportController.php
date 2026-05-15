<?php
// app/modules/Admin/Controllers/AdminReportController.php

class AdminReportController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $this->sanitize($_GET['status'] ?? 'open');
        $search = $this->sanitize($_GET['search'] ?? '');
        $where  = []; $binds = [];
        if ($status) { $where[] = "r.status=?"; $binds[] = $status; }
        if ($search) { $where[] = "(u1.name LIKE ? OR u2.name LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l]); }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT r.*, u1.name AS reporter_name, u1.profile_id AS reporter_pid,
                       u2.name AS reported_name, u2.profile_id AS reported_pid
                FROM reports r
                JOIN users u1 ON u1.id=r.reporter_id
                JOIN users u2 ON u2.id=r.reported_id
                $whereStr ORDER BY r.created_at DESC";
        $result  = Database::paginate($sql, $binds, $page, 20);
        $filters = compact('status', 'search');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/reports/index.php', array_merge($result, compact('filters', 'flash')), 'Admin/Views/layouts/main.php');
    }

    // Renamed from view() → detail() to avoid base Controller conflict
    public function detail(array $params = []): void {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $report = Database::fetchOne(
            "SELECT r.*, u1.name AS reporter_name, u2.name AS reported_name
             FROM reports r
             JOIN users u1 ON u1.id=r.reporter_id
             JOIN users u2 ON u2.id=r.reported_id
             WHERE r.id=?", [$id]
        );
        $this->view('Admin/Views/reports/view.php', compact('report'), 'Admin/Views/layouts/main.php');
    }

    public function action(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id     = (int)($_POST['id'] ?? 0);
        $action = $this->sanitize($_POST['action'] ?? '');
        $note   = $this->sanitize($_POST['admin_note'] ?? '');
        Database::execute("UPDATE reports SET status=?,admin_note=?,updated_at=NOW() WHERE id=?", [$action, $note, $id]);
        Session::flash('success', 'Report updated.');
        $this->redirect('admin/reports');
    }
}
