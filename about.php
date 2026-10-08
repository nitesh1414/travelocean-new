<?php
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
$page = get_page('about');
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">About Travel Oceans</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100">Your trusted partner for travel, relocation and business.</p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item active">About</li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow">Our Story</span>
        <h2>Travel Across the Oceans</h2>
        <div class="mt-3 text-muted" style="font-size:1.05rem;">
          <?php if ($page): ?>
            <?= $page['content'] ?>
          <?php else: ?>
            <p>Travel Oceans is a Portugal-based consultancy dedicated to helping travellers, expats, entrepreneurs, students and families navigate their journey with practical guidance, personalized support and trusted local connections.</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          <div class="col-6">
            <div class="rounded shadow-sm p-4 text-center text-white" style="background: var(--gradient-primary);">
              <div style="font-size:3rem;">🌍</div>
              <h5 class="text-white mt-2">Global Reach</h5>
              <p class="small text-white-50 mb-0">Clients from over 25 countries</p>
            </div>
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm p-4 text-center text-white" style="background: var(--gradient-warm);">
              <div style="font-size:3rem;">🤝</div>
              <h5 class="text-white mt-2">Trusted Partners</h5>
              <p class="small text-white-50 mb-0">50+ verified partners</p>
            </div>
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm p-4 text-center text-white" style="background: linear-gradient(135deg,#198754,#20c997);">
              <div style="font-size:3rem;">✨</div>
              <h5 class="text-white mt-2">Personalized</h5>
              <p class="small text-white-50 mb-0">Tailored to every pilgrim tour</p>
            </div>
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm p-4 text-center text-white" style="background: linear-gradient(135deg,#6610f2,#a568f7);">
              <div style="font-size:3rem;">🏆</div>
              <h5 class="text-white mt-2">99% Success</h5>
              <p class="small text-white-50 mb-0">Visa & business approvals</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-title mt-5">
      <span class="eyebrow">Our Values</span>
      <h2>Why Choose Travel Oceans?</h2>
      <p>We are committed to making every step of your journey clear, simple and stress-free.</p>
    </div>
    <div class="row g-4">
      <?php foreach (get_why_choose() as $i => $r): ?>
        <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?= $i*50 ?>">
          <div class="reason-card text-start">
            <div class="icon-circle mb-3"><i class="bi <?= e($r['icon']) ?>"></i></div>
            <h5><?= e($r['title']) ?></h5>
            <p class="text-muted small mb-0"><?= e($r['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <p class="text-muted lead" style="max-width:800px;margin:0 auto;">
        At Travel Oceans, our mission is to provide <strong>support, guidance, and practical assistance</strong> to help individuals, families, entrepreneurs, and travelers confidently navigate their journey in Portugal.
      </p>
      <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary btn-lg mt-3">Start Your Journey <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<!-- Team Members -->
<?php $team = get_team_members(); if ($team): ?>
<section class="section section-light">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">Meet Our Team</span>
      <h2>The People Behind Travel Oceans</h2>
      <p>A multilingual, multicultural team of professionals dedicated to making your journey smooth and successful.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($team as $t):
        $initials = strtoupper(substr($t['name'],0,1) . (strpos($t['name'],' ') !== false ? substr($t['name'],strpos($t['name'],' ')+1,1) : ''));
      ?>
        <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="50">
          <div class="team-card">
            <div class="team-photo">
              <?php if (!empty($t['photo']) && file_exists(__DIR__ . '/' . $t['photo'])): ?>
                <img src="<?= SITE_URL ?>/<?= e($t['photo']) ?>" alt="<?= e($t['name']) ?>" loading="lazy">
              <?php else: ?>
                <div class="team-initials"><?= e($initials) ?></div>
              <?php endif; ?>
              <?php if ($t['linkedin']): ?>
                <a class="team-social" href="<?= e($t['linkedin']) ?>" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>
              <?php endif; ?>
            </div>
            <div class="team-body">
              <h5 class="mb-1"><?= e($t['name']) ?></h5>
              <div class="team-designation"><?= e($t['designation']) ?></div>
              <?php if ($t['bio']): ?>
                <p class="team-bio"><?= e($t['bio']) ?></p>
              <?php endif; ?>
              <?php if ($t['email'] || $t['phone']): ?>
                <div class="team-contact small text-muted">
                  <?php if ($t['email']): ?><i class="bi bi-envelope me-1"></i><a class="text-decoration-none text-muted" href="mailto:<?= e($t['email']) ?>"><?= e($t['email']) ?></a><?php endif; ?>
                  <?php if ($t['phone']): ?><?php if ($t['email']): ?><br><?php endif; ?><i class="bi bi-telephone me-1"></i><a class="text-decoration-none text-muted" href="tel:<?= preg_replace('/[^0-9+]/','',$t['phone']) ?>"><?= e($t['phone']) ?></a><?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
