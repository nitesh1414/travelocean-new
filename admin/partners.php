<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    $data = [
        'name'        => trim($_POST['name']),
        'location'    => trim($_POST['location']),
        'tagline'     => trim($_POST['tagline']),
        'description' => trim($_POST['description']),
        'website_url' => trim($_POST['website_url']),
        'category'    => trim($_POST['category'] ?: 'general'),
        'sort_order'  => (int)$_POST['sort_order'],
        'status'      => (int)!empty($_POST['status']),
    ];
    $img = upload_file('logo');
    if ($img) $data['logo'] = $img;

    if ($id) DB::update('partners', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('partners', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Partner saved.'];
    header('Location: partners.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('partners', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Partner removed.'];
    header('Location: partners.php'); exit;
}

$p = null;
if ($id && in_array($action, ['edit','new'])) $p = DB::fetch('SELECT * FROM partners WHERE id = ?', [$id]);
$rows = DB::fetchAll('SELECT * FROM partners ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage partners displayed on the Training and Partners pages.</p>
    <a href="partners.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Partner</a>
  </div>
  <div class="row g-3">
    <?php if (!$rows): ?>
      <div class="col-12"><div class="form-card text-center text-muted py-4">No partners yet. <a href="partners.php?action=new">Add the first one</a>.</div></div>
    <?php else: foreach ($rows as $r): ?>
      <div class="col-lg-4 col-md-6">
        <div class="data-table p-3 text-center">
          <?php if (!empty($r['logo'])): ?>
            <img src="<?= SITE_URL ?>/<?= e($r['logo']) ?>" alt="<?= e($r['name']) ?>" class="img-fluid mb-3" style="max-height:90px;object-fit:contain;">
          <?php else: ?>
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded" style="width:90px;height:90px;background:var(--gradient-primary);font-size:2rem;">
              <i class="bi bi-building"></i>
            </div>
          <?php endif; ?>
          <h5 class="mb-1"><?= e($r['name']) ?></h5>
          <?php if ($r['location']): ?><small class="text-primary d-block mb-1"><i class="bi bi-geo-alt me-1"></i><?= e($r['location']) ?></small><?php endif; ?>
          <?php if ($r['tagline']): ?><small class="text-muted d-block mb-2"><em><?= e($r['tagline']) ?></em></small><?php endif; ?>
          <p class="text-muted small mb-2"><?= e(truncate($r['description'], 90)) ?></p>
          <span class="badge bg-info"><?= e($r['category']) ?></span>
          <?= $r['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
          <div class="d-flex justify-content-center gap-2 mt-3">
            <a href="partners.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="partners.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this partner?')"><i class="bi bi-trash"></i></a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Partner</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Partner Name *</label><input type="text" name="name" required class="form-control" value="<?= e($p['name'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Category</label>
          <select name="category" class="form-select">
            <?php foreach (['training','language','education','general'] as $cat): ?>
              <option value="<?= $cat ?>" <?= ($p['category'] ?? 'general') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="<?= e($p['location'] ?? '') ?>" placeholder="e.g. Berlin, Germany"></div>
        <div class="col-md-6"><label class="form-label">Website URL</label><input type="url" name="website_url" class="form-control" value="<?= e($p['website_url'] ?? '') ?>" placeholder="https://…"></div>
        <div class="col-12"><label class="form-label">Tagline</label><input type="text" name="tagline" class="form-control" value="<?= e($p['tagline'] ?? '') ?>" placeholder="e.g. Our Trusted Language Education Partner"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="4" class="form-control"><?= e($p['description'] ?? '') ?></textarea></div>
        <div class="col-md-8"><label class="form-label">Logo</label><input type="file" name="logo" accept="image/*" class="form-control">
          <?php if (!empty($p['logo'])): ?><small class="text-muted d-block mt-1">Current: <?= e($p['logo']) ?></small><?php endif; ?>
          <small class="text-muted">PNG with transparent background recommended.</small>
        </div>
        <div class="col-md-2"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($p['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-2 d-flex align-items-end"><div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($p) || $p['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div></div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save Partner</button>
          <a href="partners.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Partners';
require __DIR__ . '/includes/auth.php';
