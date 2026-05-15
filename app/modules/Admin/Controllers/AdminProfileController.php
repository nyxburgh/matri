<?php
// app/modules/Admin/Controllers/AdminProfileController.php

class AdminProfileController extends Controller {
    private int $perPage = 20;

    public function index(array $params = []): void {
        $this->requireAdmin();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $search   = $this->sanitize($_GET['search'] ?? '');
        $status   = $this->sanitize($_GET['status'] ?? '');
        $religion = $this->sanitize($_GET['religion'] ?? '');
        $gender   = $this->sanitize($_GET['gender'] ?? '');

        $where  = ["u.role = 'user'"];
        $binds  = [];
        if ($search)   { $where[] = "(u.name LIKE ? OR u.profile_id LIKE ? OR p.city LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l,$l]); }
        if ($status)   { $where[] = "p.admin_approved = ?";  $binds[] = $status; }
        if ($religion) { $where[] = "p.religion = ?";        $binds[] = $religion; }
        if ($gender)   { $where[] = "u.gender = ?";          $binds[] = $gender; }

        $sql = "SELECT u.id, u.profile_id, u.name, u.gender, u.status, u.created_at,
                       p.city, p.state, p.religion, p.caste, p.education, p.occupation,
                       p.admin_approved, p.is_highlighted,
                       (SELECT COUNT(*) FROM photos ph WHERE ph.user_id=u.id) AS photo_count
                FROM users u
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY u.created_at DESC";

        $result  = Database::paginate($sql, $binds, $page, $this->perPage);
        $filters = compact('search','status','religion','gender');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/profiles/index.php', array_merge($result, compact('filters','flash')), 'Admin/Views/layouts/main.php');
    }

    public function detail(array $params = []): void {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $user = Database::fetchOne("SELECT u.*, p.* FROM users u LEFT JOIN profiles p ON p.user_id=u.id WHERE u.id=?", [$id]);
        if (!$user) { http_response_code(404); die('Not found.'); }
        $photos = Database::fetchAll("SELECT * FROM photos WHERE user_id=? ORDER BY sort_order", [$id]);
        $horoscope = Database::fetchOne("SELECT * FROM horoscopes WHERE user_id=?", [$id]);
        $flash  = Session::getFlash('success');
        $this->view('Admin/Views/profiles/view.php', compact('user','photos','horoscope','flash'), 'Admin/Views/layouts/main.php');
    }

    public function approve(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE profiles SET admin_approved='approved' WHERE user_id=?", [$id]);
        Database::execute("INSERT INTO notifications (user_id,type,title,body) VALUES (?,'profile_approved','Profile Approved','Your profile has been approved.')", [$id]);
        Database::execute("INSERT INTO activity_logs (user_id,action,module,ip_address) VALUES (?,?,?,?)", [Session::get('admin_id'),'approve_profile','Admin',$_SERVER['REMOTE_ADDR']??'']);
        Session::flash('success','Profile approved.');
        $this->redirect('admin/profiles');
    }

    public function reject(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id   = (int)($_POST['id'] ?? 0);
        $note = $this->sanitize($_POST['note'] ?? '');
        Database::execute("UPDATE profiles SET admin_approved='rejected' WHERE user_id=?", [$id]);
        Database::execute("INSERT INTO notifications (user_id,type,title,body) VALUES (?,'profile_rejected','Profile Rejected',?)", [$id, 'Reason: '.$note]);
        Session::flash('success','Profile rejected.');
        $this->redirect('admin/profiles');
    }
}
