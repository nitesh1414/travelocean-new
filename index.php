<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

$slides       = get_slides();
$services     = get_services(true);
$programs     = get_programs();
$testimonials = get_testimonials();
$why_choose   = get_why_choose();
?>

<!-- Hero Slider -->
<section class="hero">
  <div class="hero-slider owl-carousel owl-theme">
    <?php foreach ($slides as $s):
      $has_image = !empty($s['image']) && file_exists(__DIR__ . '/' . $s['image']);
      $bg_image  = $has_image ? SITE_URL . '/' . e($s['image']) : '';
    ?>
      <div class="hero-slide" <?= $has_image ? 'style="background-image: linear-gradient(rgba(10,77,140,0.25), rgba(10,77,140,0.35)), url(\'' . $bg_image . '\'); background-size: cover; background-position: center;"' : '' ?>>
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-9 hero-content" data-aos="fade-right">
              <span class="badge-soft"><i class="bi bi-stars me-1"></i> Trusted by Travellers & Expats Worldwide</span>
              <h1><?= e($s['title']) ?></h1>
              <?php if ($s['subtitle']): ?>
                <h3 class="h4 text-white-50 fw-normal mb-3"><span><?= e($s['subtitle']) ?></span></h3>
              <?php endif; ?>
              <?php if ($s['description']): ?>
                <p class="lead"><span><?= e($s['description']) ?></span></p>
              <?php endif; ?>
              <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-secondary btn-lg" href="<?= e($s['button_link']) ?>"><?= e($s['button_text']) ?></a>
                <a class="btn btn-primary btn-lg" href="<?= SITE_URL ?>/contact.php">Get Free Consultation</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Stats -->
<section class="section-dark">
  <div class="container">
    <div class="row">
      
      <div class="col-md-4 col-6"><div class="stat-box"><span class="number counter" data-target="15" data-suffix="+">0</span><span class="label">Years Experience</span></div></div>
      <div class="col-md-4 col-6"><div class="stat-box"><span class="number counter" data-target="50" data-suffix="+">0</span><span class="label">Trusted Partner</span></div></div>
      <div class="col-md-4 col-6"><div class="stat-box"><span class="number counter" data-target="99" data-suffix="%">0</span><span class="label">Success Rate</span></div></div>
    </div>
  </div>
</section>

<!-- About / Why choose -->
<section class="section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow">Your Trusted Partner</span>
        <h2 class="mb-3">Travel • Relocation • Business Support • Local Guidance</h2>
        <p class="text-muted">We provide practical support, personalized guidance, and trusted connections for individuals, families, entrepreneurs, remote workers, and travellers looking to <strong>visit, relocate, or establish themselves in Portugal</strong>.</p>
        <p class="text-muted">From your first consultation to settling into your new home — we are with you every step of the way.</p>
        <a href="<?= SITE_URL ?>/about.php" class="btn btn-primary mt-2">Learn More About Us <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          <?php foreach (array_slice($why_choose, 0, 4) as $r): ?>
            <div class="col-md-6">
              <div class="reason-card">
                <div class="icon-circle"><i class="bi <?= e($r['icon']) ?>"></i></div>
                <h5><?= e($r['title']) ?></h5>
                <p class="text-muted small mb-0"><?= e(truncate($r['description'], 90)) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section class="section section-light" id="services">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">What We Do</span>
      <h2>Our Services</h2>
      <p>Comprehensive support for travel, relocation, business and study — all under one roof.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($services as $svc): ?>
        <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="50">
          <div class="service-card">
            <div class="icon-wrap"><i class="bi <?= e($svc['icon']) ?>"></i></div>
            <h5><?= e($svc['title']) ?></h5>
            <p class="text-muted small mb-2"><?= e(truncate($svc['short_description'], 90)) ?></p>
            <a class="stretched-link text-decoration-none small fw-bold" href="<?= SITE_URL ?>/services.php#svc-<?= e($svc['slug']) ?>">Learn more <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= SITE_URL ?>/services.php" class="btn btn-primary btn-lg">View All Services <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<!-- Study Abroad -->
<section class="section">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">Education Opportunities</span>
      <h2>Study Abroad Programs</h2>
      <p>Top universities in Spain and Germany with affordable tuition, post-study work visas and full visa assistance.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($programs as $p):
        $bg = $p['slug']==='study-spain' ? 'linear-gradient(135deg,#aa1518,#c60b1e)'
            : ($p['slug']==='study-germany' ? 'linear-gradient(135deg,#000,#dd0000)'
            : 'linear-gradient(135deg,#ffce00,#dd0000)'); ?>
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <div class="program-card">
            <div class="program-img" style="background: <?= $bg ?>;">
              <span class="flag-badge"><?= e($p['flag_emoji']) ?></span>
              <h4><?= e($p['title']) ?></h4>
            </div>
            <div class="program-body">
              <p class="text-muted"><?= e(truncate($p['description'], 130)) ?></p>
              <div class="mb-3">
                <?php foreach (array_slice(get_program_highlights($p['id']), 0, 4) as $h): ?>
                  <span class="feature-tag"><i class="bi <?= e($h['icon']) ?>"></i> <?= e($h['feature_text']) ?></span>
                <?php endforeach; ?>
              </div>
              <a href="<?= SITE_URL ?>/program.php?slug=<?= e($p['slug']) ?>" class="btn btn-primary btn-sm">Explore Program <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Business Formation Highlight -->
<section class="section section-light">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow">Start Your Business</span>
        <h2>Start Your Business in Portugal</h2>
        <p class="text-muted">Your Gateway to Europe. Endless Opportunities. At Travel Oceans, we provide professional <strong>Business Formation Services</strong> to help entrepreneurs, investors, freelancers, and startups set up their businesses quickly and efficiently.</p>
        <ul class="list-unstyled mb-4">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Fast and hassle-free company setup</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Strategic European location & EU access</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Tax & compliance assistance</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> End-to-end business setup solutions</li>
        </ul>
        <a href="<?= SITE_URL ?>/business-formation.php" class="btn btn-primary">Start Your Business <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          <?php foreach (get_business_services() as $bs): ?>
            <div class="col-md-6">
              <div class="reason-card">
                <div class="icon-circle"><i class="bi <?= e($bs['icon']) ?>"></i></div>
                <h5><?= e($bs['title']) ?></h5>
                <p class="text-muted small mb-0"><?= e($bs['description']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="section section-dark">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow" style="background: rgba(255,255,255,0.1); color: var(--secondary);">Client Stories</span>
      <h2 class="text-white">What Our Clients Say</h2>
      <p class="text-white-50">Real stories from people who trusted us with their journey.</p>
    </div>
    <div class="testimonial-slider owl-carousel owl-theme">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="quote">"</div>
          <div class="stars">
            <?php for ($i=0;$i<(int)$t['rating'];$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
          </div>
          <p><?= e($t['message']) ?></p>
          <div class="client">
            <div class="avatar"><?= e(strtoupper(substr($t['client_name'],0,1))) ?></div>
            <div>
              <strong><?= e($t['client_name']) ?></strong>
              <span><?= e($t['client_role']) ?><?= $t['client_country'] ? ' · '.e($t['client_country']) : '' ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-banner">
  <div class="container text-center position-relative" data-aos="fade-up">
    <h2>Ready to Start Your Journey?</h2>
    <p class="mb-4">Get a free consultation with our experts — we'll guide you every step of the way.</p>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary btn-lg me-2">Contact Us Today <i class="bi bi-arrow-right ms-1"></i></a>
    <a href="tel:<?= preg_replace('/[^0-9+]/','',setting('site_phone_1')) ?>" class="btn btn-outline-light btn-lg"><i class="bi bi-telephone me-1"></i> Call Now</a>
  </div>
</section>

<!-- Newsletter -->
<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center" data-aos="fade-up">
        <h3>Stay Updated</h3>
        <p class="text-muted">Subscribe to our newsletter for the latest updates on Portugal immigration, business opportunities and study programs.</p>
        <form class="newsletter-form d-flex gap-2 mt-4 justify-content-center flex-wrap">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required style="max-width: 350px;">
          <button class="btn btn-primary" type="submit">Subscribe</button>
        </form>
        <div class="newsletter-message mt-2"></div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
