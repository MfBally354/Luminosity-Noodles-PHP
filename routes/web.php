<?php
// routes/web.php

require_once ROOT_PATH . '/app/controllers/AuthController.php';
require_once ROOT_PATH . '/app/controllers/MenuController.php';
require_once ROOT_PATH . '/app/controllers/CartController.php';
require_once ROOT_PATH . '/app/controllers/OrderController.php';
require_once ROOT_PATH . '/app/controllers/AdminController.php';
require_once ROOT_PATH . '/app/controllers/ApiController.php';

$url = $_GET['url'] ?? '';
$url = trim($url, '/');
$segments = explode('/', $url);

$controller = $segments[0] ?? '';
$action = $segments[1] ?? 'index';
$param = $segments[2] ?? null;

$method = $_SERVER['REQUEST_METHOD'];

// API routes
if ($controller === 'api') {
    $api = new ApiController();
    $resource = $action;     // e.g. "menu", "cart", "orders"
    $id = $param;

    match(true) {
        $resource === 'menu' && $method === 'GET'           => $api->getMenu(),
        $resource === 'cart' && $method === 'POST'          => $api->addToCart(),
        $resource === 'cart' && $method === 'DELETE'        => $api->removeFromCart(),
        $resource === 'cart' && $method === 'GET'           => $api->getCart(),
        $resource === 'checkout' && $method === 'POST'      => $api->checkout(),
        $resource === 'orders' && $method === 'GET'         => $api->getOrders(),
        $resource === 'orders' && $method === 'PUT' && $id  => $api->updateOrderStatus($id),
        $resource === 'promo' && $method === 'POST'         => $api->applyPromo(),
        $resource === 'stats' && $method === 'GET'          => $api->getStats(),
        $resource === 'poll-orders' && $method === 'GET'    => $api->pollOrders(),
        default => jsonResponse(['error' => 'API endpoint not found'], 404)
    };
    exit;
}

// Web routes
$auth  = new AuthController();
$menu  = new MenuController();
$cart  = new CartController();
$order = new OrderController();
$admin = new AdminController();

match($controller) {
    ''          => $menu->home(),
    'login'     => ($method === 'POST' ? $auth->login() : $auth->showLogin()),
    'register'  => ($method === 'POST' ? $auth->register() : $auth->showRegister()),
    'logout'    => $auth->logout(),
    'menu'      => $menu->index(),
    'cart'      => $cart->index(),
    'checkout'  => $order->checkout(),
    'orders'    => match($action) {
        'index'  => $order->index(),
        'detail' => $order->detail($param),
        default  => $order->index(),
    },
    'admin'     => match($action) {
        'index'     => $admin->dashboard(),
        'menu'      => ($method === 'POST' ? $admin->saveMenu() : $admin->menuList()),
        'menu-add'  => $admin->menuForm(),
        'menu-edit' => $admin->menuForm($param),
        'menu-delete' => $admin->deleteMenu($param),
        'orders'    => $admin->orders(),
        'promo'     => ($method === 'POST' ? $admin->savePromo() : $admin->promoList()),
        default     => $admin->dashboard(),
    },
    default     => $menu->notFound(),
};
