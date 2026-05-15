<?php
// public/index.php — Front Controller
//
// IMPORTANT: config/config.php owns the BASE_PATH constant (line 49).
// We must NOT call define('BASE_PATH', ...) here first — that causes
// "Constant already defined" warning and a white page.
//
// Solution: use a plain local variable just for this one bootstrap require,
// then BASE_PATH constant is available for every subsequent require.

$__boot = dirname(__DIR__);

require_once $__boot . '/config/config.php';       // ← defines BASE_PATH constant
require_once BASE_PATH . '/app/core/Autoloader.php';
require_once BASE_PATH . '/app/core/Logger.php';
require_once BASE_PATH . '/app/core/Database.php';
require_once BASE_PATH . '/app/core/Session.php';
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Controller.php';

Session::start();

$router = new Router();

// ── Load Module Routes ─────────────────────────────────────────────────────
require_once BASE_PATH . '/app/modules/Admin/Routes/admin.routes.php';
require_once BASE_PATH . '/app/modules/User/Routes/user.routes.php';
require_once BASE_PATH . '/app/modules/Profile/Routes/profile.routes.php';
require_once BASE_PATH . '/app/modules/Search/Routes/search.routes.php';
require_once BASE_PATH . '/app/modules/Interest/Routes/interest.routes.php';
require_once BASE_PATH . '/app/modules/Chat/Routes/chat.routes.php';
require_once BASE_PATH . '/app/modules/Notification/Routes/notification.routes.php';
require_once BASE_PATH . '/app/modules/Subscription/Routes/subscription.routes.php';

// ── Dispatch ──────────────────────────────────────────────────────────────
$uri           = $_GET['url'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];
$router->dispatch('/' . trim($uri, '/'), $requestMethod);
