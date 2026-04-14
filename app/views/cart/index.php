<?php $pageTitle = 'Keranjang'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-hero mini">
  <h1>🛒 Keranjang <span class="glow-text">Belanja</span></h1>
</div>

<section class="section">
  <div class="container">
    <?php if (empty($cart)): ?>
      <div class="empty-state">
        <div class="empty-icon">🌌</div>
        <h2>Keranjang Kosong</h2>
        <p>Belum ada item di keranjangmu. Mulai pilih menu cosmic!</p>
        <a href="<?= base_url('menu') ?>" class="btn-neon">Browse Menu</a>
      </div>
    <?php else: ?>
      <div class="cart-layout">
        <div class="cart-items" id="cart-items">
          <?php $subtotal = 0; ?>
          <?php foreach ($cart as $key => $item): ?>
            <?php $lineTotal = ($item['price'] + $item['topping_price']) * $item['qty']; $subtotal += $lineTotal; ?>
            <div class="cart-item" data-key="<?= htmlspecialchars($key, ENT_QUOTES) ?>">
              <img src="<?= base_url('uploads/menu/' . sanitize($item['image'])) ?>"
                   onerror="this.src='<?= base_url('img/default-menu.jpg') ?>'"
                   alt="<?= sanitize($item['name']) ?>">
              <div class="cart-item-info">
                <h3><?= sanitize($item['name']) ?></h3>
                <small>
                  🔥 <?= sanitize($item['spicy']) ?>
                  <?php if ($item['topping_price'] > 0): ?>
                    · Topping +<?= formatPrice($item['topping_price']) ?>
                  <?php endif; ?>
                  <?php if ($item['notes']): ?>
                    · 📝 <?= sanitize($item['notes']) ?>
                  <?php endif; ?>
                </small>
                <div class="cart-price"><?= formatPrice($item['price'] + $item['topping_price']) ?> × <?= $item['qty'] ?></div>
              </div>
              <div class="cart-item-actions">
                <strong><?= formatPrice($lineTotal) ?></strong>
                <button class="btn-remove" onclick="removeFromCart('<?= htmlspecialchars($key, ENT_QUOTES) ?>')">✕</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="cart-summary">
          <h3>Ringkasan Pesanan</h3>
          <div class="summary-row">
            <span>Subtotal</span>
            <strong id="summary-subtotal"><?= formatPrice($subtotal) ?></strong>
          </div>
          <div class="promo-row">
            <input type="text" id="promo-input" placeholder="Kode Promo" style="text-transform:uppercase">
            <button class="btn-neon-sm" onclick="applyPromo()">Pakai</button>
          </div>
          <div class="summary-row" id="discount-row" style="display:none;color:var(--accent-green)">
            <span>Diskon</span>
            <strong id="discount-val">-Rp 0</strong>
          </div>
          <div class="summary-row total-row">
            <span>Total</span>
            <strong id="summary-total"><?= formatPrice($subtotal) ?></strong>
          </div>
          <a href="<?= base_url('checkout') ?>" class="btn-neon btn-block mt-2">Checkout →</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
