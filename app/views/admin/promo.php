<?php $pageTitle = 'Kelola Promo'; $activePage = 'promo'; require ROOT_PATH . '/app/views/layouts/admin_header.php'; ?>

<h2 class="admin-page-title">🎯 Kelola Promo</h2>

<div class="admin-grid-2 mt-2">
  <!-- Add Promo Form -->
  <div class="admin-card">
    <h3>+ Tambah Kode Promo</h3>
    <form method="POST" action="<?= base_url('admin/promo') ?>" class="admin-form">
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf ?>">
      <div class="form-group">
        <label>Kode Promo</label>
        <input type="text" name="code" required placeholder="COSMIC25" style="text-transform:uppercase">
      </div>
      <div class="form-group">
        <label>Diskon (%)</label>
        <input type="number" name="discount_percent" required min="1" max="100" placeholder="25">
      </div>
      <div class="form-group">
        <label>Minimum Order (Rp)</label>
        <input type="number" name="min_order" value="0" min="0" step="1000">
      </div>
      <div class="form-group">
        <label>Berlaku Sampai</label>
        <input type="date" name="expires_at">
      </div>
      <button type="submit" class="btn-neon btn-block">Tambah Promo</button>
    </form>
  </div>

  <!-- Promo List -->
  <div class="admin-card">
    <h3>📋 Daftar Promo</h3>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Kode</th><th>Diskon</th><th>Min. Order</th><th>Exp.</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($promos as $p): ?>
            <tr>
              <td><strong><?= sanitize($p['code']) ?></strong></td>
              <td><?= $p['discount_percent'] ?>%</td>
              <td><?= formatPrice($p['min_order']) ?></td>
              <td><?= $p['expires_at'] ?? '∞' ?></td>
              <td>
                <span class="status-badge" style="color:<?= $p['is_active']?'#22c55e':'#ef4444' ?>;border-color:<?= $p['is_active']?'#22c55e':'#ef4444' ?>">
                  <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/app/views/layouts/admin_footer.php'; ?>
