<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$counts = [
    'slides'        => (int)DB::fetchColumn('SELECT COUNT(*) FROM slides'),
    'services'      => (int)DB::fetchColumn('SELECT COUNT(*) FROM services'),
    'programs'      => (int)DB::fetchColumn('SELECT COUNT(*) FROM programs'),
    'testimonials'  => (int)DB::fetchColumn('SELECT COUNT(*) FROM testimonials'),
    'messages'      => (int)DB::fetchColumn('SELECT COUNT(*) FROM contact_messages'),
    'unread'        => (int)DB::fetchColumn('SELECT COUNT(*) FROM contact_messages WHERE is_read=0'),
    'subscribers'   => (int)DB::fetchColumn('SELECT COUNT(*) FROM subscribers'),
    'visa'          => (int)DB::fetchColumn('SELECT COUNT(*) FROM visa_assessments'),
    'visa_unread'   => (int)DB::fetchColumn('SELECT COUNT(*) FROM visa_assessments WHERE is_read=0'),
];
$recent_messages = DB::fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5');

ob_start();
?>
<div class="row g-3 mb-4">
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(10,77,140,0.1);color:var(--primary);"><i class="bi bi-images"></i></div><div><span class="value"><?= $counts['slides'] ?></span><span class="label">Hero Slides</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(25,135,84,0.1);color:#198754;"><i class="bi bi-briefcase"></i></div><div><span class="value"><?= $counts['services'] ?></span><span class="label">Services</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(245,166,35,0.15);color:#f5a623;"><i class="bi bi-mortarboard"></i></div><div><span class="value"><?= $counts['programs'] ?></span><span class="label">Study Programs</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(13,110,253,0.1);color:#0d6efd;"><i class="bi bi-chat-quote"></i></div><div><span class="value"><?= $counts['testimonials'] ?></span><span class="label">Testimonials</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(220,53,69,0.1);color:#dc3545;"><i class="bi bi-envelope"></i></div><div><span class="value"><?= $counts['messages'] ?> <small class="text-danger">(<?= $counts['unread'] ?> new)</small></span><span class="label">Messages</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(102,16,242,0.1);color:#6610f2;"><i class="bi bi-people"></i></div><div><span class="value"><?= $counts['subscribers'] ?></span><span class="label">Subscribers</span></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="stat-card"><div class="icon-bg" style="background:rgba(245,166,35,0.15);color:#f5a623;"><i class="bi bi-clipboard-check"></i></div><div><span class="value"><?= $counts['visa'] ?> <small class="text-danger"><?= $counts['visa_unread'] ?> new</small></span><span class="label">Visa Assessments</span></div></div></div>
  <div class="col-md-6 col-sm-12"><div class="stat-card"><div class="icon-bg" style="background:rgba(0,179,164,0.1);color:#00b3a4;"><i class="bi bi-globe"></i></div><div><span class="value"><?= e(setting('site_name')) ?></span><span class="label">Site is live · <?= e(setting('site_url')) ?></span></div></div></div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="form-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-envelope me-2"></i> Recent Messages</h5>
        <a href="messages.php" class="btn btn-sm btn-outline-primary">View all</a>
      </div>
      <?php if (!$recent_messages): ?>
        <p class="text-muted mb-0">No messages yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead><tr><th>Name</th><th>Subject</th><th>Date</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($recent_messages as $m): ?>
                <tr>
                  <td><?= e($m['name']) ?><br><small class="text-muted"><?= e($m['email']) ?></small></td>
                  <td><?= e(truncate($m['subject'] ?: $m['message'], 50)) ?></td>
                  <td><small><?= fmt_date($m['created_at'], 'M d, Y') ?></small></td>
                  <td><a href="messages.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="form-card">
      <h5 class="mb-3"><i class="bi bi-lightning me-2"></i> Quick Actions</h5>
      <div class="d-grid gap-2">
        <a href="slides.php?action=new" class="btn btn-outline-primary text-start"><i class="bi bi-plus-circle me-2"></i> Add Hero Slide</a>
        <a href="services.php?action=new" class="btn btn-outline-primary text-start"><i class="bi bi-plus-circle me-2"></i> Add Service</a>
        <a href="testimonials.php?action=new" class="btn btn-outline-primary text-start"><i class="bi bi-plus-circle me-2"></i> Add Testimonial</a>
        <a href="settings.php" class="btn btn-outline-primary text-start"><i class="bi bi-sliders me-2"></i> Site Settings</a>
        <a href="<?= SITE_URL ?>/index.php" target="_blank" class="btn btn-outline-secondary text-start"><i class="bi bi-box-arrow-up-right me-2"></i> View Website</a>
      </div>
    </div>
  </div>
</div>

<?php
$admin_content = ob_get_clean();
$admin_page_title = 'Dashboard';
require __DIR__ . '/includes/auth.php';
