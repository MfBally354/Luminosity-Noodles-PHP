<?php
// config/app.php

define('APP_NAME', 'Luminosity Noodles');
define('APP_URL', 'http://localhost/luminosity-noodles/public');
define('APP_ENV', 'development'); // 'production' untuk live

// Session config
define('SESSION_NAME', 'luminosity_sess');
define('SESSION_LIFETIME', 3600 * 24); // 24 jam

// Upload
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/menu/');
define('UPLOAD_URL', APP_URL . '/uploads/menu/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// CSRF
define('CSRF_TOKEN_NAME', '_csrf_token');

function base_url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: " . base_url($url));
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('login');
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        redirect('');
    }
}

function formatPrice(float $price): string {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function generateCSRF(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCSRF(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
