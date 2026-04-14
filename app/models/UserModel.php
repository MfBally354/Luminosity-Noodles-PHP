<?php
// app/models/UserModel.php

require_once ROOT_PATH . '/app/models/BaseModel.php';

class UserModel extends BaseModel {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", "s", [$email]);
    }

    public function create(string $name, string $email, string $password, string $phone = ''): int {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        return $this->db->insert(
            "INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)",
            "ssss",
            [$name, $email, $hashed, $phone]
        );
    }

    public function verifyPassword(string $input, string $hash): bool {
        return password_verify($input, $hash);
    }

    public function updateProfile(int $id, string $name, string $phone, string $address): bool {
        return $this->db->execute(
            "UPDATE users SET name=?, phone=?, address=? WHERE id=?",
            "sssi", [$name, $phone, $address, $id]
        );
    }
}
