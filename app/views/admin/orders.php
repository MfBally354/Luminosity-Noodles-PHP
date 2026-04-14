<?php $pageTitle = 'Kelola Pesanan'; $activePage = 'orders'; require ROOT_PATH . '/app/views/layouts/admin_header.php'; ?>

<div class="admin-card-header">
  <h2 class="admin-page-title">🧾 Kelola Pesanan</h2>
  <div class="filter-tabs">
    <?php foreach ([''=>'Semua','pending'=>'Pending','cooking'=>'Masak','delivering'=>'Kirim','done'=>'Selesai','cancelled'=>'Batal'] as $val=>$lbl): ?>
      <a href="<?= base_url('admin/orders') . ($val ? '?status='.$val : '') ?>"
         class="tab-btn <?= ($_GET['status']??'')===$val?'active':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>
  </div>
</div>

<div class="admin-card mt-2">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Pelanggan</th><th>Total</th><th>Bayar</th><th>Status</th><th>Waktu</th><th>Update Status</th></tr>
      </thead>
      <tbody>
        <?php
          $sc = ['pending'=>'#f59e0b','processing'=>'#3b82f6','cooking'=>'#f97316','delivering'=>'#a78bfa','done'=>'#22c55e','cancelled'=>'#ef4444'];
          $sl = ['pending'=>'Menunggu','processing'=>'Diproses','cooking'=>'Dimasak','delivering'=>'Diantar','done'=>'Selesai','cancelled'=>'Batal'];
        ?>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= sanitize($o['user_name']) ?></td>
            <td><?= formatPrice($o['total']) ?></td>
            <td><?= sanitize(ucfirst($o['payment_method'])) ?></td>
            <td>
              <span class="status-badge" style="color:<?= $sc[$o['status']]??'#aaa' ?>;border-color:<?= $sc[$o['status']]??'#aaa' ?>">
                <?= $sl[$o['status']]??$o['status'] ?>
              </span>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <select class="status-select" data-id="<?= $o['id'] ?>" onchange="updateStatus(this)">
                <?php foreach (array_keys($sl) as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= $sl[$s] ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
          <tr><td colspan="7" style="text-align:center;opacity:.5">Tidak ada pesanan</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require ROOT_PATH . '/app/views/layouts/admin_footer.php'; ?>
