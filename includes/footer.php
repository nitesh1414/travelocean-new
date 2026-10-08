<!-- Footer -->
<footer class="site-footer bg-dark text-white pt-5 pb-3">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center mb-3">
          <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo" class="img-fluid">
          <div>
            <div class="brand-name text-white fs-4"><?= e(setting('site_name')) ?></div>
            <p></p>
            <div class="text-white-50 small"><?= e(setting('site_tagline')) ?></div>
          </div>
        </div>
        <p class="text-white-50"><?= e(setting('footer_about')) ?></p>
        <div class="social-icons mt-3">
          <?php if (setting('facebook')): ?><a class="text-white me-2" href="<?= e(setting('facebook')) ?>" target="_blank"><i class="bi bi-facebook fs-5"></i></a><?php endif; ?>
          <?php if (setting('instagram')): ?><a class="text-white me-2" href="<?= e(setting('instagram')) ?>" target="_blank"><i class="bi bi-instagram fs-5"></i></a><?php endif; ?>
          <?php if (setting('linkedin')): ?><a class="text-white me-2" href="<?= e(setting('linkedin')) ?>" target="_blank"><i class="bi bi-linkedin fs-5"></i></a><?php endif; ?>
          <?php if (setting('youtube')): ?><a class="text-white me-2" href="<?= e(setting('youtube')) ?>" target="_blank"><i class="bi bi-youtube fs-5"></i></a><?php endif; ?>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <h5 class="text-white mb-3">Quick Links</h5>
        <ul class="list-unstyled footer-links">
          <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
          <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
          <li><a href="<?= SITE_URL ?>/services.php">Services</a></li>
          <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-4">
        <h5 class="text-white mb-3">Our Services</h5>
        <ul class="list-unstyled footer-links">
          <?php foreach (array_slice(get_services(),0,6) as $svc): ?>
            <li><a href="<?= SITE_URL ?>/services.php#svc-<?= e($svc['slug']) ?>"><?= e($svc['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="col-lg-3 col-md-4">
        <h5 class="text-white mb-3">Get In Touch</h5>
        <ul class="list-unstyled text-white-50">
          <li class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i><?= e(setting('site_address')) ?></li>
          <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><a class="text-white-50 text-decoration-none" href="tel:<?= preg_replace('/[^0-9+]/','',setting('site_phone_1')) ?>"><?= e(setting('site_phone_1')) ?></a></li>
          <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><a class="text-white-50 text-decoration-none" href="tel:<?= preg_replace('/[^0-9+]/','',setting('site_phone_2')) ?>"><?= e(setting('site_phone_2')) ?></a></li>
          <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i><a class="text-white-50 text-decoration-none" href="tel:<?= preg_replace('/[^0-9+]/','',setting('site_phone_3')) ?>"><?= e(setting('site_phone_3')) ?></a></li>
          <li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i><a class="text-white-50 text-decoration-none" href="mailto:<?= e(setting('site_email')) ?>"><?= e(setting('site_email')) ?></a></li>
          <li class="mb-2"><i class="bi bi-globe me-2 text-primary"></i><?= e(setting('site_url')) ?></li>
        </ul>
      </div>
    </div>

    <!-- Important Notice -->
    <div class="important-notice mt-4 p-3 rounded">
      <div class="d-flex">
        <i class="bi bi-info-circle-fill text-warning fs-4 me-3"></i>
        <div>
          <strong class="text-warning">Disclaimer</strong>
          <p class="mb-0 text-white-50 small"><?= e(setting('important_notice')) ?></p>
        </div>
      </div>
    </div>

    <hr class="border-secondary mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-white-50 small">
      <div>&copy; <?= date('Y') ?> <?= e(setting('site_name')) ?>. All rights reserved.</div>
      <div>
        <a class="text-white-50 text-decoration-none me-3" href="<?= SITE_URL ?>/page.php?slug=privacy">Privacy Policy</a>
        <a class="text-white-50 text-decoration-none" href="<?= SITE_URL ?>/page.php?slug=terms">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<!-- WhatsApp Floating -->
<a class="whatsapp-float" href="https://wa.me/<?= preg_replace('/[^0-9]/','',setting('whatsapp')) ?>" target="_blank" title="Chat on WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>

<!-- Back to top -->
<button class="back-to-top" id="backToTop" title="Back to top"><i class="bi bi-arrow-up"></i></button>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
