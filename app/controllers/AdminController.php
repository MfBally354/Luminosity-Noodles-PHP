<?php
// app/controllers/AdminController.php

require_once ROOT_PATH . '/app/models/MenuModel.php';
require_once ROOT_PATH . '/app/models/OrderModel.php';

class AdminController {
    private MenuModel  $menuModel;
    private OrderModel $orderModel;

    public function __construct() {
        $this->menuModel  = new MenuModel();
        $this->orderModel = new OrderModel();
    }

    public function dashboard(): void {
        requireAdmin();
        $stats   = $this->orderModel->getSalesStats();
        $recent  = $this->orderModel->getRecentOrders(8);
        require ROOT_PATH . '/app/views/admin/dashboard.php';
    }

    public function menuList(): void {
        requireAdmin();
        $menus = $this->menuModel->getAdminAll();
        require ROOT_PATH . '/app/views/admin/menu_list.php';
    }

    public function menuForm(?string $id = null): void {
        requireAdmin();
        $menu = $id ? $this->menuModel->findById((int)$id) : null;
        $categories = $this->menuModel->getCategories();
        $csrf = generateCSRF();
        require ROOT_PATH . '/app/views/admin/menu_form.php';
    }

    public function saveMenu(): void {
        requireAdmin();
        if (!verifyCSRF($_POST[CSRF_TOKEN_NAME] ?? '')) redirect('admin/menu');

        $id   = (int)($_POST['id'] ?? 0);
        $data = [
            'category_id'  => (int)$_POST['category_id'],
            'name'         => sanitize($_POST['name']),
            'description'  => sanitize($_POST['description'] ?? ''),
            'price'        => (float)$_POST['price'],
            'has_spicy'    => isset($_POST['has_spicy']) ? 1 : 0,
            'has_topping'  => isset($_POST['has_topping']) ? 1 : 0,
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
            'image'        => 'default.jpg',
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ALLOWED_EXTENSIONS) && $_FILES['image']['size'] <= MAX_FILE_SIZE) {
                $filename = uniqid('menu_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $filename);
                $data['image'] = $filename;
            }
        } elseif ($id) {
            $existing = $this->menuModel->findById($id);
            $data['image'] = $existing['image'] ?? 'default.jpg';
        }

        if ($id) {
            $this->menuModel->updateMenu($id, $data);
        } else {
            $this->menuModel->createMenu($data);
        }

        redirect('admin/menu');
    }

    public function deleteMenu(?string $id): void {
        requireAdmin();
        if ($id) $this->menuModel->delete((int)$id);
        redirect('admin/menu');
    }

    public function orders(): void {
        requireAdmin();
        $status = $_GET['status'] ?? '';
        $orders = $this->orderModel->getAllOrders($status);
        require ROOT_PATH . '/app/views/admin/orders.php';
    }

    public function promoList(): void {
        requireAdmin();
        $db = Database::getInstance();
        $promos = $db->fetchAll("SELECT * FROM promos ORDER BY id DESC");
        $csrf = generateCSRF();
        require ROOT_PATH . '/app/views/admin/promo.php';
    }

    public function savePromo(): void {
        requireAdmin();
        if (!verifyCSRF($_POST[CSRF_TOKEN_NAME] ?? '')) redirect('admin/promo');
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO promos (code, discount_percent, min_order, expires_at) VALUES (?,?,?,?)",
            "siis",
            [
                strtoupper(sanitize($_POST['code'])),
                (int)$_POST['discount_percent'],
                (float)$_POST['min_order'],
                $_POST['expires_at'] ?: null
            ]
        );
        redirect('admin/promo');
    }
}
