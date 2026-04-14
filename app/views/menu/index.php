<?php $pageTitle = 'Menu'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-hero mini">
  <h1>🍜 Cosmic <span class="glow-text">Menu</span></h1>
  <p>Pilih makananmu, kustomisasi topping & level pedas</p>
</div>

<section class="section">
  <div class="container">
    <!-- Category Filter -->
    <div class="category-tabs">
      <button class="tab-btn active" data-cat="all">Semua</button>
      <?php foreach ($categories as $cat): ?>
        <button class="tab-btn" data-cat="<?= sanitize($cat['slug']) ?>">
          <?= sanitize($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Menu Grid -->
    <div class="menu-grid" id="menu-grid">
      <?php foreach ($menus as $item): ?>
        <div class="menu-card" 
             data-cat="<?= sanitize($item['category_slug'] ?? '') ?>"
             data-menu-id="<?= $item['id'] ?>">
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
                <button class="btn-neon-sm"
                  onclick="openOrderModal(<?= htmlspecialchars(json_encode([
                    'id'          => $item['id'],
                    'name'        => $item['name'],
                    'price'       => $item['price'],
                    'has_spicy'   => (bool)$item['has_spicy'],
                    'has_topping' => (bool)$item['has_topping'],
                  ]), ENT_QUOTES) ?>)">
                  + Tambah
                </button>
              <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn-neon-sm">Login</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Order Modal -->
<?php if (isLoggedIn()): ?>
<div class="modal-overlay" id="order-modal" style="display:none">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h2 class="modal-title" id="modal-menu-name"></h2>
    <p class="modal-price" id="modal-menu-price"></p>

    <!-- Spicy Level -->
    <div class="form-group" id="spicy-group">
      <label>🔥 Level Pedas</label>
      <div class="spicy-buttons">
        <?php foreach (['none'=>'Tidak Pedas','mild'=>'Mild','medium'=>'Medium','hot'=>'Hot','extra_hot'=>'Extra Hot'] as $val=>$lbl): ?>
          <button type="button" class="spicy-btn <?= $val==='none'?'active':'' ?>" data-spicy="<?= $val ?>">
            <?= $lbl ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Toppings -->
    <div class="form-group" id="topping-group">
      <label>🍥 Topping Tambahan</label>
      <div class="topping-grid">
        <?php foreach ($toppings as $top): ?>
          <label class="topping-item">
            <input type="checkbox" name="toppings[]" value="<?= $top['id'] ?>">
            <span><?= sanitize($top['name']) ?></span>
            <?php if ($top['extra_price'] > 0): ?>
              <small>+<?= formatPrice($top['extra_price']) ?></small>
            <?php endif; ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Notes -->
    <div class="form-group">
      <label>📝 Catatan Khusus</label>
      <textarea id="modal-notes" placeholder="Misalnya: tanpa bawang, ekstra saos..." rows="2"></textarea>
    </div>

    <!-- Qty -->
    <div class="form-group qty-row">
      <label>Jumlah</label>
      <div class="qty-control">
        <button type="button" onclick="changeQty(-1)">−</button>
        <span id="modal-qty">1</span>
        <button type="button" onclick="changeQty(1)">+</button>
      </div>
    </div>

    <p class="modal-total">Total: <strong id="modal-total-price"></strong></p>
    <button class="btn-neon btn-block" onclick="submitToCart()">🛒 Masukkan Cart</button>
  </div>
</div>
<?php endif; ?>

<script>
const TOPPINGS_DATA = <?= json_encode($toppings) ?>;

// Category filter
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cat = btn.dataset.cat;
    document.querySelectorAll('.menu-card').forEach(card => {
      card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
    });
  });
});
</script>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
