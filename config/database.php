<?php
// config/database.php

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'lumuser');       // Ganti sesuai MySQL user kamu
define('DB_PASS', 'LumPass123!');           // Ganti sesuai MySQL password kamu
define('DB_NAME', 'luminosity_noodles');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            error_log("DB Connection failed: " . $this->conn->connect_error);
            die(json_encode(['error' => 'Database connection failed']));
        }
        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConn(): mysqli {
        return $this->conn;
    }

    // Prepared query helper: query($sql, "si", [$str, $int])
    public function query(string $sql, string $types = '', array $params = []): mysqli_stmt|false {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error . " | SQL: $sql");
            return false;
        }
        if ($types && $params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }

    public function fetchAll(string $sql, string $types = '', array $params = []): array {
        $stmt = $this->query($sql, $types, $params);
        if (!$stmt) return [];
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchOne(string $sql, string $types = '', array $params = []): ?array {
        $stmt = $this->query($sql, $types, $params);
        if (!$stmt) return null;
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function insert(string $sql, string $types, array $params): int {
        $stmt = $this->query($sql, $types, $params);
        if (!$stmt) return 0;
        return $this->conn->insert_id;
    }

    public function execute(string $sql, string $types = '', array $params = []): bool {
        $stmt = $this->query($sql, $types, $params);
        return $stmt !== false;
    }
}
