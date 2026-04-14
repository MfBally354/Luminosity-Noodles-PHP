    </div><!-- /.admin-content -->
  </div><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<script>
  const CSRF_TOKEN = '<?= generateCSRF() ?>';
  const BASE_URL   = '<?= base_url() ?>';
  // Live clock
  const cl = document.getElementById('admin-clock');
  if (cl) setInterval(() => { cl.textContent = new Date().toLocaleTimeString('id-ID'); }, 1000);
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
<script src="<?= base_url('js/admin.js') ?>"></script>
</body>
</html>
