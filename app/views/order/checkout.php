<?php $pageTitle = 'Checkout'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-hero mini">
  <h1>💳 <span class="glow-text">Checkout</span></h1>
</div>

<section class="section">
  <div class="container">
    <div class="checkout-layout">
      <!-- Order Summary -->
      <div class="checkout-summary">
        <h3>🛒 Pesanan Kamu</h3>
        <?php $subtotal = 0; ?>
        <?php foreach ($cart as $item): ?>
          <?php $line = ($item['price'] + $item['topping_price']) * $item['qty']; $subtotal += $line; ?>
          <div class="checkout-item">
            <span><?= sanitize($item['name']) ?> ×<?= $item['qty'] ?></span>
            <strong><?= formatPrice($line) ?></strong>
          </div>
        <?php endforeach; ?>
        <hr class="divider">
        <div class="checkout-item">
          <span>Subtotal</span>
          <strong id="co-subtotal"><?= formatPrice($subtotal) ?></strong>
        </div>
        <div class="checkout-item" id="co-discount-row" style="display:none; color:var(--accent-green)">
          <span>Diskon</span>
          <strong id="co-discount">-Rp 0</strong>
        </div>
        <div class="checkout-item total-row">
          <span>Total Bayar</span>
          <strong id="co-total"><?= formatPrice($subtotal) ?></strong>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="checkout-form-box">
        <h3>📋 Detail Pesanan</h3>

        <div class="form-group">
          <label>Kode Promo (opsional)</label>
          <div class="input-row">
            <input type="text" id="promo-code" placeholder="Masukkan kode promo" style="text-transform:uppercase">
            <button class="btn-neon-sm" onclick="applyPromoCheckout()">Pakai</button>
          </div>
          <small id="promo-msg"></small>
        </div>

        <div class="form-group">
          <label>💳 Metode Pembayaran</label>
          <div class="radio-group">
            <label class="radio-item">
              <input type="radio" name="payment" value="cash" checked> Bayar di Tempat (Cash)
            </label>
            <label class="radio-item">
              <input type="radio" name="payment" value="transfer"> Transfer Bank
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>📝 Catatan Tambahan</label>
          <textarea id="order-notes" rows="3" placeholder="Catatan untuk pesanan keseluruhan..."></textarea>
        </div>

        <button class="btn-neon btn-block btn-lg" onclick="submitCheckout()">
          🚀 Konfirmasi Pesanan
        </button>
      </div>
    </div>
  </div>
</section>

<div class="modal-overlay" id="success-modal" style="display:none">
  <div class="modal-box text-center">
    <div style="font-size:4rem;margin-bottom:1rem">🚀</div>
    <h2 class="glow-text">Pesanan Berhasil!</h2>
    <p>Pesananmu sedang kami proses. Cek status di halaman Pesananku.</p>
    <a href="<?= base_url('orders') ?>" class="btn-neon mt-2">Lihat Pesanan</a>
  </div>
</div>

<script>
const CO_SUBTOTAL = <?= $subtotal ?>;
const CSRF_TOKEN_VAL = '<?= $csrf ?>';
let appliedPromo = null;

function applyPromoCheckout() {
  const code = document.getElementById('promo-code').value.trim().toUpperCase();
  if (!code) return;
  fetch(BASE_URL + '/api/promo', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({code, csrf: CSRF_TOKEN})
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) {
      document.getElementById('promo-msg').textContent = '❌ ' + data.error;
      document.getElementById('promo-msg').style.color = 'var(--accent-pink)';
      appliedPromo = null;
      updateCoTotal(0);
    } else {
      appliedPromo = data.promo;
      const disc = CO_SUBTOTAL * (data.promo.discount_percent / 100);
      document.getElementById('promo-msg').textContent = `✅ Diskon ${data.promo.discount_percent}% berhasil!`;
      document.getElementById('promo-msg').style.color = 'var(--accent-green)';
      updateCoTotal(disc);
    }
  });
}

function updateCoTotal(discount) {
  const total = CO_SUBTOTAL - discount;
  const fmt = n => 'Rp ' + n.toLocaleString('id-ID');
  document.getElementById('co-discount-row').style.display = discount > 0 ? '' : 'none';
  document.getElementById('co-discount').textContent = '-' + fmt(discount);
  document.getElementById('co-total').textContent = fmt(total);
}

function submitCheckout() {
  const payment = document.querySelector('input[name="payment"]:checked')?.value || 'cash';
  const notes   = document.getElementById('order-notes').value;
  fetch(BASE_URL + '/api/checkout', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      csrf: CSRF_TOKEN,
      payment, notes,
      promo_code: appliedPromo?.code || ''
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('success-modal').style.display = 'flex';
    } else {
      alert('Error: ' + (data.error || 'Gagal checkout'));
    }
  });
}
</script>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
