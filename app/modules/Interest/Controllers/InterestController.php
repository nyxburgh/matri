<?php
// app/modules/Interest/Controllers/InterestController.php

class InterestController extends Controller {

    public function index(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $tab    = $this->input('tab', 'received');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $received = $this->fetchInterests($userId, 'received', $page);
        $sent     = $this->fetchInterests($userId, 'sent', $page);

        $stats = [
            'received_pending'  => Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE receiver_id=? AND status='pending'",  [$userId])['c'] ?? 0,
            'received_accepted' => Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE receiver_id=? AND status='accepted'", [$userId])['c'] ?? 0,
            'sent_pending'      => Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE sender_id=? AND status='pending'",    [$userId])['c'] ?? 0,
            'sent_accepted'     => Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE sender_id=? AND status='accepted'",   [$userId])['c'] ?? 0,
        ];

        $plan      = $this->getUserPlan($userId);
        $flash     = Session::getFlash('success');
        $error     = Session::getFlash('error');
        $pageTitle = 'My Interests';

        $this->view(
            'Interest/Views/index.php',
            compact('received','sent','stats','tab','plan','flash','error','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function sent(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $this->input('status', '');

        $where  = ["i.sender_id = ?"];
        $binds  = [$userId];
        if ($status) { $where[] = "i.status = ?"; $binds[] = $status; }

        $sql = $this->buildInterestSql($where, 'sender');
        $result    = Database::paginate($sql, $binds, $page, 15);
        $pageTitle = 'Interests Sent';

        $this->view(
            'Interest/Views/sent.php',
            array_merge($result, compact('pageTitle', 'status')),
            'User/Views/layouts/main.php'
        );
    }

    public function received(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $status = $this->input('status', '');

        $where = ["i.receiver_id = ?"];
        $binds = [$userId];
        if ($status) { $where[] = "i.status = ?"; $binds[] = $status; }

        $sql = $this->buildInterestSql($where, 'receiver');
        $result    = Database::paginate($sql, $binds, $page, 15);
        $pageTitle = 'Interests Received';

        $this->view(
            'Interest/Views/received.php',
            array_merge($result, compact('pageTitle', 'status')),
            'User/Views/layouts/main.php'
        );
    }

    public function send(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();

        $senderId   = Session::get('user_id');
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message    = $this->sanitize(substr($_POST['message'] ?? '', 0, 500));

        if (!$receiverId || $receiverId === $senderId) {
            $this->json(['success' => false, 'message' => 'Invalid request.']);
        }

        // Check if receiver exists and is active
        $receiver = Database::fetchOne(
            "SELECT id FROM users WHERE id = ? AND status = 'active' AND role = 'user' LIMIT 1",
            [$receiverId]
        );
        if (!$receiver) {
            $this->json(['success' => false, 'message' => 'Profile not found.']);
        }

        // Check for existing interest
        $existing = Database::fetchOne(
            "SELECT id, status FROM interests WHERE sender_id = ? AND receiver_id = ? LIMIT 1",
            [$senderId, $receiverId]
        );
        if ($existing) {
            $msg = $existing['status'] === 'rejected'
                ? 'This profile has declined your interest.'
                : 'You have already sent an interest to this profile.';
            $this->json(['success' => false, 'message' => $msg]);
        }

        // Plan limits check
        $plan = $this->getUserPlan($senderId);
        $limit = (int)$plan['interests_limit'];
        if ($limit > 0) {
            $sentCount = Database::fetchOne(
                "SELECT COUNT(*) c FROM interests WHERE sender_id = ?", [$senderId])['c'] ?? 0;
            if ($sentCount >= $limit) {
                $this->json([
                    'success' => false,
                    'message' => "You have reached your interest limit ({$limit}). Upgrade your plan.",
                    'upgrade' => true,
                ]);
            }
        }

        // Insert interest
        $interestId = Database::insert(
            "INSERT INTO interests (sender_id, receiver_id, message) VALUES (?, ?, ?)",
            [$senderId, $receiverId, $message]
        );

        // Notify receiver
        $senderName = Session::get('user_name');
        Database::execute(
            "INSERT INTO notifications (user_id, type, ref_id, title, body, channel) VALUES (?,?,?,?,?,'inapp')",
            [$receiverId, 'interest_received', (int)$interestId,
             "$senderName sent you an interest",
             $message ?: "$senderName is interested in your profile."]
        );

        $this->logActivity('send_interest', 'Interest', (int)$interestId);
        $this->json(['success' => true, 'message' => 'Interest sent successfully!']);
    }

    public function respond(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();

        $userId     = Session::get('user_id');
        $interestId = (int)($_POST['interest_id'] ?? 0);
        $action     = $this->input('action'); // accepted | rejected

        if (!in_array($action, ['accepted', 'rejected'])) {
            $this->json(['success' => false, 'message' => 'Invalid action.']);
        }

        $interest = Database::fetchOne(
            "SELECT * FROM interests WHERE id = ? AND receiver_id = ? AND status = 'pending' LIMIT 1",
            [$interestId, $userId]
        );
        if (!$interest) {
            $this->json(['success' => false, 'message' => 'Interest not found or already responded.']);
        }

        Database::execute(
            "UPDATE interests SET status = ?, responded_at = NOW() WHERE id = ?",
            [$action, $interestId]
        );

        // Notify sender
        $receiverName = Session::get('user_name');
        $notifType    = $action === 'accepted' ? 'interest_accepted' : 'interest_rejected';
        $notifTitle   = $action === 'accepted'
            ? "$receiverName accepted your interest! 🎉"
            : "$receiverName has declined your interest.";

        Database::execute(
            "INSERT INTO notifications (user_id, type, ref_id, title, body, channel) VALUES (?,?,?,?,?,'inapp')",
            [$interest['sender_id'], $notifType, $interestId, $notifTitle, '']
        );

        $this->logActivity('respond_interest_' . $action, 'Interest', $interestId);
        $this->json([
            'success' => true,
            'action'  => $action,
            'message' => $action === 'accepted' ? 'Interest accepted! You can now chat.' : 'Interest declined.',
            'can_chat'=> $action === 'accepted',
        ]);
    }

    public function cancel(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId     = Session::get('user_id');
        $interestId = (int)($_POST['interest_id'] ?? 0);

        $interest = Database::fetchOne(
            "SELECT * FROM interests WHERE id = ? AND sender_id = ? AND status = 'pending' LIMIT 1",
            [$interestId, $userId]
        );
        if (!$interest) {
            $this->json(['success' => false, 'message' => 'Interest not found.']);
        }

        Database::execute("UPDATE interests SET status = 'cancelled' WHERE id = ?", [$interestId]);
        $this->json(['success' => true, 'message' => 'Interest cancelled.']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fetchInterests(int $userId, string $direction, int $page): array {
        $col = $direction === 'received' ? 'receiver_id' : 'sender_id';
        $joinCol = $direction === 'received' ? 'sender_id' : 'receiver_id';
        $sql = "SELECT i.id, i.status, i.message, i.sent_at, i.responded_at,
                       u.id AS uid, u.name, u.profile_id, u.gender,
                       p.age, p.city, p.religion, p.education, p.occupation, p.marital_status,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo
                FROM interests i
                JOIN users u ON u.id = i.$joinCol
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE i.$col = ?
                ORDER BY i.sent_at DESC";
        return Database::paginate($sql, [$userId], $page, 10);
    }

    private function buildInterestSql(array $where, string $direction): string {
        $joinCol = $direction === 'sender' ? 'receiver_id' : 'sender_id';
        $whereStr = implode(' AND ', $where);
        return "SELECT i.id, i.status, i.message, i.sent_at, i.responded_at,
                       u.id AS uid, u.name, u.profile_id, u.gender,
                       p.age, p.city, p.religion, p.education, p.occupation,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo
                FROM interests i
                JOIN users u ON u.id = i.$joinCol
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE $whereStr
                ORDER BY i.sent_at DESC";
    }
}
