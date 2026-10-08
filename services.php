<?php
$page_title = 'Our Services';
require_once __DIR__ . '/includes/header.php';
$services = get_services();
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Our Services</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100">Comprehensive support for travel, relocation, business and study.</p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container">
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
        <li class="breadcrumb-item active">Services</li>
      </ol>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">What We Offer</span>
      <h2>Everything You Need, Under One Roof</h2>
      <p>From your first consultation to settling into your new life — our team provides practical, personalized and reliable services.</p>
    </div>

    <div class="row g-4">
      <?php foreach ($services as $i => $svc):
        $has_image = !empty($svc['image']) && file_exists(__DIR__ . '/' . $svc['image']);
        $bg_image  = $has_image ? SITE_URL . '/' . e($svc['image']) : '';


      ?>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 50 ?>">

          <div class="service-detail" id="svc-<?= e($svc['slug']) ?>">
            <div class="d-flex align-items-start mb-3">
              <div class="icon-circle me-3" style="width:60px;height:60px;background:rgba(10,77,140,0.1);color:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                <i class="bi <?= e($svc['icon']) ?>"></i>
              </div>
              <div>
                <h3 class="mb-1"><?= e($svc['title']) ?></h3>

              </div>

            </div>
            <p class="text-muted mb-0"><?= e($svc['short_description']) ?></p>
            <?php if ($svc['full_description']): ?>
              <p><?= nl2br(e($svc['full_description'])) ?></p>
            <?php endif; ?>
            <ul class="features-list">
              <?php foreach (get_service_features($svc['id']) as $f): ?>
                <li><?= e($f['feature_text']) ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="<?= SITE_URL ?>/<?= e($svc['button_link']) ?>" class="btn btn-outline-primary btn-sm mt-2">
              <i class="bi bi-envelope me-1"></i> <?= e($svc['button_text']) ?>
            </a>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Why choose us -->
<section class="section section-light">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">Why Travel Oceans</span>
      <h2>Why Choose Travel Oceans?</h2>
    </div>
    <div class="row g-4">
      <?php foreach (get_why_choose() as $i => $r): ?>
        <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?= $i * 50 ?>">
          <div class="reason-card">
            <div class="icon-circle"><i class="bi <?= e($r['icon']) ?>"></i></div>
            <h5><?= e($r['title']) ?></h5>
            <p class="text-muted small mb-0"><?= e($r['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-banner">
  <div class="container text-center">
    <h2 class="text-white">Need Help Choosing a Service?</h2>
    <p class="mb-4">Tell us about your goals and we'll recommend the right path — free, no obligation.</p>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary btn-lg">Get Free Consultation</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>