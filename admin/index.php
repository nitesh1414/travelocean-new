<?php
require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } else {
        $admin = DB::fetch('SELECT * FROM admins WHERE username = ?', [$username]);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            DB::update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $admin['id']]);
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid username or password.';
    }
}
if (is_admin()) { header('Location: dashboard.php'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login | <?= e(setting('site_name')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>
<body class="login-wrap">
  <div class="login-card">
    <div class="text-center mb-4">
      <div style="font-size:3rem;">🌊</div>
      <div class="brand-name"><?= e(setting('site_name')) ?></div>
      <div class="text-muted small">Admin Panel</div>
    </div>
    <?php if ($error): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i> <?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i> Sign In</button>
    </form>
    <div class="text-center mt-4 small text-muted">
      <i class="bi bi-info-circle me-1"></i> Default: <code>admin</code> / <code>admin123</code><br>
      <a href="<?= SITE_URL ?>/index.php" class="text-decoration-none mt-2 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Back to website</a>
    </div>
  </div>
</body>
</html>
