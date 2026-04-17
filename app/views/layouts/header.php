<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= sanitize($pageTitle ?? 'Luminosity Noodles') ?> — Luminosity Noodles</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body>

<nav class="navbar" id="navbar">
  <a href="<?= base_url() ?>" class="nav-brand">
    <span class="brand-glow">✦</span> Luminosity<span class="brand-accent"> Noodles</span>
  </a>
  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
  <ul class="nav-links">
    <li><a href="<?= base_url() ?>">Home</a></li>
    <li><a href="<?= base_url('menu') ?>">Menu</a></li>
    <?php if (isLoggedIn()): ?>
      <li><a href="<?= base_url('cart') ?>" class="cart-link">
        🛒 Cart <span class="cart-badge" id="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
      </a></li>
      <li><a href="<?= base_url('orders') ?>">Pesanan</a></li>
      <?php if (isAdmin()): ?>
        <li><a href="<?= base_url('admin') ?>" class="btn-neon">Admin</a></li>
      <?php endif; ?>
      <li><a href="<?= base_url('logout') ?>">Logout</a></li>
    <?php else: ?>
      <li><a href="<?= base_url('login') ?>">Login</a></li>
      <li><a href="<?= base_url('register') ?>" class="btn-neon">Register</a></li>
    <?php endif; ?>
  </ul>
</nav>

<main class="main-content">
  