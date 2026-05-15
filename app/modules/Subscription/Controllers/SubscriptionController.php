<?php
// app/modules/Subscription/Controllers/SubscriptionController.php

class SubscriptionController extends Controller {

    public function index(array $params = []): void {
        $this->requireUser();
        $userId       = Session::get('user_id');
        $plans        = Database::fetchAll("SELECT * FROM subscription_plans WHERE is_active=1 ORDER BY sort_order");
        $currentPlan  = $this->getUserPlan($userId);
        $flash        = Session::getFlash('success');
        $error        = Session::getFlash('error');
        $pageTitle    = 'Upgrade Plan';

        $this->view(
            'Subscription/Views/index.php',
            compact('plans','currentPlan','flash','error','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function checkout(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $planId = (int)($params['plan_id'] ?? 0);

        $plan = Database::fetchOne(
            "SELECT * FROM subscription_plans WHERE id=? AND is_active=1 LIMIT 1",
            [$planId]
        );
        if (!$plan || $plan['price'] == 0) {
            Session::flash('error', 'Invalid plan selected.');
            $this->redirect('subscription');
        }

        $currentPlan = $this->getUserPlan($userId);
        $pageTitle   = 'Checkout — ' . $plan['name'] . ' Plan';

        // Razorpay key from settings
        $razorpayKey = Database::fetchOne("SELECT value FROM settings WHERE `key`='razorpay_key_id'")['value'] ?? '';

        $this->view(
            'Subscription/Views/checkout.php',
            compact('plan','currentPlan','razorpayKey','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    /**
     * Create Razorpay order (AJAX)
     */
    public function initiate(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();

        $userId = Session::get('user_id');
        $planId = (int)($_POST['plan_id'] ?? 0);

        $plan = Database::fetchOne("SELECT * FROM subscription_plans WHERE id=? AND is_active=1 LIMIT 1", [$planId]);
        if (!$plan) { $this->json(['success' => false, 'message' => 'Plan not found.']); }

        $amount = (int)($plan['price'] * 100); // Razorpay uses paise

        // Create DB payment record
        $paymentId = Database::insert(
            "INSERT INTO payments (user_id, plan_id, amount, currency, gateway, status) VALUES (?,?,?,?,'razorpay','initiated')",
            [$userId, $planId, $plan['price'], 'INR']
        );

        // TODO: Call Razorpay API to create order
        // $razorpay = new RazorpayClient($keyId, $keySecret);
        // $order = $razorpay->order->create(['amount'=>$amount,'currency'=>'INR','receipt'=>'PAY-'.$paymentId]);
        // For now, mock order ID:
        $razorpayOrderId = 'order_' . bin2hex(random_bytes(8));

        Database::execute(
            "UPDATE payments SET gateway_order_id=? WHERE id=?",
            [$razorpayOrderId, $paymentId]
        );

        $user = Database::fetchOne("SELECT name, email, mobile FROM users WHERE id=? LIMIT 1", [$userId]);

        $this->json([
            'success'    => true,
            'payment_id' => $paymentId,
            'order_id'   => $razorpayOrderId,
            'amount'     => $amount,
            'currency'   => 'INR',
            'plan_name'  => $plan['name'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_mobile'=> $user['mobile'],
        ]);
    }

    /**
     * Verify Razorpay payment signature (AJAX POST after payment)
     */
    public function verify(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();

        $userId          = Session::get('user_id');
        $paymentId       = (int)($_POST['payment_db_id'] ?? 0);
        $razorpayPayId   = $this->sanitize($_POST['razorpay_payment_id'] ?? '');
        $razorpayOrderId = $this->sanitize($_POST['razorpay_order_id'] ?? '');
        $razorpaySign    = $this->sanitize($_POST['razorpay_signature'] ?? '');

        $payment = Database::fetchOne(
            "SELECT * FROM payments WHERE id=? AND user_id=? AND status='initiated' LIMIT 1",
            [$paymentId, $userId]
        );
        if (!$payment) { $this->json(['success' => false, 'message' => 'Payment record not found.']); }

        // Signature verification
        $keySecret = Database::fetchOne("SELECT value FROM settings WHERE `key`='razorpay_key_secret'")['value'] ?? '';
        $generated = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPayId, $keySecret);

        if (!hash_equals($generated, $razorpaySign)) {
            Database::execute(
                "UPDATE payments SET status='failed' WHERE id=?", [$paymentId]
            );
            $this->json(['success' => false, 'message' => 'Payment verification failed. Please contact support.']);
        }

        // Mark payment success
        Database::execute(
            "UPDATE payments SET gateway_payment_id=?, gateway_signature=?, status='success', paid_at=NOW() WHERE id=?",
            [$razorpayPayId, $razorpaySign, $paymentId]
        );

        // Activate subscription
        $plan = Database::fetchOne("SELECT * FROM subscription_plans WHERE id=? LIMIT 1", [$payment['plan_id']]);
        $endDate = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));

        // Cancel existing active sub
        Database::execute(
            "UPDATE user_subscriptions SET status='cancelled' WHERE user_id=? AND status='active'",
            [$userId]
        );

        Database::execute(
            "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, payment_id)
             VALUES (?,?,CURDATE(),?,'active',?)",
            [$userId, $plan['id'], $endDate, $paymentId]
        );

        $this->logActivity('subscription_upgraded', 'Subscription', $paymentId,
            ['plan' => $plan['name'], 'amount' => $payment['amount']]);

        Session::flash('success', "🎉 Welcome to {$plan['name']} plan! Your subscription is now active.");
        $this->json(['success' => true, 'redirect' => APP_URL . '/subscription/success']);
    }

    public function success(array $params = []): void {
        $this->requireUser();
        $userId      = Session::get('user_id');
        $plan        = $this->getUserPlan($userId);
        $flash       = Session::getFlash('success');
        $pageTitle   = 'Subscription Activated';

        $this->view(
            'Subscription/Views/success.php',
            compact('plan','flash','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function history(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $sql = "SELECT us.*, sp.name AS plan_name, sp.price, p.gateway, p.gateway_payment_id, p.paid_at, p.status AS payment_status
                FROM user_subscriptions us
                JOIN subscription_plans sp ON sp.id = us.plan_id
                LEFT JOIN payments p ON p.id = us.payment_id
                WHERE us.user_id = ?
                ORDER BY us.created_at DESC";

        $result    = Database::paginate($sql, [$userId], $page, 10);
        $pageTitle = 'Subscription History';

        $this->view(
            'Subscription/Views/history.php',
            array_merge($result, compact('pageTitle')),
            'User/Views/layouts/main.php'
        );
    }
}
