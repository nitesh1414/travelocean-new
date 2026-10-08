<?php
$page_title = 'Portugal - A Place to Fall in Love';
require_once __DIR__ . '/includes/header.php';
$things = get_portugal_things();
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Portugal 🇵🇹</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100"><em>A place to fall in love 💙</em></p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item active">Portugal</li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row align-items-center g-5 mb-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow">Discover Portugal</span>
        <h2 class="mb-3">A Place to Fall in Love</h2>
        <p class="text-muted">Portugal is a country of beauty, culture, flavor and warm welcomes. A destination that stays in your heart and makes you want to come back.</p>
        <p class="text-muted">From the historic streets of Lisbon and Porto to the stunning beaches of the Algarve, and the natural wonders of Madeira and the Azores — every place has its own magic.</p>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="row g-3">
          <div class="col-6">
            <div class="rounded shadow-sm overflow-hidden" style="height:160px;background:linear-gradient(135deg,#f5a623,#e88a17);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;">
              <img src="<?= SITE_URL ?>/assets/images/lisbon-tram.png" alt="Portugal Tram">
            </div>
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm overflow-hidden" style="height:160px;background:linear-gradient(135deg,#aa1518,#c60b1e);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;">
              <img src="<?= SITE_URL ?>/assets/images/56.png" alt="Portugal Monument">
            </div>
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm overflow-hidden" style="height:160px;background:linear-gradient(135deg,#0a4d8c,#1d6cb5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;">
              <img src="<?= SITE_URL ?>/assets/images/lisbon-dis.png" alt="Portugal Dish">
            </div>
            
          </div>
          <div class="col-6">
            <div class="rounded shadow-sm overflow-hidden" style="height:160px;background:linear-gradient(135deg,#198754,#20c997);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;">
              <img src="<?= SITE_URL ?>/assets/images/Sunrise-in-Algarve.png" alt="Portugal Flag">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-title mt-5">
      <span class="eyebrow">Why We Love Portugal</span>
      <h2>What Makes Portugal Special</h2>
    </div>

    <div class="row g-4">
      <div class="col-md-4 col-sm-6" data-aos="zoom-in">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-camera"></i></div><h5>Beautiful Places</h5><p class="small text-muted">From the historic streets of Lisbon and Porto to the stunning beaches of the Algarve, Madeira and the Azores — every place has its own magic.</p></div>
      </div>
      <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="50">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-egg-fried"></i></div><h5>Delicious Food</h5><p class="small text-muted">Fresh seafood, traditional recipes and rich flavors that reflect Portugal's heritage and love for good food.</p><p class="small fw-bold text-primary mt-2">A true culinary experience.</p></div>
      </div>
      <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="100">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-cup-hot"></i></div><h5>World-Famous Wine</h5><p class="small text-muted">Home to exceptional wines such as Port Wine, Douro and Alentejo wines — recognized and enjoyed all over the world.</p></div>
      </div>
      <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="150">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-cake2"></i></div><h5>Delicious Desserts</h5><p class="small text-muted">Indulge in the famous Pastel de Nata and many other traditional sweets that make every moment even sweeter.</p><p class="small fw-bold text-primary mt-2">A treat for every sweet lover.</p></div>
      </div>
      <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="200">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-sun"></i></div><h5>Amazing Weather</h5><p class="small text-muted">Enjoy a mild climate, plenty of sunshine and beautiful days all year round.</p><p class="small fw-bold text-primary mt-2">Perfect for outdoor living and exploring.</p></div>
      </div>
      <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="250">
        <div class="reason-card"><div class="icon-circle"><i class="bi bi-people"></i></div><h5>Welcoming People</h5><p class="small text-muted">Portuguese people are known for their kindness, hospitality and strong sense of community.</p><p class="small fw-bold text-primary mt-2">You will feel welcome from the first day.</p></div>
      </div>
    </div>
  </div>
</section>

<section class="section section-light">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">Must-Do Experiences</span>
      <h2>Things You Must Do in Portugal</h2>
      <p>Portugal is more than a destination, it's a way of life.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($things as $i => $t): ?>
        <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="<?= $i*30 ?>">
          <div class="portugal-feature p-3 h-100 rounded shadow-sm">
            <div class="pf-icon"><i class="bi <?= e($t['icon']) ?>"></i></div>
            <h6><?= e($t['title']) ?></h6>
            <p><?= e($t['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container text-center">
    <h2 class="text-white">Ready to Experience Portugal?</h2>
    <p class="mb-4">Travel • Relocate • Start a business — we make it simple.</p>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary btn-lg">Plan Your Journey <i class="bi bi-arrow-right ms-1"></i></a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
