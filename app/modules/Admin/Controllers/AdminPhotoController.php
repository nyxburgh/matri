<?php
// app/modules/Admin/Controllers/AdminPhotoController.php

class AdminPhotoController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $this->sanitize($_GET['status'] ?? 'pending');
        $search = $this->sanitize($_GET['search'] ?? '');
        $where  = []; $binds = [];
        if ($status) { $where[] = "ph.is_approved=?"; $binds[] = $status; }
        if ($search) { $where[] = "(u.name LIKE ? OR u.profile_id LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l]); }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT ph.*, u.name, u.profile_id, u.gender
                FROM photos ph JOIN users u ON u.id=ph.user_id
                $whereStr ORDER BY ph.created_at DESC";
        $result  = Database::paginate($sql, $binds, $page, 24);
        $filters = compact('status', 'search');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/photos/index.php', array_merge($result, compact('filters', 'flash')), 'Admin/Views/layouts/main.php');
    }

    public function approve(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE photos SET is_approved='approved' WHERE id=?", [$id]);
        Session::flash('success', 'Photo approved.');
        $this->redirect('admin/photos');
    }

    public function reject(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE photos SET is_approved='rejected' WHERE id=?", [$id]);
        Session::flash('success', 'Photo rejected.');
        $this->redirect('admin/photos');
    }
}
