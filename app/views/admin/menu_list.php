<?php $pageTitle = 'Kelola Menu'; $activePage = 'menu'; require ROOT_PATH . '/app/views/layouts/admin_header.php'; ?>

<div class="admin-card-header">
  <h2 class="admin-page-title">🍜 Kelola Menu</h2>
  <a href="<?= base_url('admin/menu-add') ?>" class="btn-neon">+ Tambah Menu</a>
</div>

<div class="admin-card mt-2">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($menus as $m): ?>
          <tr>
            <td>
              <img src="<?= base_url('uploads/menu/' . sanitize($m['image'])) ?>"
                   onerror="this.src='<?= base_url('img/default-menu.jpg') ?>'"
                   style="width:50px;height:50px;object-fit:cover;border-radius:8px">
            </td>
            <td><?= sanitize($m['name']) ?></td>
            <td><?= sanitize($m['category_name'] ?? '-') ?></td>
            <td><?= formatPrice($m['price']) ?></td>
            <td>
              <span class="status-badge" style="color:<?= $m['is_available']?'#22c55e':'#ef4444' ?>;border-color:<?= $m['is_available']?'#22c55e':'#ef4444' ?>">
                <?= $m['is_available'] ? '✅ Aktif' : '❌ Nonaktif' ?>
              </span>
            </td>
            <td>
              <a href="<?= base_url('admin/menu-edit/' . $m['id']) ?>" class="btn-neon-sm">Edit</a>
              <a href="<?= base_url('admin/menu-delete/' . $m['id']) ?>" class="btn-danger-sm"
                 onclick="return confirm('Hapus menu ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require ROOT_PATH . '/app/views/layouts/admin_footer.php'; ?>
