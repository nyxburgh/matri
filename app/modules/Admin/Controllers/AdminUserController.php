<?php
// app/modules/Admin/Controllers/AdminUserController.php

class AdminUserController extends Controller {

    private int $perPage = 20;

    public function index(array $params = []): void {
        $this->requireAdmin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');
        $gender = $this->sanitize($_GET['gender'] ?? '');
        $status = $this->sanitize($_GET['status'] ?? '');
        $from   = $this->sanitize($_GET['from'] ?? '');
        $to     = $this->sanitize($_GET['to'] ?? '');

        $where  = ["u.role = 'user'"];
        $binds  = [];

        if ($search) {
            $where[] = "(u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR u.profile_id LIKE ?)";
            $like    = "%$search%";
            $binds   = array_merge($binds, [$like, $like, $like, $like]);
        }
        if ($gender) { $where[] = "u.gender = ?";  $binds[] = $gender; }
        if ($status)  { $where[] = "u.status = ?";  $binds[] = $status; }
        if ($from)    { $where[] = "DATE(u.created_at) >= ?"; $binds[] = $from; }
        if ($to)      { $where[] = "DATE(u.created_at) <= ?"; $binds[] = $to; }

        $whereStr = implode(' AND ', $where);

        $sql = "SELECT u.id, u.profile_id, u.name, u.email, u.mobile,
                       u.gender, u.status, u.email_verified, u.mobile_verified,
                       u.last_login, u.created_at,
                       p.city, p.religion, p.admin_approved,
                       (SELECT COUNT(*) FROM photos ph WHERE ph.user_id = u.id) AS photo_count
                FROM users u
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE $whereStr
                ORDER BY u.created_at DESC";

        $result = Database::paginate($sql, $binds, $page, $this->perPage);

        $filters = compact('search', 'gender', 'status', 'from', 'to');
        $flash   = Session::getFlash('success');

        $this->view(
            'Admin/Views/users/index.php',
            array_merge($result, compact('filters', 'flash')),
            'Admin/Views/layouts/main.php'
        );
    }

    public function detail(array $params = []): void {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);

        $user = Database::fetchOne(
            "SELECT u.*, p.*, fd.*
             FROM users u
             LEFT JOIN profiles p ON p.user_id = u.id
             LEFT JOIN family_details fd ON fd.user_id = u.id
             WHERE u.id = ? AND u.role = 'user'", [$id]
        );
        if (!$user) { http_response_code(404); die('User not found.'); }

        $photos   = Database::fetchAll("SELECT * FROM photos WHERE user_id = ? ORDER BY sort_order", [$id]);
        $interests_sent     = Database::fetchAll("SELECT i.*, u.name AS rname, u.profile_id AS rpid FROM interests i JOIN users u ON u.id=i.receiver_id WHERE i.sender_id=? ORDER BY i.sent_at DESC LIMIT 10", [$id]);
        $interests_received = Database::fetchAll("SELECT i.*, u.name AS sname, u.profile_id AS spid FROM interests i JOIN users u ON u.id=i.sender_id WHERE i.receiver_id=? ORDER BY i.sent_at DESC LIMIT 10", [$id]);
        $subscription       = Database::fetchOne("SELECT us.*, sp.name AS plan_name FROM user_subscriptions us JOIN subscription_plans sp ON sp.id=us.plan_id WHERE us.user_id=? AND us.status='active' ORDER BY us.end_date DESC LIMIT 1", [$id]);
        $activity           = Database::fetchAll("SELECT * FROM activity_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 15", [$id]);

        $this->view(
            'Admin/Views/users/view.php',
            compact('user','photos','interests_sent','interests_received','subscription','activity'),
            'Admin/Views/layouts/main.php'
        );
    }

    public function block(array $params = []): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $id     = (int)($_POST['id'] ?? 0);
        $reason = $this->sanitize($_POST['reason'] ?? 'Blocked by admin');

        Database::execute("UPDATE users SET status='blocked' WHERE id=? AND role='user'", [$id]);
        Database::execute(
            "INSERT INTO activity_logs (user_id,action,module,ip_address,payload) VALUES (?,?,?,?,?)",
            [Session::get('admin_id'), 'block_user', 'Admin', $_SERVER['REMOTE_ADDR']??'', json_encode(['target'=>$id,'reason'=>$reason])]
        );
        Session::flash('success', 'User has been blocked.');
        $this->redirect('admin/users');
    }

    public function unblock(array $params = []): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE users SET status='active' WHERE id=? AND role='user'", [$id]);
        Database::execute(
            "INSERT INTO activity_logs (user_id,action,module,ip_address) VALUES (?,?,?,?)",
            [Session::get('admin_id'), 'unblock_user', 'Admin', $_SERVER['REMOTE_ADDR']??'']
        );
        Session::flash('success', 'User has been unblocked.');
        $this->redirect('admin/users');
    }

    public function delete(array $params = []): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE users SET status='deleted' WHERE id=? AND role='user'", [$id]);
        Database::execute(
            "INSERT INTO activity_logs (user_id,action,module,ip_address) VALUES (?,?,?,?)",
            [Session::get('admin_id'), 'delete_user', 'Admin', $_SERVER['REMOTE_ADDR']??'']
        );
        Session::flash('success', 'User has been deleted.');
        $this->redirect('admin/users');
    }
}
