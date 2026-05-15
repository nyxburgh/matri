<?php
// app/modules/Notification/Controllers/NotificationController.php

class NotificationController extends Controller {

    public function index(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        $result    = Database::paginate($sql, [$userId], $page, 20);
        $pageTitle = 'Notifications';

        // Mark all as read on page open
        Database::execute(
            "UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0",
            [$userId]
        );

        $this->view(
            'Notification/Views/index.php',
            array_merge($result, compact('pageTitle')),
            'User/Views/layouts/main.php'
        );
    }

    public function markRead(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');
        $id     = (int)($_POST['id'] ?? 0);

        Database::execute(
            "UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?",
            [$id, $userId]
        );
        $this->json(['success' => true]);
    }

    public function markAllRead(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        Database::execute("UPDATE notifications SET is_read=1 WHERE user_id=?", [$userId]);
        $this->json(['success' => true]);
    }

    public function count(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');

        $count = Database::fetchOne(
            "SELECT COUNT(*) c FROM notifications WHERE user_id=? AND is_read=0",
            [$userId]
        )['c'] ?? 0;

        $unreadMsg = Database::fetchOne(
            "SELECT COUNT(*) c FROM chat_messages WHERE receiver_id=? AND is_read=0",
            [$userId]
        )['c'] ?? 0;

        $this->json(['notif_count' => (int)$count, 'msg_count' => (int)$unreadMsg]);
    }
}
