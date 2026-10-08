<?php
require_once __DIR__ . '/includes/header.php';
$slug = $_GET['slug'] ?? '';
$page = get_page($slug);
if (!$page) { header('Location: ' . SITE_URL . '/index.php'); exit; }
$page_title = $page['title'];
?>
<section class="page-header">
  <div class="container">
    <h1 data-aos="fade-up"><?= e($page['title']) ?></h1>
  </div>
</section>
<div class="breadcrumb-wrapper">
  <div class="container"><nav><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
    <li class="breadcrumb-item active"><?= e($page['title']) ?></li>
  </ol></nav></div>
</div>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <article class="content-wrapper" data-aos="fade-up">
          <?= $page['content'] ?>
        </article>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
