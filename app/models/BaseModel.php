<?php
// app/models/BaseModel.php

require_once ROOT_PATH . '/config/database.php';

abstract class BaseModel {
    protected Database $db;
    protected string $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", "i", [$id]);
    }

    public function findAll(string $where = '', array $params = [], string $types = ''): array {
        $sql = "SELECT * FROM {$this->table}" . ($where ? " WHERE $where" : '');
        return $this->db->fetchAll($sql, $types, $params);
    }

    public function delete(int $id): bool {
        return $this->db->execute("DELETE FROM {$this->table} WHERE id = ?", "i", [$id]);
    }
}
