<?php
// app/controllers/ApiController.php

require_once ROOT_PATH . '/app/models/MenuModel.php';
require_once ROOT_PATH . '/app/models/OrderModel.php';

class ApiController {
    private MenuModel  $menuModel;
    private OrderModel $orderModel;

    public function __construct() {
        $this->menuModel  = new MenuModel();
        $this->orderModel = new OrderModel();
    }

    public function getMenu(): void {
        $menus = $this->menuModel->getAllWithCategory();
        jsonResponse(['data' => $menus]);
    }

    public function getCart(): void {
        jsonResponse(['cart' => $_SESSION['cart'] ?? [], 'count' => count($_SESSION['cart'] ?? [])]);
    }

    public function addToCart(): void {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!verifyCSRF($body['csrf'] ?? '')) jsonResponse(['error' => 'CSRF failed'], 403);

        $menuId   = (int)($body['menu_id'] ?? 0);
        $qty      = max(1, (int)($body['qty'] ?? 1));
        $spicy    = in_array($body['spicy'] ?? '', ['none','mild','medium','hot','extra_hot']) ? $body['spicy'] : 'none';
        $notes    = sanitize($body['notes'] ?? '');
        $toppings = array_map('intval', $body['toppings'] ?? []);

        $menu = $this->menuModel->findById($menuId);
        if (!$menu) jsonResponse(['error' => 'Menu not found'], 404);

        $cartKey = $menuId . '_' . $spicy . '_' . implode('-', $toppings);

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        // Calculate topping price
        $toppingPrice = 0;
        if ($toppings) {
            $rows = $this->menuModel->getToppings();
            foreach ($rows as $t) {
                if (in_array($t['id'], $toppings)) $toppingPrice += $t['extra_price'];
            }
        }

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'menu_id'       => $menuId,
                'name'          => $menu['name'],
                'image'         => $menu['image'],
                'price'         => $menu['price'],
                'topping_price' => $toppingPrice,
                'qty'           => $qty,
                'spicy'         => $spicy,
                'toppings'      => $toppings,
                'notes'         => $notes,
            ];
        }

        jsonResponse(['success' => true, 'count' => count($_SESSION['cart'])]);
    }

    public function removeFromCart(): void {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!verifyCSRF($body['csrf'] ?? '')) jsonResponse(['error' => 'CSRF failed'], 403);
        $key = $body['key'] ?? '';
        unset($_SESSION['cart'][$key]);
        jsonResponse(['success' => true, 'cart' => $_SESSION['cart'] ?? []]);
    }

    public function applyPromo(): void {
        $body = json_decode(file_get_contents('php://input'), true);
        $code = strtoupper(sanitize($body['code'] ?? ''));
        $promo = $this->orderModel->getPromoByCode($code);
        if (!$promo) jsonResponse(['error' => 'Kode promo tidak valid atau sudah expired'], 400);
        jsonResponse(['success' => true, 'promo' => $promo]);
    }

    public function checkout(): void {
        if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
        $body = json_decode(file_get_contents('php://input'), true);
        if (!verifyCSRF($body['csrf'] ?? '')) jsonResponse(['error' => 'CSRF failed'], 403);

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) jsonResponse(['error' => 'Cart kosong'], 400);

        $promoCode = strtoupper($body['promo_code'] ?? '');
        $notes     = sanitize($body['notes'] ?? '');
        $payment   = in_array($body['payment'] ?? '', ['cash','transfer']) ? $body['payment'] : 'cash';

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] + $item['topping_price']) * $item['qty'];
        }

        $discount = 0;
        $promoId  = null;
        if ($promoCode) {
            $promo = $this->orderModel->getPromoByCode($promoCode);
            if ($promo && $subtotal >= $promo['min_order']) {
                $discount = $subtotal * ($promo['discount_percent'] / 100);
                $promoId  = $promo['id'];
            }
        }
        $total = $subtotal - $discount;

        // Insert order
        $orderId = $this->orderModel->createOrderFull(
            $_SESSION['user_id'], $promoId, $subtotal, $discount, $total, $notes, $payment
        );

        // Insert items
        foreach ($cart as $item) {
            $itemId = $this->orderModel->addOrderItem(
                $orderId, $item['menu_id'], $item['qty'],
                $item['price'] + $item['topping_price'],
                $item['spicy'], $item['notes']
            );
            foreach ($item['toppings'] as $tid) {
                $this->orderModel->addItemTopping($itemId, $tid);
            }
        }

        unset($_SESSION['cart']);
        jsonResponse(['success' => true, 'order_id' => $orderId]);
    }

    public function getOrders(): void {
        if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);
        jsonResponse(['data' => $orders]);
    }

    public function updateOrderStatus(string $id): void {
        if (!isAdmin()) jsonResponse(['error' => 'Forbidden'], 403);
        $body   = json_decode(file_get_contents('php://input'), true);
        $status = $body['status'] ?? '';
        $valid  = ['pending','processing','cooking','delivering','done','cancelled'];
        if (!in_array($status, $valid)) jsonResponse(['error' => 'Invalid status'], 400);
        $this->orderModel->updateStatus((int)$id, $status);
        jsonResponse(['success' => true]);
    }

    public function pollOrders(): void {
        if (!isAdmin()) jsonResponse(['error' => 'Forbidden'], 403);
        $orders = $this->orderModel->getRecentOrders(20);
        jsonResponse(['data' => $orders]);
    }

    public function getStats(): void {
        if (!isAdmin()) jsonResponse(['error' => 'Forbidden'], 403);
        jsonResponse($this->orderModel->getSalesStats());
    }
}
