<?php $pageTitle = 'Home'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<section class="hero">
  <div class="hero-stars"></div>
  <div class="hero-content">
    <p class="hero-tagline">🚀 Experience Cosmic Flavors</p>
    <h1 class="hero-title">Luminosity<br><span class="glow-text">Noodles</span></h1>
    <p class="hero-sub">Ramen & Mie yang melampaui batas galaksi — dimasak dengan bahan premium, disajikan dengan gaya futuristik.</p>
    <div class="hero-actions">
      <a href="<?= base_url('menu') ?>" class="btn-neon btn-lg">Lihat Menu ✦</a>
      <?php if (!isLoggedIn()): ?>
        <a href="<?= base_url('register') ?>" class="btn-ghost btn-lg">Daftar Sekarang</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="hero-visual">
    <div class="planet planet-1"></div>
    <div class="planet planet-2"></div>
    <div class="orbit-ring"></div>
  </div>
</section>

<!-- Featured Menu -->
<section class="section featured-section">
  <div class="container">
    <h2 class="section-title">⚡ Menu <span class="glow-text">Unggulan</span></h2>
    <p class="section-sub">Cosmic creations yang wajib kamu coba</p>
    <div class="menu-grid">
      <?php foreach (array_slice($featured, 0, 6) as $item): ?>
        <div class="menu-card" data-menu-id="<?= $item['id'] ?>">
          <div class="menu-card-img">
            <img src="<?= base_url('uploads/menu/' . sanitize($item['image'])) ?>"
                 onerror="this.src='<?= base_url('img/default-menu.jpg') ?>'"
                 alt="<?= sanitize($item['name']) ?>" loading="lazy">
            <?php if ($item['has_spicy']): ?>
              <span class="badge-spicy">🔥 Spicy</span>
            <?php endif; ?>
          </div>
          <div class="menu-card-body">
            <span class="menu-category"><?= sanitize($item['category_name'] ?? '') ?></span>
            <h3><?= sanitize($item['name']) ?></h3>
            <p><?= sanitize($item['description']) ?></p>
            <div class="menu-card-footer">
              <span class="menu-price"><?= formatPrice($item['price']) ?></span>
              <?php if (isLoggedIn()): ?>
                <button class="btn-add-cart btn-neon-sm" 
                        onclick="quickAddCart(<?= $item['id'] ?>, '<?= sanitize($item['name']) ?>')">
                  + Cart
                </button>
              <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn-neon-sm">Login dulu</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= base_url('menu') ?>" class="btn-neon">Lihat Semua Menu →</a>
    </div>
  </div>
</section>

<!-- How it works -->
<section class="section how-section">
  <div class="container">
    <h2 class="section-title">Cara <span class="glow-text">Pesan</span></h2>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-icon">🔍</div>
        <h3>1. Pilih Menu</h3>
        <p>Browse cosmic menu kami, pilih topping & level pedas sesuai selera.</p>
      </div>
      <div class="step-card">
        <div class="step-icon">🛒</div>
        <h3>2. Masukkan Cart</h3>
        <p>Tambahkan ke keranjang, review pesanan kamu sebelum checkout.</p>
      </div>
      <div class="step-card">
        <div class="step-icon">💳</div>
        <h3>3. Checkout</h3>
        <p>Pilih metode bayar, pakai kode promo kalau ada, lalu konfirmasi.</p>
      </div>
      <div class="step-card">
        <div class="step-icon">🚀</div>
        <h3>4. Nikmati!</h3>
        <p>Pesanan diproses & dikirim. Track status real-time di halaman pesanan.</p>
      </div>
    </div>
  </div>
</section>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
