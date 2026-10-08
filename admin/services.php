<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { die('Invalid CSRF token.'); }
    $data = [
        'title'             => trim($_POST['title']),
        'slug'              => slugify($_POST['slug'] ?: $_POST['title']),
        'short_description' => trim($_POST['short_description']),
        'full_description'  => trim($_POST['full_description']),
        'icon'              => trim($_POST['icon'] ?: 'bi-briefcase'),
        'color'             => trim($_POST['color'] ?: '#0d6efd'),
        'sort_order'        => (int)$_POST['sort_order'],
        'show_on_home'      => (int)!empty($_POST['show_on_home']),
        'status'            => (int)!empty($_POST['status']),
        'button_text'       => trim($_POST['button_text'] ?: 'Learn More'),
        'button_link'       => trim($_POST['button_link'] ?: '#'),
    ];
    $img = upload_file('image');
    if ($img) $data['image'] = $img;

    if ($id) {
        DB::update('services', $data, 'id = :id', [':id' => $id]);
        DB::delete('service_features', 'service_id = :id', [':id' => $id]);
    } else {
        $id = DB::insert('services', $data);
    }

    $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
    foreach ($features as $i => $f) {
        DB::insert('service_features', ['service_id' => $id, 'feature_text' => $f, 'sort_order' => $i]);
    }

    $_SESSION['flash'] = ['type'=>'success','msg'=>'Service saved successfully.'];
    header('Location: services.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('services', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Service deleted.'];
    header('Location: services.php'); exit;
}

$service = null;
if (($action === 'edit' || $action === 'new') && $id) {
    $service = DB::fetch('SELECT * FROM services WHERE id = ?', [$id]);
    if ($service) {
        $service['features'] = implode("\n", array_column(get_service_features($id), 'feature_text'));
    }
}

$services = DB::fetchAll('SELECT * FROM services ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage the services displayed across the website.</p>
    <a href="services.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Service</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Service</th><th>Slug</th><th>Order</th><th>Home</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><?= (int)$s['id'] ?></td>
            <td><i class="bi <?= e($s['icon']) ?> text-primary me-2"></i><strong><?= e($s['title']) ?></strong><br><small class="text-muted"><?= e(truncate($s['short_description'], 80)) ?></small></td>
            <td><code><?= e($s['slug']) ?></code></td>
            <td><?= (int)$s['sort_order'] ?></td>
            <td><?= $s['show_on_home'] ? '<span class="badge bg-info">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
            <td><?= status_badge($s['status']) ?></td>
            <td class="text-end">
              <a href="services.php?action=edit&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="services.php?action=delete&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this service?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Service</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-7"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($service['title'] ?? '') ?>"></div>
        <div class="col-md-5"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($service['slug'] ?? '') ?>" placeholder="auto-from-title"></div>
        <div class="col-md-4"><label class="form-label">Icon (Bootstrap Icons)</label><input type="text" name="icon" class="form-control" value="<?= e($service['icon'] ?? 'bi-briefcase') ?>" placeholder="bi-airplane"></div>
        <div class="col-md-4"><label class="form-label">Color</label><input type="color" name="color" class="form-control form-control-color" value="<?= e($service['color'] ?? '#0d6efd') ?>"></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($service['sort_order'] ?? 0) ?>"></div>
        <div class="col-12"><label class="form-label">Short Description</label><textarea name="short_description" rows="2" class="form-control"><?= e($service['short_description'] ?? '') ?></textarea></div>
        <div class="col-12"><label class="form-label">Full Description</label><textarea name="full_description" rows="4" class="form-control"><?= e($service['full_description'] ?? '') ?></textarea></div>
        <div class="col-md-6">
          <label class="form-label">Button Text</label>
          <input type="text" name="button_text" class="form-control" value="<?= e($service['button_text'] ?? 'Enquire About This Service') ?>" placeholder="Enquire About This Service">
        </div>
        <div class="col-md-6">
          <label class="form-label">Button Link</label>
          <input type="text" name="button_link" class="form-control" value="<?= e($service['button_link'] ?? 'contact.php') ?>" placeholder="contact.php">
        </div>
        <div class="col-12"><label class="form-label">Features (one per line)</label><textarea name="features" rows="6" class="form-control" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><?= e($service['features'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"><?php if (!empty($service['image'])): ?><small class="text-muted d-block mt-1">Current: <?= e($service['image']) ?></small><?php endif; ?></div>
        <div class="col-md-6 d-flex align-items-center gap-4 flex-wrap">
          <div class="form-check form-switch"><input type="checkbox" name="show_on_home" value="1" class="form-check-input" id="home" <?= !empty($service['show_on_home']) ? 'checked' : '' ?>><label class="form-check-label" for="home">Show on Home</label></div>
          <div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($service) || $service['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Service</button>
          <a href="services.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Services';
require __DIR__ . '/includes/auth.php';
