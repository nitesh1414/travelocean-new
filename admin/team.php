<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die('Invalid CSRF');
    $data = [
        'name'        => trim($_POST['name']),
        'designation' => trim($_POST['designation']),
        'bio'         => trim($_POST['bio']),
        'email'       => trim($_POST['email']),
        'phone'       => trim($_POST['phone']),
        'linkedin'    => trim($_POST['linkedin']),
        'sort_order'  => (int)$_POST['sort_order'],
        'status'      => (int)!empty($_POST['status']),
    ];
    $img = upload_file('photo');
    if ($img) $data['photo'] = $img;

    if ($id) DB::update('team_members', $data, 'id = :id', [':id' => $id]);
    else     DB::insert('team_members', $data);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Team member saved.'];
    header('Location: team.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('team_members', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Team member removed.'];
    header('Location: team.php'); exit;
}

$m = null;
if ($id && in_array($action, ['edit','new'])) $m = DB::fetch('SELECT * FROM team_members WHERE id = ?', [$id]);
$rows = DB::fetchAll('SELECT * FROM team_members ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage team members displayed on the About page.</p>
    <a href="team.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Team Member</a>
  </div>
  <div class="row g-3">
    <?php if (!$rows): ?>
      <div class="col-12"><div class="form-card text-center text-muted py-4">No team members yet. <a href="team.php?action=new">Add the first one</a>.</div></div>
    <?php else: foreach ($rows as $r): ?>
      <div class="col-lg-4 col-md-6">
        <div class="data-table p-3 text-center">
          <?php if (!empty($r['photo'])): ?>
            <img src="<?= SITE_URL ?>/<?= e($r['photo']) ?>" alt="<?= e($r['name']) ?>" class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;">
          <?php else: ?>
            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width:90px;height:90px;background:var(--gradient-primary);font-size:2rem;font-weight:700;">
              <?= e(strtoupper(substr($r['name'],0,1))) ?>
            </div>
          <?php endif; ?>
          <h5 class="mb-1"><?= e($r['name']) ?></h5>
          <small class="text-primary d-block mb-2"><?= e($r['designation']) ?></small>
          <p class="text-muted small mb-2"><?= e(truncate($r['bio'] ?? '', 90)) ?></p>
          <div class="d-flex justify-content-center gap-2 mt-2">
            <?= $r['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
            <a href="team.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="team.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this team member?')"><i class="bi bi-trash"></i></a>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Team Member</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Full Name *</label><input type="text" name="name" required class="form-control" value="<?= e($m['name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Designation / Title *</label><input type="text" name="designation" required class="form-control" value="<?= e($m['designation'] ?? '') ?>" placeholder="e.g. Founder & CEO"></div>
        <div class="col-12"><label class="form-label">Short Bio</label><textarea name="bio" rows="4" class="form-control" placeholder="A short description about the team member…"><?= e($m['bio'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="photo" accept="image/*" class="form-control">
          <?php if (!empty($m['photo'])): ?><small class="text-muted d-block mt-1">Current: <?= e($m['photo']) ?></small><?php endif; ?>
          <small class="text-muted">Square photo (e.g. 400×400) recommended. If no photo is uploaded, initials are shown.</small>
        </div>
        <div class="col-md-6"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($m['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($m['email'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($m['phone'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">LinkedIn URL</label><input type="url" name="linkedin" class="form-control" value="<?= e($m['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/…"></div>
        <div class="col-12 d-flex align-items-center gap-3">
          <div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($m) || $m['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active (visible on website)</label></div>
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save Team Member</button>
          <a href="team.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Team Members';
require __DIR__ . '/includes/auth.php';
