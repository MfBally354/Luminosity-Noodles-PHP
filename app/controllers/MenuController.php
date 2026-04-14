<?php
// app/controllers/MenuController.php

require_once ROOT_PATH . '/app/models/MenuModel.php';

class MenuController {
    private MenuModel $menuModel;

    public function __construct() {
        $this->menuModel = new MenuModel();
    }

    public function home(): void {
        $featured = $this->menuModel->getAllWithCategory();
        $categories = $this->menuModel->getCategories();
        require ROOT_PATH . '/app/views/home.php';
    }

    public function index(): void {
        $menus = $this->menuModel->getAllWithCategory();
        $categories = $this->menuModel->getCategories();
        $toppings = $this->menuModel->getToppings();
        require ROOT_PATH . '/app/views/menu/index.php';
    }

    public function notFound(): void {
        http_response_code(404);
        require ROOT_PATH . '/app/views/404.php';
    }
}
