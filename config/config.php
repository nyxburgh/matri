<?php
// config/config.php — Load .env and define constants

if (!function_exists('matri_detect_app_url')) {
    function matri_detect_app_url(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $appRoot = realpath(dirname(__DIR__));

        if ($documentRoot && $appRoot && str_starts_with($appRoot, $documentRoot)) {
            $basePath = substr($appRoot, strlen($documentRoot)) ?: '';
            $basePath = str_replace(DIRECTORY_SEPARATOR, '/', $basePath);
            return $scheme . '://' . $host . rtrim($basePath, '/');
        }

        return $scheme . '://' . $host . '/nyxburgh/matri';
    }
}

function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

loadEnv(dirname(__DIR__) . '/.env');

define('APP_NAME',    $_ENV['APP_NAME']    ?? 'MyMatrimony');
define('APP_URL',     $_ENV['APP_URL']     ?? matri_detect_app_url());
define('APP_ENV',     $_ENV['APP_ENV']     ?? 'production');
define('APP_DEBUG',   ($_ENV['APP_DEBUG']  ?? 'false') === 'true');

define('DB_HOST',     $_ENV['DB_HOST']     ?? 'localhost');
define('DB_PORT',     $_ENV['DB_PORT']     ?? '3306');
define('DB_NAME',     $_ENV['DB_NAME']     ?? 'matrimony_db');
define('DB_USER',     $_ENV['DB_USER']     ?? 'root');
define('DB_PASS',     $_ENV['DB_PASS']     ?? '');

define('SESSION_TIMEOUT', (int)($_ENV['SESSION_TIMEOUT'] ?? 30));
define('SESSION_NAME',    $_ENV['SESSION_NAME'] ?? 'MAT_SESSION');

defined('BASE_PATH') || define('BASE_PATH',   dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/photos');
define('LOG_PATH',     STORAGE_PATH . '/logs');
define('CACHE_PATH',   STORAGE_PATH . '/cache');

define('ADMIN_PREFIX', 'admin');

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}