<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (($_GET['action'] ?? '') === 'delete' && $id) {
    DB::delete('contact_messages', 'id = :id', [':id' => $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Message deleted.'];
    header('Location: messages.php'); exit;
}
$m = null;
if ($id && ($_GET['action'] ?? '') !== 'delete') {
    $m = DB::fetch('SELECT * FROM contact_messages WHERE id = ?', [$id]);
    if ($m) DB::update('contact_messages', ['is_read' => 1], 'id = :id', [':id' => $id]);
}

$messages = DB::fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC');

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0">All contact form submissions.</p>
  <span class="badge bg-warning text-dark"><?= (int)DB::fetchColumn('SELECT COUNT(*) FROM contact_messages WHERE is_read=0') ?> unread</span>
</div>

<?php if ($m && $_GET['id'] ?? 0): ?>
  <div class="form-card mb-3">
    <a href="messages.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to messages</a>
    <h4 class="mb-2"><?= e($m['subject'] ?: '(No subject)') ?></h4>
    <p class="text-muted mb-3"><i class="bi bi-person me-1"></i> <?= e($m['name']) ?> · <i class="bi bi-envelope me-1"></i> <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a> · <i class="bi bi-telephone me-1"></i> <?= e($m['phone']) ?> · <i class="bi bi-calendar me-1"></i> <?= fmt_date($m['created_at']) ?></p>
    <?php if ($m['service_interest']): ?>
      <p><strong>Service:</strong> <span class="badge bg-info"><?= e($m['service_interest']) ?></span></p>
    <?php endif; ?>
    <div class="p-3 rounded" style="background:#f8fafc;"><?= nl2br(e($m['message'])) ?></div>
    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-primary" href="mailto:<?= e($m['email']) ?>?subject=Re: <?= e($m['subject'] ?: 'Your enquiry') ?>"><i class="bi bi-reply"></i> Reply by Email</a>
      <a class="btn btn-success" href="https://wa.me/<?= preg_replace('/[^0-9]/','',$m['phone']) ?>" target="_blank"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <a class="btn btn-outline-danger ms-auto" href="messages.php?action=delete&id=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this message?')"><i class="bi bi-trash"></i> Delete</a>
    </div>
  </div>
<?php endif; ?>

<div class="data-table">
  <table class="table">
    <thead><tr><th>Status</th><th>Name</th><th>Subject</th><th>Service</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php if (!$messages): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No messages yet.</td></tr>
      <?php else: foreach ($messages as $msg): ?>
        <tr class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
          <td>
            <?php if (!$msg['is_read']): ?>
              <span class="badge bg-warning text-dark">New</span>
            <?php else: ?>
              <span class="badge bg-success">Read</span>
            <?php endif; ?>
          </td>
          <td><?= e($msg['name']) ?><br><small class="text-muted"><?= e($msg['email']) ?></small></td>
          <td><?= e(truncate($msg['subject'] ?: $msg['message'], 60)) ?></td>
          <td><?php if ($msg['service_interest']): ?><span class="badge bg-info"><?= e($msg['service_interest']) ?></span><?php endif; ?></td>
          <td><small><?= fmt_date($msg['created_at'], 'M d, Y H:i') ?></small></td>
          <td class="text-end">
            <a href="messages.php?id=<?= (int)$msg['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            <a href="messages.php?action=delete&id=<?= (int)$msg['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php
$admin_content = ob_get_clean();
$admin_page_title = 'Contact Messages';
require __DIR__ . '/includes/auth.php';
