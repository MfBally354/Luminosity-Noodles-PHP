<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= sanitize($pageTitle ?? 'Admin') ?> — Luminosity Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <span class="brand-glow">✦</span> LN<span class="brand-accent"> Admin</span>
    </div>
    <nav class="sidebar-nav">
      <a href="<?= base_url('admin') ?>"        class="<?= ($activePage??'')==='dashboard'?'active':'' ?>">📊 Dashboard</a>
      <a href="<?= base_url('admin/orders') ?>"  class="<?= ($activePage??'')==='orders'?'active':'' ?>">🧾 Pesanan</a>
      <a href="<?= base_url('admin/menu') ?>"    class="<?= ($activePage??'')==='menu'?'active':'' ?>">🍜 Menu</a>
      <a href="<?= base_url('admin/promo') ?>"   class="<?= ($activePage??'')==='promo'?'active':'' ?>">🎯 Promo</a>
      <a href="<?= base_url() ?>" target="_blank">🌐 Lihat Website</a>
      <a href="<?= base_url('logout') ?>" class="sidebar-logout">🚀 Logout</a>
    </nav>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar">
      <span class="admin-greeting">Halo, <strong><?= sanitize($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
      <span class="admin-time" id="admin-clock"></span>
    </header>
    <div class="admin-content">
