<?php $pageTitle = 'Register'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-icon">✨</div>
      <h1>Bergabung Sekarang</h1>
      <p>Buat akun Luminosity Noodles kamu</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e): ?>
          <div>• <?= sanitize($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('register') ?>" class="auth-form">
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf ?>">

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" required placeholder="Nama kamu" 
               value="<?= sanitize($_POST['name'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="kamu@email.com"
               value="<?= sanitize($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>No. HP</label>
        <input type="tel" name="phone" placeholder="08xxxxxxxxxx"
               value="<?= sanitize($_POST['phone'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Password <small>(min 8 karakter)</small></label>
        <div class="input-password">
          <input type="password" name="password" required placeholder="••••••••" id="pwd">
          <button type="button" onclick="togglePwd('pwd')">👁</button>
        </div>
      </div>

      <div class="form-group">
        <label>Konfirmasi Password</label>
        <div class="input-password">
          <input type="password" name="confirm_password" required placeholder="••••••••" id="pwd2">
          <button type="button" onclick="togglePwd('pwd2')">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-neon btn-block">Daftar ✦</button>
    </form>

    <p class="auth-alt">Sudah punya akun? <a href="<?= base_url('login') ?>">Login di sini</a></p>
  </div>
</div>

<script>
function togglePwd(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
