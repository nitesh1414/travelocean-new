<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    $data = [
        'slug'             => slugify($_POST['slug'] ?: $_POST['title']),
        'title'            => trim($_POST['title']),
        'content'          => $_POST['content'],
        'meta_description' => trim($_POST['meta_description']),
        'status'           => (int)!empty($_POST['status']),
    ];
    if ($id) DB::update('pages', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('pages', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Page saved.'];
    header('Location: pages.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('pages', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Page deleted.'];
    header('Location: pages.php'); exit;
}

$p = null;
if ($id && in_array($action,['edit','new'])) $p = DB::fetch('SELECT * FROM pages WHERE id = ?',[$id]);
$rows = DB::fetchAll('SELECT * FROM pages ORDER BY id DESC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage static pages (Privacy, Terms, About, etc.).</p>
    <a href="pages.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Page</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><strong><?= e($r['title']) ?></strong></td>
            <td><code><?= e($r['slug']) ?></code></td>
            <td><?= status_badge($r['status']) ?></td>
            <td><small><?= fmt_date($r['updated_at']) ?></small></td>
            <td class="text-end">
              <a href="<?= SITE_URL ?>/page.php?slug=<?= e($r['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
              <a href="pages.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="pages.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Page</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-9"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($p['title'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($p['slug'] ?? '') ?>" placeholder="auto"></div>
        <div class="col-12"><label class="form-label">Meta Description</label><input type="text" name="meta_description" class="form-control" value="<?= e($p['meta_description'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Content (HTML allowed)</label><textarea name="content" rows="14" class="form-control"><?= e($p['content'] ?? '') ?></textarea></div>
        <div class="col-12 d-flex align-items-center gap-3">
          <div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($p) || $p['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div>
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button>
          <a href="pages.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'CMS Pages';
require __DIR__ . '/includes/auth.php';
