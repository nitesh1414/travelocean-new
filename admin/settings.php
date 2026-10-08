<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    foreach ($_POST as $key => $value) {
        if ($key === 'csrf_token' || $key === 'submit') continue;
        setting_save($key, $value);
    }
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Settings saved successfully.'];
    header('Location: settings.php'); exit;
}

// Password change
$pw_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['change_password'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    $cur = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $cfm = $_POST['confirm_password'] ?? '';
    $admin = current_admin();
    if (!password_verify($cur, $admin['password_hash'])) {
        $pw_msg = '<div class="alert alert-danger">Current password is incorrect.</div>';
    } elseif (strlen($new) < 6) {
        $pw_msg = '<div class="alert alert-danger">New password must be at least 6 characters.</div>';
    } elseif ($new !== $cfm) {
        $pw_msg = '<div class="alert alert-danger">Passwords do not match.</div>';
    } else {
        DB::update('admins', ['password_hash' => password_hash($new, PASSWORD_DEFAULT)], 'id = :id', [':id' => $admin['id']]);
        $pw_msg = '<div class="alert alert-success">Password changed successfully.</div>';
    }
}

ob_start();
?>
<div class="row g-3">
  <div class="col-lg-8">
    <div class="form-card">
      <h4 class="mb-3"><i class="bi bi-sliders me-2"></i> Site Settings</h4>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Site Name</label><input type="text" name="site_name" class="form-control" value="<?= e(setting('site_name')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Tagline</label><input type="text" name="site_tagline" class="form-control" value="<?= e(setting('site_tagline')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="site_email" class="form-control" value="<?= e(setting('site_email')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Website URL</label><input type="text" name="site_url" class="form-control" value="<?= e(setting('site_url')) ?>"></div>
          <div class="col-md-4"><label class="form-label">Phone 1</label><input type="text" name="site_phone_1" class="form-control" value="<?= e(setting('site_phone_1')) ?>"></div>
          <div class="col-md-4"><label class="form-label">Phone 2</label><input type="text" name="site_phone_2" class="form-control" value="<?= e(setting('site_phone_2')) ?>"></div>
          <div class="col-md-4"><label class="form-label">Phone 3</label><input type="text" name="site_phone_3" class="form-control" value="<?= e(setting('site_phone_3')) ?>"></div>
          <div class="col-md-8"><label class="form-label">Portugal Address</label><input type="text" name="site_address" class="form-control" value="<?= e(setting('site_address')) ?>"></div>
          <div class="col-md-4"><label class="form-label">WhatsApp (digits only)</label><input type="text" name="whatsapp" class="form-control" value="<?= e(setting('whatsapp')) ?>"></div>
          <div class="col-md-8"><label class="form-label">India Address</label><input type="text" name="site_address_1" class="form-control" value="<?= e(setting('site_address_1')) ?>"></div>
          <div class="col-md-8"><label class="form-label">Other Address</label><input type="text" name="site_address_2" class="form-control" value="<?= e(setting('site_address_2')) ?>"></div>
          
          <div class="col-md-6"><label class="form-label">Facebook</label><input type="text" name="facebook" class="form-control" value="<?= e(setting('facebook')) ?>"></div>
          <div class="col-md-6"><label class="form-label">Instagram</label><input type="text" name="instagram" class="form-control" value="<?= e(setting('instagram')) ?>"></div>
          <div class="col-md-6"><label class="form-label">TokTok</label><input type="text" name="linkedin" class="form-control" value="<?= e(setting('linkedin')) ?>"></div>
          <div class="col-md-6"><label class="form-label">YouTube</label><input type="text" name="youtube" class="form-control" value="<?= e(setting('youtube')) ?>"></div>
          <div class="col-12"><label class="form-label">About (short)</label><textarea name="about_short" rows="2" class="form-control"><?= e(setting('about_short')) ?></textarea></div>
          <div class="col-12"><label class="form-label">Footer About</label><textarea name="footer_about" rows="2" class="form-control"><?= e(setting('footer_about')) ?></textarea></div>
          <div class="col-12"><label class="form-label">Meta Description</label><textarea name="meta_description" rows="2" class="form-control"><?= e(setting('meta_description')) ?></textarea></div>
          <div class="col-12"><label class="form-label">Meta Keywords</label><input type="text" name="meta_keywords" class="form-control" value="<?= e(setting('meta_keywords')) ?>"></div>
          <div class="col-12"><label class="form-label">Important Notice (footer)</label><textarea name="important_notice" rows="4" class="form-control"><?= e(setting('important_notice')) ?></textarea></div>
          <div class="col-12"><button class="btn btn-primary" type="submit" name="submit" value="1"><i class="bi bi-check-circle me-1"></i> Save Settings</button></div>
        </div>
      </form>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-card">
      <h5 class="mb-3"><i class="bi bi-shield-lock me-2"></i> Change Password</h5>
      <?= $pw_msg ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="change_password" value="1">
        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required></div>
        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-key me-1"></i> Update Password</button>
      </form>
    </div>
    <div class="form-card mt-3">
      <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i> Quick Tips</h5>
      <ul class="small text-muted mb-0">
        <li>Hero slides appear in the homepage slider.</li>
        <li>Services with "Show on Home" appear on homepage.</li>
        <li>Study Programs auto-create their detail pages.</li>
        <li>Update the Important Notice whenever regulations change.</li>
      </ul>
    </div>
  </div>
</div>
<?php
$admin_content = ob_get_clean();
$admin_page_title = 'Site Settings';
require __DIR__ . '/includes/auth.php';
