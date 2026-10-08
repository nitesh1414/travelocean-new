<?php
require_once __DIR__ . '/includes/header.php';
$slug = $_GET['slug'] ?? '';
$program = get_program($slug);
if (!$program) { header('Location: ' . SITE_URL . '/index.php'); exit; }
$highlights = get_program_highlights($program['id']);
$details    = get_program_details($program['id']);
$bg_class   = $program['slug']==='study-spain' ? 'accent-spain' : ($program['slug']==='study-berlin' ? 'accent-berlin' : 'accent-germany');
$page_title = $program['title'];
?>
<section class="page-header <?= $bg_class ?>">
  <div class="container">
    <h1 data-aos="fade-up"><?= e($program['title']) ?> <?= e($program['flag_emoji']) ?></h1>
    <?php if ($program['tagline']): ?><p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100"><?= e($program['tagline']) ?></p><?php endif; ?>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php#services">Study Abroad</a></li>
    <li class="breadcrumb-item active"><?= e($program['title']) ?></li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8" data-aos="fade-right">
        <span class="eyebrow">Program Overview</span>
        <h2 class="mb-3"><?= e($program['title']) ?> <?= e($program['flag_emoji']) ?></h2>
        <p class="lead text-muted"><?= e($program['description']) ?></p>

        <?php if ($highlights): ?>
        <h3 class="mt-4 mb-3">Program Highlights</h3>
        <div class="row g-3">
          <?php foreach ($highlights as $h): ?>
            <div class="col-md-6">
              <div class="d-flex align-items-start p-3 rounded" style="background:#f8fafc;">
                <i class="bi <?= e($h['icon']) ?> text-primary fs-3 me-3"></i>
                <div class="flex-grow-1"><strong><?= e($h['feature_text']) ?></strong></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($details): ?>
        <h3 class="mt-5 mb-3">Key Details</h3>
        <div class="row g-3">
          <?php foreach ($details as $d): ?>
            <div class="col-md-6">
              <div class="d-flex align-items-center p-3 rounded border">
                <i class="bi <?= e($d['icon']) ?> text-secondary fs-3 me-3"></i>
                <div>
                  <small class="text-muted text-uppercase"><?= e($d['detail_key']) ?></small>
                  <div class="fw-bold"><?= e($d['detail_value']) ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4" data-aos="fade-left">
        <div class="form-card sticky-top" style="top:100px;">
          <h4 class="mb-3"><i class="bi bi-send text-primary me-2"></i> Enquire About This Program</h4>
          <form class="ajax-form" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="subject" value="Program Enquiry: <?= e($program['title']) ?>">
            <input type="hidden" name="service_interest" value="<?= e($program['slug']) ?>">
            <div class="mb-3"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Phone / WhatsApp</label><input type="text" name="phone" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Message *</label><textarea name="message" rows="4" class="form-control" required placeholder="Tell us about your study goals…"></textarea></div>
            <button class="btn btn-primary w-100" type="submit">Send Enquiry <i class="bi bi-send ms-1"></i></button>
            <div class="form-message mt-2"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container text-center">
    <h2 class="text-white">Contact Us Today</h2>
    <p class="mb-4">Take the first step toward your future in <?= e($program['country']) ?><?= $program['city'] ? ' ('.e($program['city']).')' : '' ?>.</p>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary btn-lg me-2">Contact Us <i class="bi bi-arrow-right ms-1"></i></a>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',setting('whatsapp')) ?>" target="_blank" class="btn btn-outline-light btn-lg"><i class="bi bi-whatsapp me-1"></i> WhatsApp</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
