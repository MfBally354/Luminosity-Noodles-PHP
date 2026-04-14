<?php
// app/controllers/OrderController.php

require_once ROOT_PATH . '/app/models/OrderModel.php';
require_once ROOT_PATH . '/app/models/MenuModel.php';

class OrderController {
    private OrderModel $orderModel;
    private MenuModel  $menuModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->menuModel  = new MenuModel();
    }

    public function checkout(): void {
        requireLogin();
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) redirect('menu');
        $csrf = generateCSRF();
        require ROOT_PATH . '/app/views/order/checkout.php';
    }

    public function index(): void {
        requireLogin();
        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);
        require ROOT_PATH . '/app/views/order/list.php';
    }

    public function detail(?string $id): void {
        requireLogin();
        if (!$id) redirect('orders');
        $order = $this->orderModel->getOrderDetail((int)$id, $_SESSION['user_id']);
        if (!$order) redirect('orders');
        $items = $this->orderModel->getOrderItems((int)$id);
        require ROOT_PATH . '/app/views/order/detail.php';
    }
}
