<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_slug = 'training';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');

    // Save all editable content blocks
    $editable = ['hero_title','hero_subtitle','intro_text',
                 'stat_1_number','stat_1_label',
                 'stat_2_number','stat_2_label',
                 'stat_3_label','stat_4_label',
                 'partners_title','partners_subtitle','partners_description',
                 'important_update_title','important_update_text'];

    foreach ($editable as $key) {
        if (isset($_POST[$key])) {
            save_page_block($page_slug, $key, trim($_POST[$key]));
        }
    }
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Training page content saved.'];
    header('Location: training.php'); exit;
}

$blocks = get_page_blocks($page_slug);
$partners = get_partners();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <p class="text-muted mb-0">Edit the content displayed on <code>/training.php</code>.</p>
    <small class="text-muted">Partners are managed separately under <a href="partners.php">Partners</a>.</small>
  </div>
  <a href="<?= SITE_URL ?>/training.php" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right me-1"></i> Preview Page</a>
</div>

<form method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <!-- Hero / Intro -->
  <div class="form-card mb-4">
    <h5 class="mb-3"><i class="bi bi-stars me-2 text-primary"></i> Hero Section</h5>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Hero Title</label><input type="text" name="hero_title" class="form-control" value="<?= e($blocks['hero_title'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Hero Subtitle</label><input type="text" name="hero_subtitle" class="form-control" value="<?= e($blocks['hero_subtitle'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Intro Paragraph</label><textarea name="intro_text" rows="6" class="form-control"><?= e($blocks['intro_text'] ?? '') ?></textarea></div>
    </div>
  </div>

  <!-- Stats -->
  <div class="form-card mb-4">
    <h5 class="mb-3"><i class="bi bi-graph-up me-2 text-primary"></i> Stats / Highlights</h5>
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">Stat 1 Number</label><input type="text" name="stat_1_number" class="form-control" value="<?= e($blocks['stat_1_number'] ?? '25') ?>"></div>
      <div class="col-md-3"><label class="form-label">Stat 1 Label</label><input type="text" name="stat_1_label" class="form-control" value="<?= e($blocks['stat_1_label'] ?? '') ?>"></div>
      <div class="col-md-3"><label class="form-label">Stat 2 Number</label><input type="text" name="stat_2_number" class="form-control" value="<?= e($blocks['stat_2_number'] ?? '100%') ?>"></div>
      <div class="col-md-3"><label class="form-label">Stat 2 Label</label><input type="text" name="stat_2_label" class="form-control" value="<?= e($blocks['stat_2_label'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Stat 3 Label</label><input type="text" name="stat_3_label" class="form-control" value="<?= e($blocks['stat_3_label'] ?? '') ?>" placeholder="e.g. Faster & Reliable Execution"></div>
      <div class="col-md-6"><label class="form-label">Stat 4 Label</label><input type="text" name="stat_4_label" class="form-control" value="<?= e($blocks['stat_4_label'] ?? '') ?>" placeholder="e.g. Accurate & Expert Advice"></div>
    </div>
  </div>

  <!-- Partners section -->
  <div class="form-card mb-4">
    <h5 class="mb-3"><i class="bi bi-people me-2 text-primary"></i> Partners Section</h5>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Section Title</label><input type="text" name="partners_title" class="form-control" value="<?= e($blocks['partners_title'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Section Subtitle</label><input type="text" name="partners_subtitle" class="form-control" value="<?= e($blocks['partners_subtitle'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Section Description</label><textarea name="partners_description" rows="3" class="form-control"><?= e($blocks['partners_description'] ?? '') ?></textarea></div>
    </div>
    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="bi bi-building me-1"></i> Partners (<?= count($partners) ?>)</h6>
      <a href="partners.php?action=new" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-circle me-1"></i> Manage Partners</a>
    </div>
    <div class="row g-2">
      <?php foreach ($partners as $p): ?>
        <div class="col-md-4">
          <div class="border rounded p-2 small d-flex align-items-center">
            <i class="bi bi-building me-2 text-primary"></i>
            <div class="flex-grow-1"><?= e($p['name']) ?> <small class="text-muted">· <?= e($p['location']) ?></small></div>
            <a href="partners.php?action=edit&id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-link p-0"><i class="bi bi-pencil"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Important update / news -->
  <div class="form-card mb-4">
    <h5 class="mb-3"><i class="bi bi-megaphone me-2 text-warning"></i> Important Update / Notice</h5>
    <div class="row g-3">
      <div class="col-12"><label class="form-label">Title</label><input type="text" name="important_update_title" class="form-control" value="<?= e($blocks['important_update_title'] ?? '') ?>"></div>
      <div class="col-12"><label class="form-label">Text</label><textarea name="important_update_text" rows="4" class="form-control"><?= e($blocks['important_update_text'] ?? '') ?></textarea></div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-1"></i> Save Training Page Content</button>
</form>
<?php
$admin_content = ob_get_clean();
$admin_page_title = 'Training Page (CMS)';
require __DIR__ . '/includes/auth.php';
