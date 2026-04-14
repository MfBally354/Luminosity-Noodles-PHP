<?php $pageTitle = 'Dashboard'; $activePage = 'dashboard'; require ROOT_PATH . '/app/views/layouts/admin_header.php'; ?>

<h2 class="admin-page-title">📊 Dashboard</h2>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">📦</div>
    <div class="stat-info">
      <h3><?= $stats['today']['count'] ?? 0 ?></h3>
      <p>Pesanan Hari Ini</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">💰</div>
    <div class="stat-info">
      <h3><?= formatPrice($stats['today']['revenue'] ?? 0) ?></h3>
      <p>Revenue Hari Ini</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🍜</div>
    <div class="stat-info">
      <h3><?= $stats['topMenu'][0]['name'] ?? '-' ?></h3>
      <p>Menu Terlaris</p>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="admin-grid-2">
  <div class="admin-card">
    <h3>📈 Revenue 6 Bulan Terakhir</h3>
    <canvas id="revenueChart" height="200"></canvas>
  </div>
  <div class="admin-card">
    <h3>🏆 Top Menu</h3>
    <canvas id="topMenuChart" height="200"></canvas>
  </div>
</div>

<!-- Recent Orders -->
<div class="admin-card mt-2">
  <div class="admin-card-header">
    <h3>🧾 Pesanan Terbaru</h3>
    <a href="<?= base_url('admin/orders') ?>" class="btn-neon-sm">Lihat Semua</a>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr>
      </thead>
      <tbody id="orders-tbody">
        <?php foreach ($recent as $o): ?>
          <?php
            $sc = ['pending'=>'#f59e0b','processing'=>'#3b82f6','cooking'=>'#f97316','delivering'=>'#a78bfa','done'=>'#22c55e','cancelled'=>'#ef4444'];
            $sl = ['pending'=>'Menunggu','processing'=>'Diproses','cooking'=>'Dimasak','delivering'=>'Diantar','done'=>'Selesai','cancelled'=>'Batal'];
          ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= sanitize($o['user_name']) ?></td>
            <td><?= formatPrice($o['total']) ?></td>
            <td><span class="status-badge" style="color:<?= $sc[$o['status']]??'#aaa' ?>;border-color:<?= $sc[$o['status']]??'#aaa' ?>"><?= $sl[$o['status']]??$o['status'] ?></span></td>
            <td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <select class="status-select" data-id="<?= $o['id'] ?>" onchange="updateStatus(this)">
                <?php foreach (['pending','processing','cooking','delivering','done','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const monthlyData = <?= json_encode(array_reverse($stats['monthly'])) ?>;
const topMenuData = <?= json_encode($stats['topMenu']) ?>;

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: monthlyData.map(d => d.month),
    datasets: [{
      label: 'Revenue',
      data: monthlyData.map(d => d.revenue),
      borderColor: '#a855f7',
      backgroundColor: 'rgba(168,85,247,0.1)',
      tension: 0.4, fill: true,
      pointBackgroundColor: '#a855f7'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { labels: { color: '#e2e8f0' } } },
    scales: {
      x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
      y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
    }
  }
});

// Top Menu Chart
new Chart(document.getElementById('topMenuChart'), {
  type: 'doughnut',
  data: {
    labels: topMenuData.map(d => d.name),
    datasets: [{
      data: topMenuData.map(d => d.total_sold),
      backgroundColor: ['#a855f7','#3b82f6','#22c55e','#f59e0b','#ef4444']
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { labels: { color: '#e2e8f0' } } }
  }
});
</script>

<?php require ROOT_PATH . '/app/views/layouts/admin_footer.php'; ?>
