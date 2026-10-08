<?php
$page_title = 'Start Your Business in Portugal';
require_once __DIR__ . '/includes/header.php';
$business_services = get_business_services();
$reasons = get_portugal_reasons();
?>
<section class="page-header accent-spain">
  <div class="container">
    <h1 data-aos="fade-up">Start Your Business in <span style="color:#ffce00">Portugal</span></h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100">Your Gateway to Europe. Endless Opportunities.</p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item active">Business Formation</li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row align-items-center g-5 mb-5">
      <div class="col-lg-7" data-aos="fade-right">
        <span class="eyebrow">Business Formation</span>
        <h2 class="mb-3">Launch Your Business in Portugal</h2>
        <p class="text-muted">At <strong>Travel Oceans</strong>, we provide professional <strong>Business Formation Services</strong> to help entrepreneurs, investors, freelancers, and startups set up their businesses quickly and efficiently.</p>
        <p class="text-muted">From company registration to tax compliance — we handle it all so you can focus on growing your business.</p>
        <div class="d-flex gap-2 flex-wrap mt-4">
          <a href="<?= SITE_URL ?>/contact.php?service=business-entrepreneur" class="btn btn-primary btn-lg">Get Started <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-primary btn-lg">Talk to an Expert</a>
        </div>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div class="text-center p-4 rounded shadow-sm" style="background: linear-gradient(135deg,#0a4d8c,#1d6cb5); color:#fff;">
          <div style="font-size:5rem;">🇵🇹</div>
          <h4 class="text-white mt-2">Portugal</h4>
          <p class="mb-0 text-white-50">A business-friendly European hub for international founders.</p>
        </div>
      </div>
    </div>

    <div class="section-title mt-5">
      <span class="eyebrow">Our Business Formation Services</span>
      <h2>What We Help You With</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($business_services as $i => $bs): ?>
        <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?= $i*50 ?>">
          <div class="reason-card text-start">
            <div class="icon-circle mb-3"><i class="bi <?= e($bs['icon']) ?>"></i></div>
            <h5><?= e($bs['title']) ?></h5>
            <p class="text-muted small mb-0"><?= e($bs['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-light">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow">Why Portugal</span>
        <h2>Why Choose Portugal?</h2>
        <p class="text-muted">Portugal is one of the most attractive destinations in Europe for entrepreneurs and investors — combining a strategic location, a welcoming business environment and access to the entire EU market.</p>
        <a href="<?= SITE_URL ?>/portugal.php" class="btn btn-outline-primary">Discover Portugal <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          <?php foreach ($reasons as $r): ?>
            <div class="col-md-6">
              <div class="reason-card text-start">
                <div class="icon-circle mb-3"><i class="bi <?= e($r['icon']) ?>"></i></div>
                <h5><?= e($r['title']) ?></h5>
                <p class="text-muted small mb-0"><?= e($r['description']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container text-center">
    <h2 class="text-white">Your Business Journey. Our Expertise.</h2>
    <p class="mb-4">Let our experienced team simplify the process while you focus on building your business.</p>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary btn-lg">Contact Us Today <i class="bi bi-arrow-right ms-1"></i></a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
