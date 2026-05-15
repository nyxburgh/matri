<?php
// matri_check.php — Place in project ROOT (same level as public/ folder)
// Visit: http://localhost/nyxburgh/matri/matri_check.php
// DELETE this file after fixing.

ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = __DIR__;
echo "<h2>Matrimony Diagnostic</h2>";
echo "<p><b>Project Root:</b> $root</p>";
echo "<hr>";

// Check critical files
$files = [
    'config/config.php',
    'public/index.php',
    'app/core/Autoloader.php',
    'app/core/Router.php',
    'app/core/Controller.php',
    'app/core/Database.php',
    'app/core/Session.php',
    '.env',
    // Admin
    'app/modules/Admin/Routes/admin.routes.php',
    'app/modules/Admin/Controllers/AdminAuthController.php',
    // User module
    'app/modules/User/Routes/user.routes.php',
    'app/modules/User/Controllers/UserAuthController.php',
    'app/modules/User/Controllers/UserDashboardController.php',
    'app/modules/User/Views/layouts/main.php',
    'app/modules/User/Views/home/index.php',
    'app/modules/User/Views/auth/login.php',
    'app/modules/User/Views/auth/register.php',
    'app/modules/User/Views/auth/otp.php',
    'app/modules/User/Views/dashboard/index.php',
    'app/modules/User/Views/profile/view.php',
    'app/modules/User/Views/profile/edit.php',
    // Profile
    'app/modules/Profile/Routes/profile.routes.php',
    'app/modules/Profile/Controllers/ProfileController.php',
    // Search
    'app/modules/Search/Routes/search.routes.php',
    'app/modules/Search/Controllers/SearchController.php',
    // Interest
    'app/modules/Interest/Routes/interest.routes.php',
    'app/modules/Interest/Controllers/InterestController.php',
    // Chat
    'app/modules/Chat/Routes/chat.routes.php',
    'app/modules/Chat/Controllers/ChatController.php',
    // Notification
    'app/modules/Notification/Routes/notification.routes.php',
    'app/modules/Notification/Controllers/NotificationController.php',
    // Subscription
    'app/modules/Subscription/Routes/subscription.routes.php',
    'app/modules/Subscription/Controllers/SubscriptionController.php',
];

$ok = 0; $missing = 0;
echo "<table border='1' cellpadding='6' cellspacing='0' style='font-family:monospace;font-size:13px'>";
echo "<tr><th>File</th><th>Status</th></tr>";
foreach ($files as $f) {
    $exists = file_exists($root . '/' . $f);
    $exists ? $ok++ : $missing++;
    $color  = $exists ? '#d4edda' : '#f8d7da';
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    echo "<tr style='background:$color'><td>$f</td><td><b>$status</b></td></tr>";
}
echo "</table>";
echo "<p><b>$ok OK, $missing MISSING</b></p><hr>";

// Check .env contents (APP_DEBUG)
$envFile = $root . '/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    $debugOn = str_contains($env, 'APP_DEBUG=true');
    echo "<p><b>APP_DEBUG:</b> " . ($debugOn ? "<span style='color:green'>true ✓</span>" : "<span style='color:red'>false — errors are hidden! Change to APP_DEBUG=true in .env</span>") . "</p>";
    $url = '';
    foreach (explode("\n", $env) as $line) {
        if (str_starts_with(trim($line), 'APP_URL')) { $url = trim($line); break; }
    }
    echo "<p><b>$url</b></p>";
} else {
    echo "<p style='color:red'><b>.env file MISSING!</b></p>";
}

// Try DB connection
echo "<hr><h3>Database Test</h3>";
try {
    require_once $root . '/config/config.php';
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<p style='color:green'>✓ Database connected. Users: $count</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ DB Error: " . $e->getMessage() . "</p>";
}

echo "<hr><p style='color:red'><b>DELETE matri_check.php after fixing!</b></p>";
