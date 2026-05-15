<?php
// app/core/Autoloader.php

spl_autoload_register(function (string $class): void {
    $dirs = [
        // Core
        BASE_PATH . '/app/core/',

        // Admin module
        BASE_PATH . '/app/modules/Admin/Controllers/',
        BASE_PATH . '/app/modules/Admin/Models/',

        // User module
        BASE_PATH . '/app/modules/User/Controllers/',
        BASE_PATH . '/app/modules/User/Models/',

        // Profile module
        BASE_PATH . '/app/modules/Profile/Controllers/',
        BASE_PATH . '/app/modules/Profile/Models/',

        // Search module
        BASE_PATH . '/app/modules/Search/Controllers/',
        BASE_PATH . '/app/modules/Search/Models/',

        // Interest module
        BASE_PATH . '/app/modules/Interest/Controllers/',
        BASE_PATH . '/app/modules/Interest/Models/',

        // Chat module
        BASE_PATH . '/app/modules/Chat/Controllers/',
        BASE_PATH . '/app/modules/Chat/Models/',

        // Notification module
        BASE_PATH . '/app/modules/Notification/Controllers/',
        BASE_PATH . '/app/modules/Notification/Models/',

        // Subscription module
        BASE_PATH . '/app/modules/Subscription/Controllers/',
        BASE_PATH . '/app/modules/Subscription/Models/',
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});