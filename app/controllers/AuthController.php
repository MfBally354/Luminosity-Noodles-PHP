<?php
// app/controllers/AuthController.php

require_once ROOT_PATH . '/app/models/UserModel.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        if (isLoggedIn()) redirect('');
        $csrf = generateCSRF();
        require ROOT_PATH . '/app/views/auth/login.php';
    }

    public function login(): void {
        if (!verifyCSRF($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Email dan password wajib diisi.';
            $csrf = generateCSRF();
            require ROOT_PATH . '/app/views/auth/login.php';
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $error = 'Email atau password salah.';
            $csrf = generateCSRF();
            require ROOT_PATH . '/app/views/auth/login.php';
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];

        redirect($user['role'] === 'admin' ? 'admin' : '');
    }

    public function showRegister(): void {
        if (isLoggedIn()) redirect('');
        $csrf = generateCSRF();
        require ROOT_PATH . '/app/views/auth/register.php';
    }

    public function register(): void {
        if (!verifyCSRF($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $name     = sanitize($_POST['name'] ?? '');
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $phone    = sanitize($_POST['phone'] ?? '');

        $errors = [];
        if (strlen($name) < 2) $errors[] = 'Nama minimal 2 karakter.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
        if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
        if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak cocok.';
        if ($this->userModel->findByEmail($email)) $errors[] = 'Email sudah terdaftar.';

        if ($errors) {
            $csrf = generateCSRF();
            require ROOT_PATH . '/app/views/auth/register.php';
            return;
        }

        $id = $this->userModel->create($name, $email, $password, $phone);
        session_regenerate_id(true);
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['role']      = 'user';

        redirect('');
    }

    public function logout(): void {
        session_destroy();
        redirect('login');
    }
}
