<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$settings_error = '';
$pw_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!verify_csrf($csrfToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    if (isset($_POST['change_password'])) {
        $cur = isset($_POST['current_password']) && is_string($_POST['current_password']) ? $_POST['current_password'] : '';
        $new = isset($_POST['new_password']) && is_string($_POST['new_password']) ? $_POST['new_password'] : '';
        $cfm = isset($_POST['confirm_password']) && is_string($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $admin = current_admin();
        if (!$admin || !password_verify($cur, $admin['password_hash'])) {
            $pw_msg = '<div class="alert alert-danger">Current password is incorrect.</div>';
        } elseif (strlen($new) < 6) {
            $pw_msg = '<div class="alert alert-danger">New password must be at least 6 characters.</div>';
        } elseif ($new !== $cfm) {
            $pw_msg = '<div class="alert alert-danger">Passwords do not match.</div>';
        } else {
            DB::update('admins', ['password_hash' => password_hash($new, PASSWORD_DEFAULT)], 'id = :id', [':id' => $admin['id']]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Password changed successfully.'];
            header('Location: settings.php');
            exit;
        }
    } elseif (isset($_POST['save_settings'])) {
        $readText = static function ($key, $default = '') {
            $value = $_POST[$key] ?? $default;
            return is_scalar($value) ? trim((string)$value) : $default;
        };

        $notificationEmail = $readText('visa_assessment_notification_email', 'info@traveloceans.eu');
        $siteEmail = $readText('site_email');
        $fromEmail = $readText('smtp_from_email');
        $smtpPortInput = $readText('smtp_port', '587');
        $smtpPort = filter_var($smtpPortInput === '' ? '587' : $smtpPortInput, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]);
        $encryption = $readText('smtp_encryption', 'tls');
        $smtpUsername = $readText('smtp_username');
        $existingSmtpPassword = (string)setting('smtp_password');
        $newSmtpPassword = isset($_POST['smtp_password']) && is_string($_POST['smtp_password']) ? $_POST['smtp_password'] : '';
        $clearSmtpPassword = isset($_POST['clear_smtp_password']) && $_POST['clear_smtp_password'] === '1';

        if (!filter_var($notificationEmail, FILTER_VALIDATE_EMAIL)) {
            $settings_error = 'Enter a valid assessment notification email address.';
        } elseif ($siteEmail !== '' && !filter_var($siteEmail, FILTER_VALIDATE_EMAIL)) {
            $settings_error = 'Enter a valid site email address.';
        } elseif ($fromEmail !== '' && !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $settings_error = 'Enter a valid SMTP From email address, or leave it blank to use the site email.';
        } elseif ($smtpPort === false) {
            $settings_error = 'SMTP port must be a number from 1 to 65535.';
        } elseif (!in_array($encryption, ['tls', 'ssl', 'none'], true)) {
            $settings_error = 'Select a valid SMTP connection security option.';
        } elseif ($clearSmtpPassword && $smtpUsername !== '') {
            $settings_error = 'Clear the SMTP username as well, or keep the saved SMTP password.';
        } elseif ($smtpUsername !== '' && $existingSmtpPassword === '' && $newSmtpPassword === '') {
            $settings_error = 'Enter the SMTP password for the configured username.';
        }

        if ($settings_error === '') {
            $settingsToSave = [
                'site_name', 'site_tagline', 'site_email', 'site_url', 'site_phone_1', 'site_phone_2',
                'site_phone_3', 'site_address', 'whatsapp', 'site_address_1', 'site_address_2',
                'facebook', 'instagram', 'linkedin', 'youtube', 'about_short', 'footer_about',
                'meta_description', 'meta_keywords', 'important_notice',
            ];
            foreach ($settingsToSave as $key) setting_save($key, $readText($key));
            setting_save('visa_assessment_notification_email', $notificationEmail);
            setting_save('smtp_host', $readText('smtp_host'));
            setting_save('smtp_port', (string)$smtpPort);
            setting_save('smtp_username', $smtpUsername);
            setting_save('smtp_encryption', $encryption);
            setting_save('smtp_from_email', $fromEmail);
            setting_save('smtp_from_name', $readText('smtp_from_name'));

            if ($clearSmtpPassword) {
                setting_save('smtp_password', '');
            } elseif ($newSmtpPassword !== '') {
                setting_save('smtp_password', $newSmtpPassword);
            }

            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Settings saved successfully.'];
            header('Location: settings.php');
            exit;
        }
    } else {
        http_response_code(400);
        die('Invalid settings request.');
    }
}

$smtpPasswordConfigured = (string)setting('smtp_password') !== '';
$formSetting = static function ($key, $default = '') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings']) && isset($_POST[$key]) && is_scalar($_POST[$key])) {
        return (string)$_POST[$key];
    }
    return (string)setting($key, $default);
};
$smtpFromEmailValue = $formSetting('smtp_from_email');
if ($smtpFromEmailValue === '') $smtpFromEmailValue = $formSetting('site_email', 'info@traveloceans.eu');
$smtpPortValue = $formSetting('smtp_port', '587');
if ($smtpPortValue === '') $smtpPortValue = '587';
$smtpEncryptionValue = $formSetting('smtp_encryption', 'tls');

ob_start();
?>
<div class="row g-3">
  <div class="col-lg-8">
    <div class="form-card">
      <h4 class="mb-3"><i class="bi bi-sliders me-2"></i> Site Settings</h4>
      <?php if ($settings_error !== ''): ?><div class="alert alert-danger"><?= e($settings_error) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="save_settings" value="1">
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

          <div class="col-12"><hr class="my-2"><h5 class="mb-1"><i class="bi bi-envelope-paper me-2"></i>Visa Assessment Email Notifications</h5><p class="form-text mb-0">New assessments are emailed after they are saved. Configure SMTP below; if SMTP host is blank, the server's PHP mail transport is used.</p></div>
          <div class="col-md-6"><label class="form-label">Notification recipient</label><input type="email" name="visa_assessment_notification_email" class="form-control" value="<?= e($formSetting('visa_assessment_notification_email', 'info@traveloceans.eu')) ?>" required><div class="form-text">Defaults to info@traveloceans.eu.</div></div>
          <div class="col-md-6"><label class="form-label">SMTP Host</label><input type="text" name="smtp_host" class="form-control" value="<?= e($formSetting('smtp_host')) ?>" autocomplete="off" placeholder="smtp.example.com"></div>
          <div class="col-md-4"><label class="form-label">SMTP Port</label><input type="number" name="smtp_port" class="form-control" min="1" max="65535" value="<?= e($smtpPortValue) ?>"></div>
          <div class="col-md-4"><label class="form-label">Connection Security</label><select name="smtp_encryption" class="form-select"><option value="tls" <?= $smtpEncryptionValue === 'tls' ? 'selected' : '' ?>>STARTTLS (recommended)</option><option value="ssl" <?= $smtpEncryptionValue === 'ssl' ? 'selected' : '' ?>>SMTPS / SSL</option><option value="none" <?= $smtpEncryptionValue === 'none' ? 'selected' : '' ?>>None (not recommended)</option></select></div>
          <div class="col-md-4"><label class="form-label">SMTP From Email</label><input type="email" name="smtp_from_email" class="form-control" value="<?= e($smtpFromEmailValue) ?>" placeholder="Uses site email if blank"></div>
          <div class="col-md-6"><label class="form-label">SMTP Username</label><input type="text" name="smtp_username" class="form-control" value="<?= e($formSetting('smtp_username')) ?>" autocomplete="username"></div>
          <div class="col-md-6"><label class="form-label">SMTP Password</label><input type="password" name="smtp_password" class="form-control" value="" autocomplete="new-password" placeholder="<?= $smtpPasswordConfigured ? 'Password saved — leave blank to keep it' : 'Not configured' ?>"><div class="form-text">The saved password is never displayed. Leave blank to keep the existing password.</div></div>
          <div class="col-md-6"><label class="form-label">SMTP From Name</label><input type="text" name="smtp_from_name" class="form-control" value="<?= e($formSetting('smtp_from_name', setting('site_name', 'Travel Oceans'))) ?>"></div>
          <div class="col-md-6 d-flex align-items-end"><div class="form-check mb-2"><input type="checkbox" id="clear_smtp_password" name="clear_smtp_password" value="1" class="form-check-input" <?= $smtpPasswordConfigured && isset($_POST['clear_smtp_password']) ? 'checked' : '' ?>><label for="clear_smtp_password" class="form-check-label">Clear the saved SMTP password</label></div></div>
          <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save Settings</button></div>
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
