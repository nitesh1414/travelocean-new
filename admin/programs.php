<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { die('Invalid CSRF token.'); }
    $data = [
        'slug'             => slugify($_POST['slug'] ?: $_POST['title']),
        'country'          => trim($_POST['country']),
        'city'             => trim($_POST['city']),
        'title'            => trim($_POST['title']),
        'tagline'          => trim($_POST['tagline']),
        'description'      => trim($_POST['description']),
        'flag_emoji'       => trim($_POST['flag_emoji']),
        'accent_color'     => trim($_POST['accent_color'] ?: '#0d6efd'),
        'meta_title'       => trim($_POST['meta_title']),
        'meta_description' => trim($_POST['meta_description']),
        'sort_order'       => (int)$_POST['sort_order'],
        'status'           => (int)!empty($_POST['status']),
    ];
    $img = upload_file('hero_image');
    if ($img) $data['hero_image'] = $img;

    if ($id) {
        DB::update('programs', $data, 'id = :id', [':id' => $id]);
        DB::delete('program_highlights', 'program_id = :id', [':id' => $id]);
        DB::delete('program_details', 'program_id = :id', [':id' => $id]);
    } else {
        $id = DB::insert('programs', $data);
    }

    // Highlights
    $h_texts = array_filter(array_map('trim', explode("\n", $_POST['highlights'] ?? '')));
    foreach (array_values($h_texts) as $i => $t) {
        DB::insert('program_highlights', [
            'program_id' => $id, 'feature_text' => $t,
            'icon' => 'bi-check2-circle', 'sort_order' => $i,
        ]);
    }
    // Details (key|value|icon per line)
    $lines = array_filter(array_map('trim', explode("\n", $_POST['details'] ?? '')));
    foreach (array_values($lines) as $i => $line) {
        $parts = array_map('trim', explode('|', $line, 3));
        if (count($parts) >= 2 && $parts[0] !== '' && $parts[1] !== '') {
            DB::insert('program_details', [
                'program_id'   => $id,
                'detail_key'   => $parts[0],
                'detail_value' => $parts[1],
                'icon'         => $parts[2] ?? 'bi-info-circle',
                'sort_order'   => $i,
            ]);
        }
    }

    $_SESSION['flash'] = ['type'=>'success','msg'=>'Program saved successfully.'];
    header('Location: programs.php'); exit;
}

if ($action === 'delete' && $id) {
    DB::delete('programs', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Program deleted.'];
    header('Location: programs.php'); exit;
}

$program = null;
if ($id && in_array($action, ['edit','new'])) {
    $program = DB::fetch('SELECT * FROM programs WHERE id = ?', [$id]);
    if ($program) {
        $program['highlights'] = implode("\n", array_column(get_program_highlights($id), 'feature_text'));
        $det_lines = [];
        foreach (get_program_details($id) as $d) {
            $det_lines[] = $d['detail_key'] . ' | ' . $d['detail_value'] . ' | ' . $d['icon'];
        }
        $program['details'] = implode("\n", $det_lines);
    }
}

$rows = DB::fetchAll('SELECT * FROM programs ORDER BY sort_order ASC');

ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage Study Abroad programs (Spain, Germany, Berlin…).</p>
    <a href="programs.php?action=new" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Program</a>
  </div>
  <div class="data-table">
    <table class="table">
      <thead><tr><th>#</th><th>Program</th><th>Country</th><th>Slug</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= e($r['flag_emoji']) ?> <strong><?= e($r['title']) ?></strong><br><small class="text-muted"><?= e(truncate($r['tagline'], 60)) ?></small></td>
            <td><?= e($r['country']) ?><?= $r['city'] ? ' · '.e($r['city']) : '' ?></td>
            <td><code><?= e($r['slug']) ?></code></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="text-end">
              <a href="programs.php?action=edit&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <a href="programs.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="form-card">
    <h4 class="mb-3"><?= $id ? 'Edit' : 'New' ?> Study Program</h4>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= e($program['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($program['slug'] ?? '') ?>" placeholder="auto"></div>
        <div class="col-md-4"><label class="form-label">Country *</label><input type="text" name="country" class="form-control" required value="<?= e($program['country'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= e($program['city'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Flag Emoji</label><input type="text" name="flag_emoji" class="form-control" value="<?= e($program['flag_emoji'] ?? '') ?>" placeholder="🇪🇸"></div>
        <div class="col-md-6"><label class="form-label">Tagline</label><input type="text" name="tagline" class="form-control" value="<?= e($program['tagline'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Accent Color</label><input type="color" name="accent_color" class="form-control form-control-color" value="<?= e($program['accent_color'] ?? '#0d6efd') ?>"></div>
        <div class="col-md-2"><label class="form-label">Order</label><input type="number" name="sort_order" class="form-control" value="<?= e($program['sort_order'] ?? 0) ?>"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="4" class="form-control"><?= e($program['description'] ?? '') ?></textarea></div>
        <div class="col-12"><label class="form-label">Highlights (one per line)</label><textarea name="highlights" rows="6" class="form-control" placeholder="Top Universities&#10;Affordable Tuition&#10;Post-Study Work Opportunities"><?= e($program['highlights'] ?? '') ?></textarea>
          <small class="text-muted">Each line becomes a highlight card.</small></div>
        <div class="col-12"><label class="form-label">Details (Format: Key | Value | Icon)</label><textarea name="details" rows="6" class="form-control" placeholder="Tuition | Affordable | bi-cash&#10;Duration | Bachelor: 3–4 Years | bi-calendar3"><?= e($program['details'] ?? '') ?></textarea>
          <small class="text-muted">Format: <code>Key | Value | Icon</code> per line. Icon optional.</small></div>
        <div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" name="hero_image" class="form-control" accept="image/*"><?php if (!empty($program['hero_image'])): ?><small class="text-muted">Current: <?= e($program['hero_image']) ?></small><?php endif; ?></div>
        <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch"><input type="checkbox" name="status" value="1" class="form-check-input" id="status" <?= empty($program) || $program['status'] ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div></div>
        <div class="col-12"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= e($program['meta_title'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Meta Description</label><textarea name="meta_description" rows="2" class="form-control"><?= e($program['meta_description'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i> Save Program</button> <a href="programs.php" class="btn btn-outline-secondary">Cancel</a></div>
      </div>
    </form>
  </div>
<?php endif;
$admin_content = ob_get_clean();
$admin_page_title = 'Study Programs';
require __DIR__ . '/includes/auth.php';
