<?php $pageTitle = $menu ? 'Edit Menu' : 'Tambah Menu'; $activePage = 'menu'; require ROOT_PATH . '/app/views/layouts/admin_header.php'; ?>

<div class="admin-card-header">
  <h2 class="admin-page-title"><?= $menu ? '✏️ Edit Menu' : '➕ Tambah Menu' ?></h2>
  <a href="<?= base_url('admin/menu') ?>" class="btn-ghost">← Kembali</a>
</div>

<div class="admin-card mt-2" style="max-width:600px">
  <form method="POST" action="<?= base_url('admin/menu') ?>" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf ?>">
    <?php if ($menu): ?>
      <input type="hidden" name="id" value="<?= $menu['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Nama Menu *</label>
      <input type="text" name="name" required value="<?= sanitize($menu['name'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Kategori *</label>
      <select name="category_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($menu['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= sanitize($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" rows="3"><?= sanitize($menu['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label>Harga (Rp) *</label>
      <input type="number" name="price" required min="0" step="500"
             value="<?= $menu['price'] ?? '' ?>">
    </div>

    <div class="form-group">
      <label>Gambar Menu</label>
      <?php if ($menu && $menu['image'] && $menu['image'] !== 'default.jpg'): ?>
        <div style="margin-bottom:.5rem">
          <img src="<?= base_url('uploads/menu/' . sanitize($menu['image'])) ?>"
               style="height:80px;border-radius:8px">
        </div>
      <?php endif; ?>
      <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
      <small>Max 2MB. JPG/PNG/WebP</small>
    </div>

    <div class="form-group checkbox-group">
      <label class="checkbox-item">
        <input type="checkbox" name="has_spicy" value="1" <?= ($menu['has_spicy'] ?? 1) ? 'checked' : '' ?>>
        🔥 Ada pilihan level pedas
      </label>
      <label class="checkbox-item">
        <input type="checkbox" name="has_topping" value="1" <?= ($menu['has_topping'] ?? 1) ? 'checked' : '' ?>>
        🍥 Ada pilihan topping
      </label>
      <label class="checkbox-item">
        <input type="checkbox" name="is_available" value="1" <?= ($menu['is_available'] ?? 1) ? 'checked' : '' ?>>
        ✅ Tersedia / Aktif
      </label>
    </div>

    <button type="submit" class="btn-neon btn-block">
      <?= $menu ? '💾 Simpan Perubahan' : '➕ Tambah Menu' ?>
    </button>
  </form>
</div>

<?php require ROOT_PATH . '/app/views/layouts/admin_footer.php'; ?>
