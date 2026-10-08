<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    if (!empty($_POST['category_mode'])) {
        $data = [
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'icon'        => trim($_POST['icon'] ?: 'bi-file-earmark-text'),
            'sort_order'  => (int)$_POST['sort_order'],
        ];
        if ($id) DB::update('documentation_categories', $data, 'id = :id', [':id' => $id]);
        else     DB::insert('documentation_categories', $data);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Category saved.'];
        header('Location: documentation.php'); exit;
    } else {
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'sort_order'  => (int)$_POST['sort_order'],
        ];
        if ($id) DB::update('documentation_items', $data, 'id = :id', [':id' => $id]);
        else     DB::insert('documentation_items', $data);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Item saved.'];
        header('Location: documentation.php'); exit;
    }
}

if ($action === 'delete' && $id) {
    if (!empty($_GET['type']) && $_GET['type'] === 'cat') {
        DB::delete('documentation_categories', 'id = :id', [':id' => $id]);
    } else {
        DB::delete('documentation_items', 'id = :id', [':id' => $id]);
    }
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted.'];
    header('Location: documentation.php'); exit;
}

$cats = get_doc_categories();
$rows = DB::fetchAll('SELECT i.*, c.title AS cat_title FROM documentation_items i LEFT JOIN documentation_categories c ON c.id = i.category_id ORDER BY i.category_id, i.sort_order');
$item = null;
if ($id && in_array($action, ['edit','new'])) $item = DB::fetch('SELECT * FROM documentation_items WHERE id = ?', [$id]);
$cat = null;
if ($id && $action === 'edit-cat') $cat = DB::fetch('SELECT * FROM documentation_categories WHERE id = ?', [$id]);

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0">Manage documentation categories and items.</p>
    <div>
      <a href="documentation.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Item</a>
      <a href="documentation.php?action=new-cat" class="btn btn-outline-primary"><i class="bi bi-folder-plus me-1"></i> Add Category</a>
    </div>
  </div>
  <div class="row g-3">
    <?php foreach ($cats as $c): ?>
      <div class="col-lg-6">
        <div class="data-table p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0"><i class="bi <?= e($c['icon']) ?> text-primary me-2"></i><?= e($c['title']) ?></h5>
            <div>
              <a href="documentation.php?action=edit-cat&id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="documentation.php?action=delete&id=<?= (int)$c['id'] ?>&type=cat" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete category and its items?')"><i class="bi bi-trash"></i></a>
            </div>
          </div>
          <ul class="list-group list-group-flush">
            <?php foreach ($rows as $r): if ($r['category_id'] == $c['id']): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span><i class="bi bi-check-circle text-success me-2"></i><?= e($r['title']) ?></span>
                <span>
                  <a href="documentation.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                  <a href="documentation.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                </span>
              </li>
            <?php endif; endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php elseif ($action === 'new-cat' || $action === 'edit-cat'): ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Documentation Category</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="category_mode" value="1">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($cat['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($cat['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-6"><label class="form-label">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($cat['icon'] ?? 'bi-file-earmark-text') ?>"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= e($cat['description'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="documentation.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Documentation Item</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($item['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <?php foreach ($cats as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= (!empty($item) && $item['category_id'] == $c['id']) ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"><?= e($item['description'] ?? '') ?></textarea></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($item['sort_order'] ?? 0) ?>"></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="documentation.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Documentation';
require __DIR__ . '/includes/auth.php';
