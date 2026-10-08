<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { die('Invalid CSRF token.'); }
    $data = [
        'title'       => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'icon'        => trim($_POST['icon'] ?: 'bi-check-circle'),
        'sort_order'  => (int)$_POST['sort_order'],
    ];
    if ($id) DB::update('why_choose', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('why_choose', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Saved.'];
    header('Location: why.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('why_choose', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted.'];
    header('Location: why.php'); exit;
}

$r = null;
if ($id && in_array($action, ['edit','new'])) $r = DB::fetch('SELECT * FROM why_choose WHERE id = ?', [$id]);
$rows = DB::fetchAll('SELECT * FROM why_choose ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Reasons shown in the "Why Choose Us" sections.</p>
    <a href="why.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Reason</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Title</th><th>Icon</th><th>Order</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><i class="bi <?= e($r['icon']) ?> text-primary me-2"></i><strong><?= e($r['title']) ?></strong><br><small class="text-muted"><?= e(truncate($r['description'], 80)) ?></small></td>
            <td><code><?= e($r['icon']) ?></code></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td class="text-end">
              <a href="why.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="why.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Reason</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($r['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($r['icon'] ?? 'bi-check-circle') ?>"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= e($r['description'] ?? '') ?></textarea></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($r['sort_order'] ?? 0) ?>"></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="why.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Why Choose Us';
require __DIR__ . '/includes/auth.php';
