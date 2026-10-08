<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if (($_GET['action'] ?? '') === 'delete' && ($id = (int)$_GET['id'])) {
    DB::delete('subscribers', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Subscriber removed.'];
    header('Location: subscribers.php'); exit;
}

$rows = DB::fetchAll('SELECT * FROM subscribers ORDER BY created_at DESC');

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0">Newsletter subscribers (<?= count($rows) ?> total).</p>
</div>
<div class="data-table">
  <table class="table">
    <thead><tr><th>#</th><th>Email</th><th>Status</th><th>Subscribed</th><th></th></tr></thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No subscribers yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= e($r['email']) ?></td>
          <td><?= $r['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
          <td><small><?= fmt_date($r['created_at']) ?></small></td>
          <td class="text-end"><a href="subscribers.php?action=delete&id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove?')"><i class="bi bi-trash"></i></a></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php
$admin_content = ob_get_clean();
$admin_page_title = 'Newsletter Subscribers';
require __DIR__ . '/includes/auth.php';
