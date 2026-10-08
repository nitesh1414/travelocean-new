<?php
$page_title = 'Trainings';
require_once __DIR__ . '/includes/header.php';

// Load all CMS content blocks for this page
$blocks  = get_page_blocks('training');
$partners = get_partners();
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up"><?= e($blocks['hero_title'] ?? 'Trainings') ?></h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100"><?= e($blocks['hero_subtitle'] ?? 'Our Specialized Training Programs') ?></p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/services.php">Our Services</a></li>
    <li class="breadcrumb-item active"><?= e($blocks['hero_title'] ?? 'Trainings') ?></li>
  </ol></nav></div>
</div>

<!-- Intro section -->
<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7" data-aos="fade-right">
        <span class="eyebrow"><i class="bi bi-mortarboard me-1"></i> Trainings</span>
        <h2 class="mb-3"><?= e($blocks['hero_title'] ?? 'Trainings') ?></h2>
        <div class="text-muted" style="font-size:1.05rem;line-height:1.8;">
          <?= nl2br(e($blocks['intro_text'] ?? '')) ?>
        </div>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div class="row g-3">
          <div class="col-6">
            <div class="training-stat">
              <span class="stat-num"><?= e($blocks['stat_1_number'] ?? '25') ?></span>
              <span class="stat-lbl"><?= e($blocks['stat_1_label'] ?? 'Years of Experience') ?></span>
            </div>
          </div>
          <div class="col-6">
            <div class="training-stat" style="background:var(--gradient-warm);">
              <span class="stat-num"><?= e($blocks['stat_2_number'] ?? '100%') ?></span>
              <span class="stat-lbl"><?= e($blocks['stat_2_label'] ?? 'Genuine Assistance') ?></span>
            </div>
          </div>
          <div class="col-6">
            <div class="training-stat" style="background:linear-gradient(135deg,#198754,#20c997);">
              <span class="stat-lbl-full"><?= e($blocks['stat_3_label'] ?? 'Faster & Reliable Execution') ?></span>
            </div>
          </div>
          <div class="col-6">
            <div class="training-stat" style="background:linear-gradient(135deg,#6610f2,#a568f7);">
              <span class="stat-lbl-full"><?= e($blocks['stat_4_label'] ?? 'Accurate & Expert Advice') ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Partners section -->
<?php if ($partners): ?>
<section class="section section-light">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow"><i class="bi bi-handshake me-1"></i> <?= e($blocks['partners_title'] ?? 'Our Partner') ?></span>
      <h2><?= e($blocks['partners_subtitle'] ?? 'Our strong Network of Partners') ?></h2>
      <p><?= e($blocks['partners_description'] ?? 'We collaborate with trusted partners and service providers to deliver reliable, compliant, and efficient solutions to our clients.') ?></p>
    </div>

    <div class="row g-4">
      <?php foreach ($partners as $i => $p): ?>
        <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?= $i * 100 ?>">
          <div class="partner-card">
            <div class="partner-logo-wrap">
              <?php if (!empty($p['logo']) && file_exists(__DIR__ . '/' . $p['logo'])): ?>
                <img src="<?= SITE_URL ?>/<?= e($p['logo']) ?>" alt="<?= e($p['name']) ?>" class="partner-logo">
              <?php else: ?>
                <div class="partner-logo-placeholder">
                  <i class="bi bi-building"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="partner-body">
              <h5 class="partner-name"><?= e($p['name']) ?></h5>
              <?php if ($p['location']): ?>
                <div class="partner-location"><i class="bi bi-geo-alt-fill"></i> <?= e($p['location']) ?></div>
              <?php endif; ?>
              <?php if ($p['tagline']): ?>
                <div class="partner-tagline"><?= e($p['tagline']) ?></div>
              <?php endif; ?>
              <p class="partner-desc"><?= e($p['description'] ?? '') ?></p>
              <?php if ($p['website_url']): ?>
                <a class="btn btn-outline-primary btn-sm" href="<?= e($p['website_url']) ?>" target="_blank" rel="noopener">
                  Explore More <i class="bi bi-arrow-right ms-1"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Important Update -->
<?php if (!empty($blocks['important_update_text'])): ?>
<section class="section">
  <div class="container">
    <div class="alert-update" data-aos="fade-up">
      <div class="alert-update-icon"><i class="bi bi-megaphone-fill"></i></div>
      <div class="alert-update-content">
        <h4 class="alert-update-title"><?= e($blocks['important_update_title'] ?? 'Important Update') ?></h4>
        <p class="alert-update-text"><?= nl2br(e($blocks['important_update_text'])) ?></p>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-banner">
  <div class="container text-center">
    <h2 class="text-white">Have any questions?</h2>
    <p class="mb-4">Talk to our team — we offer free initial consultation and accurate expert advice.</p>
    <a href="tel:<?= preg_replace('/[^0-9+]/','',setting('site_phone_1')) ?>" class="btn btn-secondary btn-lg me-2"><i class="bi bi-telephone me-1"></i> Call Now</a>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-light btn-lg"><i class="bi bi-envelope me-1"></i> Send Enquiry</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
