<?php
// app/models/OrderModel.php

require_once ROOT_PATH . '/app/models/BaseModel.php';

class OrderModel extends BaseModel {
    protected string $table = 'orders';

    public function createOrder(array $data): int {
        return $this->db->insert(
            "INSERT INTO orders (user_id, promo_id, subtotal, discount, total, notes, payment_method)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            "iiddds s",
            [
                $data['user_id'], $data['promo_id'] ?? null,
                $data['subtotal'], $data['discount'],
                $data['total'], $data['notes'] ?? '',
                $data['payment_method'] ?? 'cash'
            ]
        );
    }

    public function createOrderFull(int $userId, ?int $promoId, float $subtotal, float $discount, float $total, string $notes, string $payment): int {
        return $this->db->insert(
            "INSERT INTO orders (user_id, promo_id, subtotal, discount, total, notes, payment_method)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            "iiddds s",
            [$userId, $promoId, $subtotal, $discount, $total, $notes, $payment]
        );
    }

    public function addOrderItem(int $orderId, int $menuId, int $qty, float $unitPrice, string $spicy, string $notes): int {
        return $this->db->insert(
            "INSERT INTO order_items (order_id, menu_id, qty, unit_price, spicy_level, notes)
             VALUES (?, ?, ?, ?, ?, ?)",
            "iiidss",
            [$orderId, $menuId, $qty, $unitPrice, $spicy, $notes]
        );
    }

    public function addItemTopping(int $itemId, int $toppingId): void {
        $this->db->execute(
            "INSERT INTO order_item_toppings (order_item_id, topping_id) VALUES (?, ?)",
            "ii", [$itemId, $toppingId]
        );
    }

    public function getUserOrders(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            "i", [$userId]
        );
    }

    public function getOrderDetail(int $orderId, int $userId = 0): ?array {
        $sql = "SELECT o.*, u.name AS user_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
        $params = [$orderId];
        $types = "i";
        if ($userId > 0) {
            $sql .= " AND o.user_id = ?";
            $params[] = $userId;
            $types .= "i";
        }
        return $this->db->fetchOne($sql, $types, $params);
    }

    public function getOrderItems(int $orderId): array {
        return $this->db->fetchAll(
            "SELECT oi.*, m.name AS menu_name, m.image
             FROM order_items oi JOIN menus m ON oi.menu_id = m.id
             WHERE oi.order_id = ?",
            "i", [$orderId]
        );
    }

    public function getAllOrders(string $status = ''): array {
        $sql = "SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id";
        if ($status) {
            return $this->db->fetchAll($sql . " WHERE o.status = ? ORDER BY o.created_at DESC", "s", [$status]);
        }
        return $this->db->fetchAll($sql . " ORDER BY o.created_at DESC");
    }

    public function updateStatus(int $id, string $status): bool {
        return $this->db->execute(
            "UPDATE orders SET status = ? WHERE id = ?",
            "si", [$status, $id]
        );
    }

    public function getRecentOrders(int $limit = 10): array {
        return $this->db->fetchAll(
            "SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC LIMIT ?",
            "i", [$limit]
        );
    }

    public function getSalesStats(): array {
        $today = $this->db->fetchOne(
            "SELECT COUNT(*) AS count, COALESCE(SUM(total),0) AS revenue
             FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'"
        );
        $monthly = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
                    COUNT(*) AS count, SUM(total) AS revenue
             FROM orders WHERE status != 'cancelled'
             GROUP BY month ORDER BY month DESC LIMIT 6"
        );
        $topMenu = $this->db->fetchAll(
            "SELECT m.name, SUM(oi.qty) AS total_sold
             FROM order_items oi JOIN menus m ON oi.menu_id = m.id
             GROUP BY m.id ORDER BY total_sold DESC LIMIT 5"
        );
        return compact('today', 'monthly', 'topMenu');
    }

    public function getPromoByCode(string $code): ?array {
        return $this->db->fetchOne(
            "SELECT * FROM promos WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())",
            "s", [$code]
        );
    }
}
