<?php
require_once __DIR__ . '/functions.php';
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = $page_title ?? setting('site_name');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($page_title) ?> | <?= e(setting('site_name')) ?></title>
  <meta name="description" content="<?= e(setting('meta_description')) ?>">
  <meta name="keywords" content="<?= e(setting('meta_keywords')) ?>">
  <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
  <link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/owl.carousel.min.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/owl.theme.default.min.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/aos.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>

<body>

  <!-- Top bar -->
  <div class="top-bar bg-dark text-white py-2 d-none d-lg-block">
    <div class="container d-flex justify-content-between align-items-center">
      <div>
        <i class="bi bi-envelope me-1"></i> <a class="text-white text-decoration-none" href="mailto:<?= e(setting('site_email')) ?>"><?= e(setting('site_email')) ?></a>
        <span class="mx-2">|</span>
        <i class="bi bi-globe me-1"></i> <?= e(setting('site_url')) ?>
      </div>
      <div>
        <i class="bi bi-telephone me-1"></i> <?= e(setting('site_phone_1')) ?>
        <span class="mx-2">|</span>
        <?php if (setting('facebook')): ?><a class="text-white me-2" href="<?= e(setting('facebook')) ?>" target="_blank"><i class="bi bi-facebook"></i></a><?php endif; ?>
        <?php if (setting('instagram')): ?><a class="text-white me-2" href="<?= e(setting('instagram')) ?>" target="_blank"><i class="bi bi-instagram"></i></a><?php endif; ?>
        <?php if (setting('linkedin')): ?><a class="text-white me-2" href="<?= e(setting('linkedin')) ?>" target="_blank"><i class="bi bi-tiktok"></i></a><?php endif; ?>
        <?php if (setting('youtube')): ?><a class="text-white me-2" href="<?= e(setting('youtube')) ?>" target="_blank"><i class="bi bi-youtube"></i></a><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="<?= SITE_URL ?>/index.php">
        <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo" class="img-fluid">
        <div>
          <div class="brand-name"><?= e(setting('site_name')) ?>

          </div>
          <div class="brand-tag"><?= e(setting('site_tagline')) ?></div>

        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/about.php">About</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= $current_page == 'services.php' ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown" aria-expanded="false">Services</a>
            <ul class="dropdown-menu mega-menu">
              <?php foreach (get_services() as $svc): ?>
                <li><a class="dropdown-item" href="<?= SITE_URL ?>/services.php#svc-<?= e($svc['slug']) ?>"><i class="bi <?= e($svc['icon']) ?> me-2"></i> <?= e($svc['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Study Abroad</a>
            <ul class="dropdown-menu">
              <?php foreach (get_programs() as $p): ?>
                <li><a class="dropdown-item" href="<?= SITE_URL ?>/program.php?slug=<?= e($p['slug']) ?>"><?= e($p['flag_emoji']) ?> <?= e($p['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link <?= $current_page == 'portugal.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/portugal.php">Why Portugal</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/contact.php">Contact</a></li>

        </ul>
      </div>
    </div>
  </nav>