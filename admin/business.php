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
        'icon'        => trim($_POST['icon'] ?: 'bi-briefcase'),
        'sort_order'  => (int)$_POST['sort_order'],
        'status'      => (int)!empty($_POST['status']),
    ];
    if ($id) DB::update('business_services', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('business_services', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Business service saved.'];
    header('Location: business.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('business_services', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted.'];
    header('Location: business.php'); exit;
}

$item = null;
if ($id && in_array($action, ['edit','new'])) {
    $item = DB::fetch('SELECT * FROM business_services WHERE id = ?', [$id]);
}
$rows = DB::fetchAll('SELECT * FROM business_services ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage Business Formation services shown on the Business page.</p>
    <a href="business.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Item</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Title</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><i class="bi <?= e($r['icon']) ?> text-primary me-2"></i><strong><?= e($r['title']) ?></strong><br><small class="text-muted"><?= e(truncate($r['description'], 80)) ?></small></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="text-end">
              <a href="business.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="business.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Business Service</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($item['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($item['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-6"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($item['icon'] ?? 'bi-briefcase') ?>"></div>
        <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($item) || $item['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= e($item['description'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="business.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Business Services';
require __DIR__ . '/includes/auth.php';
