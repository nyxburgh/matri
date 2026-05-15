<?php
// app/modules/Admin/Controllers/AdminSettingsController.php

class AdminSettingsController extends Controller {

    public function index(array $params = []): void {
        $this->requireAdmin();
        $rows     = Database::fetchAll("SELECT * FROM settings ORDER BY `group`, id");
        $settings = [];
        foreach ($rows as $r) $settings[$r['group']][] = $r;
        $flash = Session::getFlash('success');
        $this->view('Admin/Views/settings/index.php', compact('settings', 'flash'), 'Admin/Views/layouts/main.php');
    }

    public function save(array $params = []): void {
        $this->requireAdmin(); $this->verifyCsrf();
        foreach ($_POST as $key => $val) {
            if ($key === '_csrf') continue;
            Database::execute(
                "UPDATE settings SET value=? WHERE `key`=?",
                [htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8'), $key]
            );
        }
        Database::execute(
            "INSERT INTO activity_logs (user_id,action,module,ip_address) VALUES (?,?,?,?)",
            [Session::get('admin_id'), 'update_settings', 'Admin', $_SERVER['REMOTE_ADDR'] ?? '']
        );
        Session::flash('success', 'Settings saved.');
        $this->redirect('admin/settings');
    }
}
