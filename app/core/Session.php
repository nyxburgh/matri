<?php
// app/core/Session.php

class Session {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
        self::enforceTimeout();
    }

    // Auto sign-out on inactivity
    private static function enforceTimeout(): void {
        $timeout = SESSION_TIMEOUT * 60;
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::destroy();
                return;
            }
        }
        $_SESSION['last_activity'] = time();
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // Flash messages
    public static function flash(string $key, string $message): void {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }

    // CSRF
    public static function generateCsrf(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(string $token): bool {
        return isset($_SESSION['_csrf_token'])
            && hash_equals($_SESSION['_csrf_token'], $token);
    }

    public static function regenerate(): void {
        session_regenerate_id(true);
    }
}
