</main>

<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="brand-glow">✦</span> Luminosity<span class="brand-accent"> Noodles</span>
      <p>Cosmic flavors from another dimension.</p>
    </div>
    <div class="footer-links">
      <a href="<?= base_url() ?>">Home</a>
      <a href="<?= base_url('menu') ?>">Menu</a>
      <a href="<?= base_url('orders') ?>">Pesanan</a>
    </div>
    <p class="footer-copy">&copy; <?= date('Y') ?> Luminosity Noodles. All rights reserved.</p>
  </div>
</footer>

<script>
  const CSRF_TOKEN = '<?= generateCSRF() ?>';
  const BASE_URL   = '<?= base_url() ?>';
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
