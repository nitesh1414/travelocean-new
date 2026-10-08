<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();
$current_admin = current_admin();
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($admin_page_title ?? 'Admin') ?> | <?= e(setting('site_name')) ?> Admin</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="admin-wrap">
  <aside class="admin-sidebar">
    <div class="brand"><i class="bi bi-gear-fill"></i> Travel Oceans CMS</div>
    <ul class="nav flex-column">
      <li><a class="nav-link <?= $admin_page=='dashboard.php'?'active':'' ?>" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a class="nav-link <?= $admin_page=='slides.php'?'active':'' ?>" href="slides.php"><i class="bi bi-images"></i> Hero Slides</a></li>
      <li><a class="nav-link <?= $admin_page=='services.php'?'active':'' ?>" href="services.php"><i class="bi bi-briefcase"></i> Services</a></li>
      <li><a class="nav-link <?= $admin_page=='business.php'?'active':'' ?>" href="business.php"><i class="bi bi-building"></i> Business Services</a></li>
      <li><a class="nav-link <?= $admin_page=='programs.php'?'active':'' ?>" href="programs.php"><i class="bi bi-mortarboard"></i> Study Programs</a></li>
      <li><a class="nav-link <?= $admin_page=='testimonials.php'?'active':'' ?>" href="testimonials.php"><i class="bi bi-chat-quote"></i> Testimonials</a></li>
      <li><a class="nav-link <?= $admin_page=='why.php'?'active':'' ?>" href="why.php"><i class="bi bi-check-circle"></i> Why Choose Us</a></li>
      <li><a class="nav-link <?= $admin_page=='team.php'?'active':'' ?>" href="team.php"><i class="bi bi-people-fill"></i> Team Members</a></li>
      <li><a class="nav-link <?= $admin_page=='partners.php'?'active':'' ?>" href="partners.php"><i class="bi bi-building"></i> Partners</a></li>
      <li><a class="nav-link <?= $admin_page=='training.php'?'active':'' ?>" href="training.php"><i class="bi bi-mortarboard-fill"></i> Training Page</a></li>
      <li><a class="nav-link <?= $admin_page=='portugal.php'?'active':'' ?>" href="portugal.php"><i class="bi bi-geo-alt"></i> Portugal Content</a></li>
      <li><a class="nav-link <?= $admin_page=='documentation.php'?'active':'' ?>" href="documentation.php"><i class="bi bi-file-earmark-text"></i> Documentation</a></li>
      <li><a class="nav-link <?= $admin_page=='pages.php'?'active':'' ?>" href="pages.php"><i class="bi bi-file-richtext"></i> Pages</a></li>
      <li><a class="nav-link <?= $admin_page=='messages.php'?'active':'' ?>" href="messages.php"><i class="bi bi-envelope"></i> Messages
        <?php $unread = DB::fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE is_read=0"); if ($unread): ?>
          <span class="badge bg-warning text-dark ms-1"><?= (int)$unread ?></span>
        <?php endif; ?>
      </a></li>
      <li><a class="nav-link <?= $admin_page=='visa_assessments.php'?'active':'' ?>" href="visa_assessments.php"><i class="bi bi-clipboard-check"></i> Visa Assessments
        <?php $va_unread = DB::fetchColumn("SELECT COUNT(*) FROM visa_assessments WHERE is_read=0"); if ($va_unread): ?>
          <span class="badge bg-warning text-dark ms-1"><?= (int)$va_unread ?></span>
        <?php endif; ?>
      </a></li>
      <li><a class="nav-link <?= $admin_page=='subscribers.php'?'active':'' ?>" href="subscribers.php"><i class="bi bi-people"></i> Subscribers</a></li>
      <li><a class="nav-link <?= $admin_page=='settings.php'?'active':'' ?>" href="settings.php"><i class="bi bi-sliders"></i> Settings</a></li>
      <li class="mt-3 border-top pt-3"><a class="nav-link" href="<?= SITE_URL ?>/index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Website</a></li>
      <li><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
  </aside>
  <div class="admin-content">
    <header class="admin-header">
      <div>
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="adminSidebarToggle"><i class="bi bi-list"></i></button>
        <span class="ms-2 fs-5 fw-bold"><?= e($admin_page_title ?? 'Dashboard') ?></span>
      </div>
      <div class="d-flex align-items-center">
        <span class="me-3 text-muted d-none d-md-inline">Welcome, <strong><?= e($current_admin['full_name']) ?></strong></span>
        <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:var(--primary);font-weight:700;">
          <?= e(strtoupper(substr($current_admin['full_name'] ?? 'A',0,1))) ?>
        </div>
      </div>
    </header>
    <main class="admin-main">
      <?= $admin_content ?? '' ?>
    </main>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
