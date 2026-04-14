<?php
// public/index.php — Front Controller

define('ROOT_PATH', dirname(__DIR__));

// Start session securely
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_name(SESSION_NAME ?? 'luminosity_sess');
session_start();

// Load configs
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';

// Load routes
require_once ROOT_PATH . '/routes/web.php';
