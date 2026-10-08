<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { die('Invalid CSRF token.'); }
    $data = [
        'title'       => trim($_POST['title']),
        'subtitle'    => trim($_POST['subtitle']),
        'description' => trim($_POST['description']),
        'button_text' => trim($_POST['button_text'] ?: 'Learn More'),
        'button_link' => trim($_POST['button_link'] ?: '#'),
        'sort_order'  => (int)$_POST['sort_order'],
        'status'      => (int)!empty($_POST['status']),
    ];
    $img = upload_file('image');
    if ($img) $data['image'] = $img;

    if ($id) {
        DB::update('slides', $data, 'id = :id', [':id' => $id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Slide updated successfully.'];
    } else {
        DB::insert('slides', $data);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Slide created successfully.'];
    }
    header('Location: slides.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('slides', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Slide deleted.'];
    header('Location: slides.php'); exit;
}

$slide = null;
if (($action === 'edit' || $action === 'new') && $id) {
    $slide = DB::fetch('SELECT * FROM slides WHERE id = ?', [$id]);
}

$slides = DB::fetchAll('SELECT * FROM slides ORDER BY sort_order ASC, id DESC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage homepage hero slider content.</p>
    <a href="slides.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Slide</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Title</th><th>Subtitle</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($slides as $s): ?>
          <tr>
            <td><?= (int)$s['id'] ?></td>
            <td><strong><?= e($s['title']) ?></strong><br><small class="text-muted"><?= e(truncate($s['description'], 80)) ?></small></td>
            <td><?= e(truncate($s['subtitle'], 60)) ?></td>
            <td><?= (int)$s['sort_order'] ?></td>
            <td><?= status_badge($s['status']) ?></td>
            <td class="text-end">
              <a href="slides.php?action=edit&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="slides.php?action=delete&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this slide?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Slide</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" required value="<?= e($slide['title'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="<?= e($slide['sort_order'] ?? 0) ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Subtitle</label>
          <input type="text" name="subtitle" class="form-control" value="<?= e($slide['subtitle'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" rows="3" class="form-control"><?= e($slide['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Button Text</label>
          <input type="text" name="button_text" class="form-control" value="<?= e($slide['button_text'] ?? 'Learn More') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Button Link</label>
          <input type="text" name="button_link" class="form-control" value="<?= e($slide['button_link'] ?? '#') ?>" placeholder="contact.php">
        </div>
        <div class="col-md-6">
          <label class="form-label">Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($slide['image'])): ?>
            <small class="text-muted">Current: <?= e($slide['image']) ?></small>
          <?php endif; ?>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <div class="form-check form-switch">
            <input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($slide) || $slide['status'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="status">Active</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Slide</button>
          <a href="slides.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Hero Slides';
require __DIR__ . '/includes/auth.php';
