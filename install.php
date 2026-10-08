<?php
/**
 * Travel Oceans CMS - Installer
 * Run once: http://your-site/traveloceans/install.php
 */

require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/html; charset=utf-8');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Travel Oceans CMS - Installer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#0a4d8c;color:#fff;font-family:'Segoe UI',sans-serif;padding:30px 0;}.install-card{background:#fff;color:#333;border-radius:20px;padding:40px;box-shadow:0 20px 60px rgba(0,0,0,.3);max-width:780px;margin:0 auto;}h1{color:#0a4d8c;}.ok{color:#198754;}.err{color:#dc3545;}</style>
</head>
<body>
<div class="container">
<div class="install-card">
<h1>🌊 Travel Oceans CMS Installer</h1>
<p class="text-muted">This script will create the database, all tables and the default admin account.</p>

<?php
$messages = [];

try {
    // Connect to MySQL (without selecting a database)
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $messages[] = ['ok', 'Connected to MySQL server.'];

    // Read schema
    $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
    if (!$schema) throw new Exception('schema.sql missing');

    // Execute schema
    $pdo->exec($schema);
    $messages[] = ['ok', 'Database & tables created.'];

    // Recreate admin password hash for 'admin123'
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE admins SET password_hash = :h WHERE username = :u');
    $stmt->execute([':h' => $hash, ':u' => 'admin']);
    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash, full_name, role) VALUES (:u,:h,:f,:r)');
        $stmt->execute([':u' => 'admin', ':h' => $hash, ':f' => 'Administrator', ':r' => 'superadmin']);
    }
    $messages[] = ['ok', 'Default admin user created (username: <code>admin</code> · password: <code>admin123</code>).'];

    // Create uploads directory
    $uploads = __DIR__ . '/assets/uploads';
    if (!is_dir($uploads)) { mkdir($uploads, 0775, true); }
    $messages[] = ['ok', 'Uploads directory ready: <code>assets/uploads/</code>'];

} catch (Exception $e) {
    $messages[] = ['err', 'Error: ' . htmlspecialchars($e->getMessage())];
}

foreach ($messages as $m) {
    $cls = $m[0] === 'ok' ? 'ok' : 'err';
    $icon = $m[0] === 'ok' ? '✅' : '❌';
    echo "<div class='alert alert-" . ($m[0]==='ok'?'success':'danger') . "'>$icon " . $m[1] . "</div>";
}
?>

<hr>
<h4>Next steps</h4>
<ol>
  <li>Visit the <a href="index.php">Homepage</a> to see your website.</li>
  <li>Visit the <a href="admin/">Admin Panel</a> and login with <code>admin</code> / <code>admin123</code>.</li>
  <li><strong>Delete <code>install.php</code></strong> from your server for security.</li>
</ol>

<div class="alert alert-warning">
  <strong>Configuration:</strong> Edit <code>includes/config.php</code> if your MySQL credentials differ from <code>root</code> / empty password.
</div>

</div>
</div>
</body>
</html>
