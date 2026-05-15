<?php
// app/core/Logger.php

class Logger {
    public static function error(string $msg): void   { self::write('ERROR', $msg); }
    public static function info(string $msg): void    { self::write('INFO',  $msg); }
    public static function warning(string $msg): void { self::write('WARN',  $msg); }

    private static function write(string $level, string $msg): void {
        $file = LOG_PATH . '/app-' . date('Y-m-d') . '.log';
        $line = '[' . date('Y-m-d H:i:s') . '] [' . $level . '] ' . $msg . PHP_EOL;
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
