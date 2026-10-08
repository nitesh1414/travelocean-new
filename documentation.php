<?php
$page_title = 'Documentation Services';
require_once __DIR__ . '/includes/header.php';
$doc_cats = get_doc_categories();
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Documentation Services</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100">End-to-end paperwork assistance — accurate, compliant and well-structured.</p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item active">Documentation</li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($doc_cats as $i => $cat): ?>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?= $i*50 ?>">
          <div class="service-detail">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-circle me-3" style="width:60px;height:60px;background:rgba(10,77,140,0.1);color:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                <i class="bi <?= e($cat['icon']) ?>"></i>
              </div>
              <div>
                <h3 class="mb-0"><?= e($cat['title']) ?></h3>
              </div>
            </div>
            <p class="text-muted"><?= e($cat['description']) ?></p>
            <ul class="features-list mt-3">
              <?php foreach (get_doc_items($cat['id']) as $it): ?>
                <li class="d-flex align-items-start"><i class="bi bi-check-circle-fill text-success me-2 mt-1"></i> <?= e($it['title']) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="alert alert-info mt-4" data-aos="fade-up">
      <i class="bi bi-info-circle-fill me-2"></i>
      <strong>Contact us for reliable services.</strong> As your consultant, our priority is to give you the best services with the collaboration of the best accountants and lawyers to give you stress-free services.
    </div>

    <div class="text-center mt-4">
      <a href="<?= SITE_URL ?>/contact.php?service=documentation" class="btn btn-primary btn-lg">Request Documentation Help <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
