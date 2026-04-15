<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'svg', 'webp', 'ico', 'gif', 'woff', 'woff2'];
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

if (in_array($ext, $staticExtensions)) {
    // Coba dengan prefix /public dulu
    $file = __DIR__ . '/public' . $uri;
    if (file_exists($file)) {
        return false;
    }
    
    // Coba tanpa prefix (jika URI sudah termasuk public)
    $file = __DIR__ . $uri;
    if (file_exists($file)) {
        return false;
    }
}

$_GET['url'] = ltrim(str_replace('/public', '', $uri), '/');
require __DIR__ . '/public/index.php';
