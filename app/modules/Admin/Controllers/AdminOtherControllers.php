<?php
// app/modules/Admin/Controllers/AdminPhotoController.php
class AdminPhotoController extends Controller {
    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $this->sanitize($_GET['status'] ?? 'pending');
        $search = $this->sanitize($_GET['search'] ?? '');
        $where  = []; $binds = [];
        if ($status) { $where[] = "ph.is_approved=?"; $binds[]=$status; }
        if ($search) { $where[] = "(u.name LIKE ? OR u.profile_id LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l]); }
        $whereStr = $where ? 'WHERE '.implode(' AND ',$where) : '';
        $sql = "SELECT ph.*, u.name, u.profile_id, u.gender FROM photos ph JOIN users u ON u.id=ph.user_id $whereStr ORDER BY ph.created_at DESC";
        $result = Database::paginate($sql, $binds, $page, 24);
        $filters = compact('status','search');
        $flash = Session::getFlash('success');
        $this->view('Admin/Views/photos/index.php', array_merge($result, compact('filters','flash')), 'Admin/Views/layouts/main.php');
    }
    public function approve(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE photos SET is_approved='approved' WHERE id=?", [$id]);
        Session::flash('success','Photo approved.'); $this->redirect('admin/photos');
    }
    public function reject(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("UPDATE photos SET is_approved='rejected' WHERE id=?", [$id]);
        Session::flash('success','Photo rejected.'); $this->redirect('admin/photos');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminReportController.php
class AdminReportController extends Controller {
    public function index(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1,(int)($_GET['page']??1));
        $status = $this->sanitize($_GET['status']??'open');
        $search = $this->sanitize($_GET['search']??'');
        $where  = []; $binds = [];
        if ($status) { $where[]="r.status=?"; $binds[]=$status; }
        if ($search) { $where[]="(u1.name LIKE ? OR u2.name LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l]); }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT r.*, u1.name AS reporter_name, u1.profile_id AS reporter_pid,
                     u2.name AS reported_name, u2.profile_id AS reported_pid
              FROM reports r
              JOIN users u1 ON u1.id=r.reporter_id
              JOIN users u2 ON u2.id=r.reported_id
              $whereStr ORDER BY r.created_at DESC";
        $result=Database::paginate($sql,$binds,$page,20);
        $filters=compact('status','search'); $flash=Session::getFlash('success');
        $this->view('Admin/Views/reports/index.php',array_merge($result,compact('filters','flash')),'Admin/Views/layouts/main.php');
    }
    public function view(array $params=[]): void {
        $this->requireAdmin();
        $id=(int)($params['id']??0);
        $report=Database::fetchOne("SELECT r.*, u1.name AS reporter_name, u2.name AS reported_name FROM reports r JOIN users u1 ON u1.id=r.reporter_id JOIN users u2 ON u2.id=r.reported_id WHERE r.id=?",[$id]);
        $this->view('Admin/Views/reports/view.php',compact('report'),'Admin/Views/layouts/main.php');
    }
    public function action(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id=$_POST['id']??0; $action=$this->sanitize($_POST['action']??''); $note=$this->sanitize($_POST['admin_note']??'');
        Database::execute("UPDATE reports SET status=?,admin_note=?,updated_at=NOW() WHERE id=?",[$action,$note,$id]);
        Session::flash('success','Report updated.'); $this->redirect('admin/reports');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminSubscriptionController.php
class AdminSubscriptionController extends Controller {
    public function index(array $params=[]): void {
        $this->requireAdmin();
        $page   = max(1,(int)($_GET['page']??1));
        $search = $this->sanitize($_GET['search']??'');
        $status = $this->sanitize($_GET['status']??'');
        $plan   = $this->sanitize($_GET['plan']??'');
        $where=[]; $binds=[];
        if ($search) { $where[]="(u.name LIKE ? OR u.profile_id LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l]); }
        if ($status) { $where[]="us.status=?"; $binds[]=$status; }
        if ($plan)   { $where[]="us.plan_id=?"; $binds[]=$plan; }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT us.*, u.name, u.profile_id, u.gender, sp.name AS plan_name, sp.price
              FROM user_subscriptions us
              JOIN users u ON u.id=us.user_id
              JOIN subscription_plans sp ON sp.id=us.plan_id
              $whereStr ORDER BY us.created_at DESC";
        $result=Database::paginate($sql,$binds,$page,20);
        $plans=Database::fetchAll("SELECT * FROM subscription_plans ORDER BY sort_order");
        $filters=compact('search','status','plan'); $flash=Session::getFlash('success');
        $this->view('Admin/Views/subscriptions/index.php',array_merge($result,compact('filters','plans','flash')),'Admin/Views/layouts/main.php');
    }
    public function plans(array $params=[]): void {
        $this->requireAdmin();
        $plans=Database::fetchAll("SELECT * FROM subscription_plans ORDER BY sort_order");
        $flash=Session::getFlash('success');
        $this->view('Admin/Views/subscriptions/plans.php',compact('plans','flash'),'Admin/Views/layouts/main.php');
    }
    public function savePlan(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id=(int)($_POST['id']??0);
        $fields=['name','code','duration_days','price','interests_limit','chat_limit','contact_view','advanced_search','highlight','photo_request','is_active','sort_order'];
        $data=[];
        foreach($fields as $f) $data[$f]=$this->sanitize($_POST[$f]??'0');
        if ($id) {
            $sets=implode(',',array_map(fn($f)=>"$f=?",array_keys($data)));
            Database::execute("UPDATE subscription_plans SET $sets WHERE id=?",array_merge(array_values($data),[$id]));
        } else {
            $cols=implode(',',array_keys($data)); $phs=implode(',',array_fill(0,count($data),'?'));
            Database::execute("INSERT INTO subscription_plans ($cols) VALUES ($phs)",array_values($data));
        }
        Session::flash('success','Plan saved.'); $this->redirect('admin/plans');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminPaymentController.php
class AdminPaymentController extends Controller {
    public function index(array $params=[]): void {
        $this->requireAdmin();
        $page=max(1,(int)($_GET['page']??1));
        $search=$this->sanitize($_GET['search']??'');
        $status=$this->sanitize($_GET['status']??'');
        $gateway=$this->sanitize($_GET['gateway']??'');
        $from=$this->sanitize($_GET['from']??'');
        $to=$this->sanitize($_GET['to']??'');
        $where=[]; $binds=[];
        if ($search) { $where[]="(u.name LIKE ? OR u.profile_id LIKE ? OR py.gateway_payment_id LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l,$l]); }
        if ($status)  { $where[]="py.status=?";  $binds[]=$status; }
        if ($gateway) { $where[]="py.gateway=?"; $binds[]=$gateway; }
        if ($from)    { $where[]="DATE(py.created_at)>=?"; $binds[]=$from; }
        if ($to)      { $where[]="DATE(py.created_at)<=?"; $binds[]=$to; }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT py.*, u.name, u.profile_id, sp.name AS plan_name
              FROM payments py JOIN users u ON u.id=py.user_id JOIN subscription_plans sp ON sp.id=py.plan_id
              $whereStr ORDER BY py.created_at DESC";
        $result=Database::paginate($sql,$binds,$page,20);
        $filters=compact('search','status','gateway','from','to');
        $this->view('Admin/Views/payments/index.php',array_merge($result,compact('filters')),'Admin/Views/layouts/main.php');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminLogController.php
class AdminLogController extends Controller {
    public function index(array $params=[]): void {
        $this->requireAdmin();
        $page=max(1,(int)($_GET['page']??1));
        $search=$this->sanitize($_GET['search']??'');
        $action=$this->sanitize($_GET['action']??'');
        $from=$this->sanitize($_GET['from']??'');
        $to=$this->sanitize($_GET['to']??'');
        $where=[]; $binds=[];
        if ($search) { $where[]="(u.name LIKE ? OR al.ip_address LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l]); }
        if ($action) { $where[]="al.action LIKE ?"; $binds[]="%$action%"; }
        if ($from)   { $where[]="DATE(al.created_at)>=?"; $binds[]=$from; }
        if ($to)     { $where[]="DATE(al.created_at)<=?"; $binds[]=$to; }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT al.*, u.name, u.profile_id FROM activity_logs al LEFT JOIN users u ON u.id=al.user_id $whereStr ORDER BY al.created_at DESC";
        $result=Database::paginate($sql,$binds,$page,30);
        $filters=compact('search','action','from','to');
        $this->view('Admin/Views/logs/index.php',array_merge($result,compact('filters')),'Admin/Views/layouts/main.php');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminSettingsController.php
class AdminSettingsController extends Controller {
    public function index(array $params=[]): void {
        $this->requireAdmin();
        $rows=Database::fetchAll("SELECT * FROM settings ORDER BY `group`,id");
        $settings=[]; foreach($rows as $r) $settings[$r['group']][]=$r;
        $flash=Session::getFlash('success');
        $this->view('Admin/Views/settings/index.php',compact('settings','flash'),'Admin/Views/layouts/main.php');
    }
    public function save(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        foreach ($_POST as $key=>$val) {
            if ($key==='_csrf') continue;
            Database::execute("UPDATE settings SET value=? WHERE `key`=?",[htmlspecialchars(strip_tags(trim($val)),ENT_QUOTES,'UTF-8'),$key]);
        }
        Database::execute("INSERT INTO activity_logs (user_id,action,module,ip_address) VALUES (?,?,?,?)",[Session::get('admin_id'),'update_settings','Admin',$_SERVER['REMOTE_ADDR']??'']);
        Session::flash('success','Settings saved.'); $this->redirect('admin/settings');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

// app/modules/Admin/Controllers/AdminCmsController.php
class AdminCmsController extends Controller {
    public function stories(array $params=[]): void {
        $this->requireAdmin();
        $page=max(1,(int)($_GET['page']??1));
        $search=$this->sanitize($_GET['search']??'');
        $where=[]; $binds=[];
        if ($search) { $where[]="(bride_name LIKE ? OR groom_name LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l]); }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT * FROM success_stories $whereStr ORDER BY created_at DESC";
        $result=Database::paginate($sql,$binds,$page,15);
        $filters=compact('search'); $flash=Session::getFlash('success');
        $this->view('Admin/Views/cms/stories.php',array_merge($result,compact('filters','flash')),'Admin/Views/layouts/main.php');
    }
    public function saveStory(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id=(int)($_POST['id']??0);
        $bride=$this->sanitize($_POST['bride_name']??''); $groom=$this->sanitize($_POST['groom_name']??'');
        $story=$this->sanitize($_POST['story']??''); $featured=(int)($_POST['is_featured']??0); $pub=(int)($_POST['is_published']??0);
        $married=!empty($_POST['married_on'])?$_POST['married_on']:null;
        if ($id) Database::execute("UPDATE success_stories SET bride_name=?,groom_name=?,story=?,married_on=?,is_featured=?,is_published=? WHERE id=?",[$bride,$groom,$story,$married,$featured,$pub,$id]);
        else     Database::execute("INSERT INTO success_stories (bride_name,groom_name,story,married_on,is_featured,is_published) VALUES (?,?,?,?,?,?)",[$bride,$groom,$story,$married,$featured,$pub]);
        Session::flash('success','Story saved.'); $this->redirect('admin/cms/stories');
    }
    public function deleteStory(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id=(int)($_POST['id']??0);
        Database::execute("DELETE FROM success_stories WHERE id=?",[$id]);
        Session::flash('success','Story deleted.'); $this->redirect('admin/cms/stories');
    }
    public function seoPages(array $params=[]): void {
        $this->requireAdmin();
        $page=max(1,(int)($_GET['page']??1));
        $search=$this->sanitize($_GET['search']??'');
        $where=[]; $binds=[];
        if ($search) { $where[]="(slug LIKE ? OR caste_key LIKE ? OR title_en LIKE ?)"; $l="%$search%"; $binds=array_merge($binds,[$l,$l,$l]); }
        $whereStr=$where?'WHERE '.implode(' AND ',$where):'';
        $sql="SELECT id,slug,caste_key,title_en,is_published,sort_order,updated_at FROM seo_pages $whereStr ORDER BY sort_order,id DESC";
        $result=Database::paginate($sql,$binds,$page,20);
        $filters=compact('search'); $flash=Session::getFlash('success');
        $this->view('Admin/Views/cms/seo.php',array_merge($result,compact('filters','flash')),'Admin/Views/layouts/main.php');
    }
    public function saveSeoPage(array $params=[]): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id=(int)($_POST['id']??0);
        $fields=['slug','caste_key','title_en','title_ta','meta_desc_en','meta_desc_ta','content_en','content_ta','meta_keywords','is_published','sort_order'];
        $data=[]; foreach($fields as $f) $data[$f]=$this->sanitize($_POST[$f]??'');
        if ($id) { $sets=implode(',',array_map(fn($f)=>"$f=?",array_keys($data))); Database::execute("UPDATE seo_pages SET $sets,updated_at=NOW() WHERE id=?",array_merge(array_values($data),[$id])); }
        else { $cols=implode(',',array_keys($data)); $phs=implode(',',array_fill(0,count($data),'?')); Database::execute("INSERT INTO seo_pages ($cols) VALUES ($phs)",array_values($data)); }
        Session::flash('success','SEO page saved.'); $this->redirect('admin/cms/seo');
    }
}
