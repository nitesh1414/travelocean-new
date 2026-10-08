<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$tab = $_GET['tab'] ?? 'reasons';

// Reasons (Why choose Portugal)
if ($tab === 'reasons') {
    $action = $_GET['action'] ?? 'list';
    $id     = (int)($_GET['id'] ?? 0);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
        $data = [
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'icon'        => trim($_POST['icon'] ?: 'bi-geo-alt'),
            'sort_order'  => (int)$_POST['sort_order'],
        ];
        if ($id) DB::update('portugal_reasons', $data, 'id = :id', [':id' => $id]);
        else     DB::insert('portugal_reasons', $data);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Saved.'];
        header('Location: portugal.php?tab=reasons'); exit;
    }
    if ($action === 'delete' && $id) {
        DB::delete('portugal_reasons', 'id = :id', [':id' => $id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted.'];
        header('Location: portugal.php?tab=reasons'); exit;
    }
    $r = null;
    if ($id && in_array($action,['edit','new'])) $r = DB::fetch('SELECT * FROM portugal_reasons WHERE id = ?',[$id]);
    $rows = DB::fetchAll('SELECT * FROM portugal_reasons ORDER BY sort_order ASC');
}
// Things to do
elseif ($tab === 'things') {
    $action = $_GET['action'] ?? 'list';
    $id     = (int)($_GET['id'] ?? 0);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
        $data = [
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'icon'        => trim($_POST['icon'] ?: 'bi-camera'),
            'sort_order'  => (int)$_POST['sort_order'],
        ];
        if ($id) DB::update('portugal_things', $data, 'id = :id', [':id' => $id]);
        else     DB::insert('portugal_things', $data);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Saved.'];
        header('Location: portugal.php?tab=things'); exit;
    }
    if ($action === 'delete' && $id) {
        DB::delete('portugal_things', 'id = :id', [':id' => $id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted.'];
        header('Location: portugal.php?tab=things'); exit;
    }
    $r = null;
    if ($id && in_array($action,['edit','new'])) $r = DB::fetch('SELECT * FROM portugal_things WHERE id = ?',[$id]);
    $rows = DB::fetchAll('SELECT * FROM portugal_things ORDER BY sort_order ASC');
}

ob_start();
?>
<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link <?= $tab==='reasons'?'active':'' ?>" href="?tab=reasons"><i class="bi bi-geo-alt me-1"></i> Why Choose Portugal</a></li>
  <li class="nav-item"><a class="nav-link <?= $tab==='things'?'active':'' ?>" href="?tab=things"><i class="bi bi-camera me-1"></i> Things to Do</a></li>
</ul>

<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage Portugal destination content.</p>
    <a href="?tab=<?= e($tab) ?>&action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Item</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Title</th><th>Order</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><i class="bi <?= e($row['icon']) ?> text-primary me-2"></i><strong><?= e($row['title']) ?></strong><br><small class="text-muted"><?= e(truncate($row['description'], 90)) ?></small></td>
            <td><?= (int)$row['sort_order'] ?></td>
            <td class="text-end">
              <a href="?tab=<?= e($tab) ?>&action=edit&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="?tab=<?= e($tab) ?>&action=delete&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Item</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($r['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($r['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-6"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($r['icon'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= e($r['description'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="?tab=<?= e($tab) ?>" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Portugal Content';
require __DIR__ . '/includes/auth.php';
