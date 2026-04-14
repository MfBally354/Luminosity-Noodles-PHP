<?php $pageTitle = 'Pesananku'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-hero mini">
  <h1>🧾 Pesanan <span class="glow-text">Saya</span></h1>
</div>

<section class="section">
  <div class="container">
    <?php if (empty($orders)): ?>
      <div class="empty-state">
        <div class="empty-icon">🌌</div>
        <h2>Belum Ada Pesanan</h2>
        <p>Kamu belum pernah memesan. Yuk coba menu cosmic kami!</p>
        <a href="<?= base_url('menu') ?>" class="btn-neon">Mulai Pesan</a>
      </div>
    <?php else: ?>
      <div class="orders-list">
        <?php foreach ($orders as $order): ?>
          <?php
            $statusColors = [
              'pending'    => '#f59e0b',
              'processing' => '#3b82f6',
              'cooking'    => '#f97316',
              'delivering' => '#a78bfa',
              'done'       => '#22c55e',
              'cancelled'  => '#ef4444',
            ];
            $statusLabels = [
              'pending'    => '⏳ Menunggu',
              'processing' => '📋 Diproses',
              'cooking'    => '🍜 Dimasak',
              'delivering' => '🛵 Diantar',
              'done'       => '✅ Selesai',
              'cancelled'  => '❌ Dibatalkan',
            ];
            $color = $statusColors[$order['status']] ?? '#aaa';
            $label = $statusLabels[$order['status']] ?? $order['status'];
          ?>
          <div class="order-card">
            <div class="order-card-header">
              <div>
                <strong>Order #<?= $order['id'] ?></strong>
                <small><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></small>
              </div>
              <span class="status-badge" style="border-color:<?= $color ?>;color:<?= $color ?>">
                <?= $label ?>
              </span>
            </div>
            <div class="order-card-body">
              <div class="order-meta">
                <span>💳 <?= sanitize(ucfirst($order['payment_method'])) ?></span>
                <?php if ($order['discount'] > 0): ?>
                  <span>🎯 Diskon <?= formatPrice($order['discount']) ?></span>
                <?php endif; ?>
              </div>
              <div class="order-total">
                Total: <strong><?= formatPrice($order['total']) ?></strong>
              </div>
            </div>
            <div class="order-card-footer">
              <a href="<?= base_url('orders/detail/' . $order['id']) ?>" class="btn-neon-sm">
                Lihat Detail →
              </a>
              <!-- Live status polling for active orders -->
              <?php if (!in_array($order['status'], ['done','cancelled'])): ?>
                <span class="live-dot">● Live</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
