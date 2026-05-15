<?php
// app/core/Database.php

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT
                 . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('DB Connection failed: ' . $e->getMessage());
                die('Database connection error. Please try again later.');
            }
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): array|false {
        return self::query($sql, $params)->fetch();
    }

    public static function insert(string $sql, array $params = []): string {
        self::query($sql, $params);
        return self::getInstance()->lastInsertId();
    }

    public static function execute(string $sql, array $params = []): int {
        return self::query($sql, $params)->rowCount();
    }

    /**
     * Paginate a SELECT query.
     * Strips ORDER BY before wrapping in COUNT — MySQL rejects ORDER BY in derived tables.
     */
    public static function paginate(string $sql, array $params, int $page, int $perPage = 20): array {
        // Remove ORDER BY before COUNT wrap
        $countBase = preg_replace('/\s+ORDER\s+BY\s+[\s\S]+$/i', '', trim($sql));
        $countSql  = 'SELECT COUNT(*) AS total FROM (' . $countBase . ') AS _pag_sub';

        $total = 0;
        try {
            $row   = self::fetchOne($countSql, $params);
            $total = ($row && isset($row['total'])) ? (int)$row['total'] : 0;
        } catch (Exception $e) {
            Logger::error('Paginate count error: ' . $e->getMessage());
        }

        $page     = max(1, (int)$page);
        $perPage  = max(1, (int)$perPage);
        $lastPage = $total > 0 ? (int)ceil($total / $perPage) : 1;
        $offset   = ($page - 1) * $perPage;

        $data = self::fetchAll($sql . ' LIMIT ' . $perPage . ' OFFSET ' . $offset, $params);

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $lastPage,
        ];
    }
}
