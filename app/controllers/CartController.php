<?php
// app/controllers/CartController.php

class CartController {
    public function index(): void {
        requireLogin();
        $cart = $_SESSION['cart'] ?? [];
        require ROOT_PATH . '/app/views/cart/index.php';
    }
}
