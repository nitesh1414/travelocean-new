<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { die('Invalid CSRF token.'); }
    $data = [
        'client_name'  => trim($_POST['client_name']),
        'client_role'  => trim($_POST['client_role']),
        'client_country'=>trim($_POST['client_country']),
        'rating'       => max(1, min(5, (int)$_POST['rating'])),
        'message'      => trim($_POST['message']),
        'sort_order'   => (int)$_POST['sort_order'],
        'status'       => (int)!empty($_POST['status']),
    ];
    $img = upload_file('client_photo');
    if ($img) $data['client_photo'] = $img;

    if ($id) DB::update('testimonials', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('testimonials', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Testimonial saved.'];
    header('Location: testimonials.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('testimonials', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Testimonial deleted.'];
    header('Location: testimonials.php'); exit;
}

$t = null;
if ($id && in_array($action, ['edit','new'])) $t = DB::fetch('SELECT * FROM testimonials WHERE id = ?', [$id]);
$rows = DB::fetchAll('SELECT * FROM testimonials ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage client testimonials displayed on the website.</p>
    <a href="testimonials.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Testimonial</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Client</th><th>Country</th><th>Rating</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><strong><?= e($r['client_name']) ?></strong><br><small class="text-muted"><?= e($r['client_role']) ?></small></td>
            <td><?= e($r['client_country']) ?></td>
            <td>
              <?php for ($i=0; $i<(int)$r['rating']; $i++): ?><i class="bi bi-star-fill text-warning"></i><?php endfor; ?>
            </td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="text-end">
              <a href="testimonials.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="testimonials.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Testimonial</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Client Name *</label><input type="text" name="client_name" required class="form-control" value="<?= e($t['client_name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Role / Title</label><input type="text" name="client_role" class="form-control" value="<?= e($t['client_role'] ?? '') ?>" placeholder="Software Engineer"></div>
        <div class="col-md-4"><label class="form-label">Country</label><input type="text" name="client_country" class="form-control" value="<?= e($t['client_country'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Rating (1–5)</label><input type="number" min="1" max="5" name="rating" class="form-control" value="<?= e($t['rating'] ?? 5) ?>"></div>
        <div class="col-md-4"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($t['sort_order'] ?? 0) ?>"></div>
        <div class="col-12"><label class="form-label">Message *</label><textarea name="message" rows="4" required class="form-control"><?= e($t['message'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="client_photo" class="form-control" accept="image/*"><?php if (!empty($t['client_photo'])): ?><small class="text-muted">Current: <?= e($t['client_photo']) ?></small><?php endif; ?></div>
        <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($t) || $t['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save</button> <a href="testimonials.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Testimonials';
require __DIR__ . '/includes/auth.php';
