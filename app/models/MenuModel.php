<?php
// app/models/MenuModel.php

require_once ROOT_PATH . '/app/models/BaseModel.php';

class MenuModel extends BaseModel {
    protected string $table = 'menus';

    public function getAllWithCategory(): array {
        return $this->db->fetchAll("
            SELECT m.*, c.name AS category_name, c.slug AS category_slug
            FROM menus m
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE m.is_available = 1
            ORDER BY c.id, m.name
        ");
    }

    public function getByCategory(string $slug): array {
        return $this->db->fetchAll("
            SELECT m.*, c.name AS category_name
            FROM menus m
            JOIN categories c ON m.category_id = c.id
            WHERE c.slug = ? AND m.is_available = 1
        ", "s", [$slug]);
    }

    public function getCategories(): array {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY id");
    }

    public function getToppings(): array {
        return $this->db->fetchAll("SELECT * FROM toppings ORDER BY name");
    }

    public function createMenu(array $data): int {
        return $this->db->insert(
            "INSERT INTO menus (category_id, name, description, price, image, has_spicy, has_topping, is_available)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            "issdiiii",
            [
                $data['category_id'], $data['name'], $data['description'],
                $data['price'], $data['image'], $data['has_spicy'],
                $data['has_topping'], $data['is_available']
            ]
        );
    }

    public function updateMenu(int $id, array $data): bool {
        return $this->db->execute(
            "UPDATE menus SET category_id=?, name=?, description=?, price=?, image=?, has_spicy=?, has_topping=?, is_available=? WHERE id=?",
            "issdiiiii",
            [
                $data['category_id'], $data['name'], $data['description'],
                $data['price'], $data['image'], $data['has_spicy'],
                $data['has_topping'], $data['is_available'], $id
            ]
        );
    }

    public function getAdminAll(): array {
        return $this->db->fetchAll("
            SELECT m.*, c.name AS category_name
            FROM menus m LEFT JOIN categories c ON m.category_id = c.id
            ORDER BY m.id DESC
        ");
    }
}
