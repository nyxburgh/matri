<?php
// app/modules/Chat/Controllers/ChatController.php

class ChatController extends Controller {

    /**
     * Chat inbox — list all accepted-interest conversations
     */
    public function index(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');

        // Get all users this person has accepted interests with
        $threads = Database::fetchAll(
            "SELECT
                CASE WHEN i.sender_id = ? THEN i.receiver_id ELSE i.sender_id END AS partner_id,
                i.id AS interest_id,
                u.name AS partner_name, u.profile_id AS partner_pid, u.gender AS partner_gender,
                u.last_active AS partner_last_active,
                (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                 AND ph.is_approved='approved' LIMIT 1) AS partner_photo,
                (SELECT cm.message FROM chat_messages cm
                 WHERE cm.interest_id=i.id ORDER BY cm.created_at DESC LIMIT 1) AS last_message,
                (SELECT cm.created_at FROM chat_messages cm
                 WHERE cm.interest_id=i.id ORDER BY cm.created_at DESC LIMIT 1) AS last_msg_time,
                (SELECT COUNT(*) FROM chat_messages cm
                 WHERE cm.interest_id=i.id AND cm.receiver_id=? AND cm.is_read=0) AS unread_count
             FROM interests i
             JOIN users u ON u.id = CASE WHEN i.sender_id=? THEN i.receiver_id ELSE i.sender_id END
             WHERE (i.sender_id=? OR i.receiver_id=?) AND i.status='accepted' AND u.status='active'
             ORDER BY last_msg_time DESC, i.id DESC",
            [$userId, $userId, $userId, $userId, $userId]
        );

        $plan      = $this->getUserPlan($userId);
        $pageTitle = 'Messages';

        $this->view(
            'Chat/Views/index.php',
            compact('threads', 'plan', 'pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    /**
     * Chat thread with a specific user
     */
    public function thread(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $partnerId = (int)($params['user_id'] ?? 0);

        // Verify accepted interest exists
        $interest = Database::fetchOne(
            "SELECT * FROM interests
             WHERE ((sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?))
               AND status='accepted' LIMIT 1",
            [$userId, $partnerId, $partnerId, $userId]
        );

        if (!$interest) {
            Session::flash('error', 'You can only chat after an interest is accepted.');
            $this->redirect('interests');
        }

        // Check plan allows chat
        $plan = $this->getUserPlan($userId);
        if (!$plan['chat_limit']) {
            Session::flash('info', 'Chat is available on Silver plan and above. Upgrade to send messages.');
            $this->redirect('subscription');
        }

        $partner = Database::fetchOne(
            "SELECT u.id, u.name, u.profile_id, u.gender, u.last_active,
                    p.city, p.occupation,
                    (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                     AND ph.is_approved='approved' LIMIT 1) AS photo
             FROM users u LEFT JOIN profiles p ON p.user_id=u.id
             WHERE u.id = ? AND u.status='active' LIMIT 1",
            [$partnerId]
        );

        if (!$partner) {
            $this->redirect('chat');
        }

        // Load last 50 messages
        $messages = Database::fetchAll(
            "SELECT * FROM chat_messages
             WHERE interest_id = ?
               AND (
                   (sender_id=? AND is_deleted_sender=0) OR
                   (receiver_id=? AND is_deleted_receiver=0)
               )
             ORDER BY created_at ASC LIMIT 50",
            [$interest['id'], $userId, $userId]
        );

        // Mark messages as read
        Database::execute(
            "UPDATE chat_messages SET is_read=1, read_at=NOW()
             WHERE interest_id=? AND receiver_id=? AND is_read=0",
            [$interest['id'], $userId]
        );

        $pageTitle = 'Chat with ' . $partner['name'];
        $this->view(
            'Chat/Views/thread.php',
            compact('partner', 'messages', 'interest', 'plan', 'pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    /**
     * Send a message (AJAX POST)
     */
    public function send(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();

        $userId     = Session::get('user_id');
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message    = trim($_POST['message'] ?? '');
        $msgType    = in_array($_POST['msg_type'] ?? 'text', ['text','emoji']) ? $_POST['msg_type'] : 'text';

        if (!$receiverId || empty($message)) {
            $this->json(['success' => false, 'message' => 'Message cannot be empty.']);
        }
        if (strlen($message) > 1000) {
            $this->json(['success' => false, 'message' => 'Message too long (max 1000 characters).']);
        }

        // Verify accepted interest
        $interest = Database::fetchOne(
            "SELECT * FROM interests
             WHERE ((sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?))
               AND status='accepted' LIMIT 1",
            [$userId, $receiverId, $receiverId, $userId]
        );
        if (!$interest) {
            $this->json(['success' => false, 'message' => 'Cannot send message. Interest not accepted.']);
        }

        // Plan check
        $plan = $this->getUserPlan($userId);
        if (!$plan['chat_limit']) {
            $this->json(['success' => false, 'message' => 'Upgrade to Silver or above to send messages.', 'upgrade' => true]);
        }

        $msgId = Database::insert(
            "INSERT INTO chat_messages (sender_id, receiver_id, interest_id, message, msg_type) VALUES (?,?,?,?,?)",
            [$userId, $receiverId, $interest['id'], htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), $msgType]
        );

        // Notification
        Database::execute(
            "INSERT INTO notifications (user_id, type, ref_id, title, body, channel) VALUES (?,?,?,?,?,'inapp')",
            [$receiverId, 'new_message', (int)$msgId,
             Session::get('user_name') . ' sent you a message',
             substr($message, 0, 80)]
        );

        $this->json([
            'success'    => true,
            'message_id' => $msgId,
            'message'    => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            'sent_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Poll for new messages (AJAX long-poll style)
     */
    public function poll(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $partnerId = (int)($params['user_id'] ?? 0);
        $lastId    = (int)($_GET['last_id'] ?? 0);

        $interest = Database::fetchOne(
            "SELECT id FROM interests
             WHERE ((sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?))
               AND status='accepted' LIMIT 1",
            [$userId, $partnerId, $partnerId, $userId]
        );
        if (!$interest) { $this->json(['messages' => []]); }

        $messages = Database::fetchAll(
            "SELECT id, sender_id, message, msg_type, is_read, created_at
             FROM chat_messages
             WHERE interest_id=? AND id > ?
               AND ((sender_id=? AND is_deleted_sender=0) OR (receiver_id=? AND is_deleted_receiver=0))
             ORDER BY created_at ASC LIMIT 20",
            [$interest['id'], $lastId, $userId, $userId]
        );

        // Mark as read
        if ($messages) {
            Database::execute(
                "UPDATE chat_messages SET is_read=1, read_at=NOW()
                 WHERE interest_id=? AND receiver_id=? AND is_read=0",
                [$interest['id'], $userId]
            );
        }

        $this->json(['messages' => $messages]);
    }

    /**
     * Mark all messages in thread as read
     */
    public function markRead(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $partnerId = (int)($params['user_id'] ?? 0);

        $interest = Database::fetchOne(
            "SELECT id FROM interests
             WHERE ((sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?))
               AND status='accepted' LIMIT 1",
            [$userId, $partnerId, $partnerId, $userId]
        );
        if (!$interest) { $this->json(['success' => false]); }

        Database::execute(
            "UPDATE chat_messages SET is_read=1, read_at=NOW()
             WHERE interest_id=? AND receiver_id=? AND is_read=0",
            [$interest['id'], $userId]
        );
        $this->json(['success' => true]);
    }
}
