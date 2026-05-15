<?php
// app/modules/Admin/Controllers/AdminCmsController.php

class AdminCmsController extends Controller {

    public function stories(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');
        $where  = []; $binds = [];
        if ($search) { $where[] = "(bride_name LIKE ? OR groom_name LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l]); }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql    = "SELECT * FROM success_stories $whereStr ORDER BY created_at DESC";
        $result = Database::paginate($sql, $binds, $page, 15);
        $filters = compact('search');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/cms/stories.php', array_merge($result, compact('filters', 'flash')), 'Admin/Views/layouts/main.php');
    }

    public function saveStory(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id      = (int)($_POST['id'] ?? 0);
        $bride   = $this->sanitize($_POST['bride_name'] ?? '');
        $groom   = $this->sanitize($_POST['groom_name'] ?? '');
        $story   = $this->sanitize($_POST['story'] ?? '');
        $featured = (int)($_POST['is_featured'] ?? 0);
        $pub     = (int)($_POST['is_published'] ?? 0);
        $married = !empty($_POST['married_on']) ? $_POST['married_on'] : null;
        if ($id) {
            Database::execute(
                "UPDATE success_stories SET bride_name=?,groom_name=?,story=?,married_on=?,is_featured=?,is_published=? WHERE id=?",
                [$bride, $groom, $story, $married, $featured, $pub, $id]
            );
        } else {
            Database::execute(
                "INSERT INTO success_stories (bride_name,groom_name,story,married_on,is_featured,is_published) VALUES (?,?,?,?,?,?)",
                [$bride, $groom, $story, $married, $featured, $pub]
            );
        }
        Session::flash('success', 'Story saved.');
        $this->redirect('admin/cms/stories');
    }

    public function deleteStory(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Database::execute("DELETE FROM success_stories WHERE id=?", [$id]);
        Session::flash('success', 'Story deleted.');
        $this->redirect('admin/cms/stories');
    }

    public function seoPages(array $params = []): void {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $this->sanitize($_GET['search'] ?? '');
        $where  = []; $binds = [];
        if ($search) { $where[] = "(slug LIKE ? OR caste_key LIKE ? OR title_en LIKE ?)"; $l = "%$search%"; $binds = array_merge($binds, [$l, $l, $l]); }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql    = "SELECT id,slug,caste_key,title_en,is_published,sort_order,updated_at FROM seo_pages $whereStr ORDER BY sort_order,id DESC";
        $result = Database::paginate($sql, $binds, $page, 20);
        $filters = compact('search');
        $flash   = Session::getFlash('success');
        $this->view('Admin/Views/cms/seo.php', array_merge($result, compact('filters', 'flash')), 'Admin/Views/layouts/main.php');
    }

    public function saveSeoPage(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        $id     = (int)($_POST['id'] ?? 0);
        $fields = ['slug','caste_key','title_en','title_ta','meta_desc_en','meta_desc_ta',
                   'content_en','content_ta','meta_keywords','is_published','sort_order'];
        $data = [];
        foreach ($fields as $f) $data[$f] = $this->sanitize($_POST[$f] ?? '');
        if ($id) {
            $sets = implode(',', array_map(fn($f) => "$f=?", array_keys($data)));
            Database::execute("UPDATE seo_pages SET $sets,updated_at=NOW() WHERE id=?", array_merge(array_values($data), [$id]));
        } else {
            $cols = implode(',', array_keys($data));
            $phs  = implode(',', array_fill(0, count($data), '?'));
            Database::execute("INSERT INTO seo_pages ($cols) VALUES ($phs)", array_values($data));
        }
        Session::flash('success', 'SEO page saved.');
        $this->redirect('admin/cms/seo');
    }
}
