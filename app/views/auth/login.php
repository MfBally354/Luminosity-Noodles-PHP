<?php $pageTitle = 'Login'; require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-icon">🚀</div>
      <h1>Welcome Back</h1>
      <p>Login ke akun Luminosity kamu</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('login') ?>" class="auth-form">
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf ?>">

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="kamu@email.com" autocomplete="email">
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-password">
          <input type="password" name="password" required placeholder="••••••••" id="pwd" autocomplete="current-password">
          <button type="button" onclick="togglePwd('pwd')">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-neon btn-block">Login ✦</button>
    </form>

    <p class="auth-alt">Belum punya akun? <a href="<?= base_url('register') ?>">Daftar sekarang</a></p>
  </div>
</div>

<script>
function togglePwd(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
