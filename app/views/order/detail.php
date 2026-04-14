<?php $pageTitle = 'Detail Pesanan #' . $order['id']; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-hero mini">
  <h1>📦 Detail <span class="glow-text">Pesanan #<?= $order['id'] ?></span></h1>
</div>

<section class="section">
  <div class="container" style="max-width:700px">
    <?php
      $statusColors = ['pending'=>'#f59e0b','processing'=>'#3b82f6','cooking'=>'#f97316','delivering'=>'#a78bfa','done'=>'#22c55e','cancelled'=>'#ef4444'];
      $statusLabels = ['pending'=>'⏳ Menunggu','processing'=>'📋 Diproses','cooking'=>'🍜 Dimasak','delivering'=>'🛵 Diantar','done'=>'✅ Selesai','cancelled'=>'❌ Dibatalkan'];
      $color = $statusColors[$order['status']] ?? '#aaa';
      $label = $statusLabels[$order['status']] ?? $order['status'];
    ?>

    <!-- Status Progress -->
    <div class="status-track">
      <?php $steps = ['pending','processing','cooking','delivering','done']; $active = array_search($order['status'], $steps); ?>
      <?php foreach ($steps as $i => $step): ?>
        <div class="track-step <?= $i <= $active ? 'done' : '' ?> <?= $i === $active ? 'current' : '' ?>">
          <div class="track-dot"></div>
          <span><?= $statusLabels[$step] ?? $step ?></span>
        </div>
        <?php if ($i < count($steps)-1): ?>
          <div class="track-line <?= $i < $active ? 'done' : '' ?>"></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <div class="detail-card">
      <div class="detail-meta">
        <div><label>Tanggal</label><span><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span></div>
        <div><label>Status</label><span style="color:<?= $color ?>"><?= $label ?></span></div>
        <div><label>Pembayaran</label><span><?= sanitize(ucfirst($order['payment_method'])) ?></span></div>
      </div>
      <?php if ($order['notes']): ?>
        <div class="detail-notes">📝 <?= sanitize($order['notes']) ?></div>
      <?php endif; ?>
    </div>

    <div class="detail-items">
      <h3>Item Pesanan</h3>
      <?php foreach ($items as $item): ?>
        <div class="detail-item">
          <img src="<?= base_url('uploads/menu/' . sanitize($item['image'])) ?>"
               onerror="this.src='<?= base_url('img/default-menu.jpg') ?>'" alt="">
          <div>
            <strong><?= sanitize($item['menu_name']) ?></strong>
            <small>
              🔥 <?= sanitize($item['spicy_level']) ?> · Qty: <?= $item['qty'] ?>
              <?php if ($item['notes']): ?> · 📝 <?= sanitize($item['notes']) ?><?php endif; ?>
            </small>
          </div>
          <strong><?= formatPrice($item['unit_price'] * $item['qty']) ?></strong>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="detail-total-box">
      <div class="detail-total-row"><span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
      <?php if ($order['discount'] > 0): ?>
        <div class="detail-total-row" style="color:var(--accent-green)">
          <span>Diskon</span><span>-<?= formatPrice($order['discount']) ?></span>
        </div>
      <?php endif; ?>
      <div class="detail-total-row total-row"><span>Total</span><strong><?= formatPrice($order['total']) ?></strong></div>
    </div>

    <div class="text-center mt-2">
      <a href="<?= base_url('orders') ?>" class="btn-ghost">← Kembali ke Pesanan</a>
    </div>
  </div>
</section>

<?php if (!in_array($order['status'], ['done','cancelled'])): ?>
<script>
// Poll order status every 10 seconds
const orderId = <?= $order['id'] ?>;
setInterval(() => {
  fetch(BASE_URL + '/api/orders')
    .then(r => r.json())
    .then(data => {
      const o = data.data?.find(x => x.id == orderId);
      if (o && o.status !== '<?= $order['status'] ?>') location.reload();
    });
}, 10000);
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
