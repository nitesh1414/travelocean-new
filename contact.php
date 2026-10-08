<?php
$page_title = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
$services = get_services();
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Contact Us</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="100">We'd love to hear from you. Get in touch for a free consultation.</p>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container">
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
        <li class="breadcrumb-item active">Contact</li>
      </ol>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5" data-aos="fade-right">
        <span class="eyebrow">Get in touch</span>
        <h2 class="mb-3">Let's Talk</h2>
        <p class="text-muted">Whether you're relocating, planning to study, starting a business, or just exploring — our team is ready to help.</p>

        <div class="mt-4">
          <div class="contact-info-card">
            <div class="icon-circle"><i class="bi bi-geo-alt"></i></div>
            <div><strong>Portugal Office</strong><a href="#"><?= e(setting('site_address')) ?></a></div>
          </div>
          <div class="contact-info-card">
            <div class="icon-circle"><i class="bi bi-geo-alt"></i></div>
            <div><strong>India Office</strong><a href="#">6, Lobo Apartments, Jayabai Colony Road, Nasik Road 422101 <p></p>+91 95118 40548</a></div>
          </div>
          <div class="contact-info-card">
            <div class="icon-circle"><i class="bi bi-telephone"></i></div>
            <div>
              <strong>Phone</strong>
              <a href="tel:<?= preg_replace('/[^0-9+]/', '', setting('site_phone_1')) ?>"><?= e(setting('site_phone_1')) ?></a><br>
              <a href="tel:<?= preg_replace('/[^0-9+]/', '', setting('site_phone_2')) ?>"><?= e(setting('site_phone_2')) ?></a><br>
              <a href="tel:<?= preg_replace('/[^0-9+]/', '', setting('site_phone_3')) ?>"><?= e(setting('site_phone_3')) ?></a>
            </div>
          </div>
          <div class="contact-info-card">
            <div class="icon-circle"><i class="bi bi-envelope"></i></div>
            <div><strong>Email</strong><a href="mailto:<?= e(setting('site_email')) ?>"><?= e(setting('site_email')) ?></a></div>
          </div>
          <div class="contact-info-card">
            <div class="icon-circle" style="background:rgba(37,211,102,0.1);color:#25d366;"><i class="bi bi-whatsapp"></i></div>
            <div><strong>WhatsApp</strong><a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', setting('whatsapp')) ?>" target="_blank">Chat with us</a></div>
          </div>
        </div>
      </div>

      <div class="col-lg-7" data-aos="fade-left">
        <div class="form-card">
          <h3 class="mb-3"><i class="bi bi-send text-primary me-2"></i> Send Us a Message</h3>
          <form class="ajax-form" method="post" action="<?= SITE_URL ?>/api/contact.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone / WhatsApp</label>
                <input type="text" name="phone" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Service of Interest</label>
                <select name="service_interest" class="form-select">
                  <option value="">-- Select --</option>
                  <?php foreach ($services as $s): ?>
                    <option value="<?= e($s['slug']) ?>" <?= (($_GET['service'] ?? '') === $s['slug']) ? 'selected' : '' ?>><?= e($s['title']) ?></option>
                  <?php endforeach; ?>
                  <option value="study-spain">Study in Spain</option>
                  <option value="study-germany">Study in Germany</option>
                  <option value="study-berlin">Learn German in Berlin</option>
                  <option value="documentation">Documentation</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Brief subject" value="<?= e($_GET['subject'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Your Message *</label>
                <textarea name="message" rows="5" class="form-control" required placeholder="Tell us how we can help…"></textarea>
              </div>
              <div class="col-12">
                <button class="btn btn-primary btn-lg" type="submit">Send Message <i class="bi bi-send ms-1"></i></button>
              </div>
              <div class="col-12">
                <div class="form-message"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>