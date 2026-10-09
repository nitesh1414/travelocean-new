<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

// Handle delete
if ($action === 'delete' && $id) {
    DB::delete('visa_assessments', 'id = ?', [$id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Assessment deleted.'];
    header('Location: visa_assessments.php'); exit;
}

// Handle status / notes update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_assessment'])) {
    $upd_id = (int)($_POST['id'] ?? 0);
    $new_status = trim($_POST['status'] ?? 'pending');
    $new_notes = trim($_POST['admin_notes'] ?? '');
    if ($upd_id) {
        DB::update('visa_assessments', [
            'status' => $new_status,
            'admin_notes' => $new_notes,
            'is_read' => 1,
        ], 'id = :id', [':id' => $upd_id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Assessment updated successfully.'];
        header('Location: visa_assessments.php?id=' . $upd_id); exit;
    }
}

$record = null;
if ($id && $action !== 'delete') {
    $record = get_visa_assessment_by_id($id);
    if ($record) {
        if (!$record['is_read']) {
            DB::update('visa_assessments', ['is_read' => 1], 'id = :id', [':id' => $id]);
            $record['is_read'] = 1;
        }
    } else {
        $_SESSION['flash'] = ['type'=>'danger','msg'=>'Assessment not found.'];
        header('Location: visa_assessments.php'); exit;
    }
}

$filter_status = $_GET['status'] ?? '';
$assessments = get_visa_assessments(200, $filter_status ?: null);

ob_start();
?>

<?php if (isset($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> <?= e($_SESSION['flash']['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if ($record): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="visa_assessments.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to assessments</a>
    <div class="d-flex gap-2">
      <a href="mailto:<?= e($record['email']) ?>?subject=Re: Visa Assessment #<?= (int)$record['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-envelope"></i> Reply</a>
      <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$record['phone'] ?? '') ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
    </div>
  </div>

  <div class="form-card mb-3">
    <h4 class="mb-3">Assessment #<?= (int)$record['id'] ?> — <span class="badge bg-<?= $record['status'] === 'processed' ? 'success' : ($record['status'] === 'in_review' ? 'info' : 'warning') ?> text-dark"><?= e(ucfirst(str_replace('_',' ',$record['status']))) ?></span></h4>
    <div class="row g-3 small mb-3">
      <div class="col-md-3"><strong>Submitted:</strong> <?= fmt_date($record['created_at'], 'M d, Y H:i') ?></div>
      <div class="col-md-3"><strong>IP:</strong> <?= e($record['ip_address']) ?></div>
      <div class="col-md-3"><strong>Read:</strong> <?= $record['is_read'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' ?></div>
      <div class="col-md-3"><strong>Email:</strong> <a href="mailto:<?= e($record['email']) ?>"><?= e($record['email']) ?></a></div>
    </div>

    <form method="post" action="visa_assessments.php?id=<?= (int)$record['id'] ?>" class="row g-3 mb-3">
      <input type="hidden" name="update_assessment" value="1">
      <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
      <div class="col-md-4">
        <label class="form-label small fw-bold">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="pending" <?= $record['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="in_review" <?= $record['status'] === 'in_review' ? 'selected' : '' ?>>In Review</option>
          <option value="processed" <?= $record['status'] === 'processed' ? 'selected' : '' ?>>Processed</option>
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label small fw-bold">Admin Notes</label>
        <textarea name="admin_notes" class="form-control form-control-sm" rows="2" placeholder="Internal notes, action items, follow-up..."><?= e($record['admin_notes']) ?></textarea>
      </div>
      <div class="col-12"><button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-save"></i> Save Changes</button></div>
    </form>

    <hr>

    <div class="row g-4">
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-person-circle text-primary me-1"></i> Personal &amp; Identity</h5>
        <dl class="small mb-0">
          <dt>Full Name</dt><dd><?= e($record['full_legal_name']) ?></dd>
          <dt>DOB</dt><dd><?= e($record['dob']) ?></dd>
          <dt>Gender</dt><dd><?= e($record['gender']) ?></dd>
          <dt>Birthplace</dt><dd><?= e($record['birthplace_city']) ?>, <?= e($record['birthplace_state']) ?>, <?= e($record['birthplace_country']) ?></dd>
          <dt>Nationality</dt><dd><?= e($record['nationality']) ?></dd>
          <dt>Current Residency</dt><dd><?= e($record['current_residency']) ?></dd>
          <dt>Passport</dt><dd><?= e($record['passport_number']) ?> (<?= e($record['passport_issue']) ?> — <?= e($record['passport_expiry']) ?>)</dd>
          <dt>Current Country / State / City</dt><dd><?= e($record['address_country']) ?> — <?= e($record['address_state']) ?> — <?= e($record['address_city']) ?></dd>
          <dt>Street / Full Address</dt><dd><?= nl2br(e($record['address'])) ?></dd>
          <dt>Phone</dt><dd><?= e($record['phone']) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-people-fill text-primary me-1"></i> Marital &amp; Family</h5>
        <dl class="small mb-0">
          <dt>Marital Status</dt><dd><?= e($record['marital_status']) ?></dd>
          <dt>Spouse / Partner</dt><dd><?= nl2br(e($record['spouse_partner_details'])) ?></dd>
          <dt>Dependent Children</dt><dd><?= (int)$record['dependent_children'] ?></dd>
          <dt>Children Details</dt><dd>
            <?php $kids = !empty($record['children_json']) ? json_decode($record['children_json'], true) : []; if ($kids): ?>
              <ul class="mb-0 ps-1">
              <?php foreach ($kids as $k): ?>
                <li><?= e($k['name']) ?> — Age <?= (int)($k['age'] ?? 0) ?> — <?= e($k['nationality']) ?></li>
              <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <span class="text-muted">None listed</span>
            <?php endif; ?>
          </dd>
          <dt>Immediate family / relatives in destination?</dt><dd><?= e($record['family_target_country']) ?></dd>
          <dt>Details</dt><dd><?= nl2br(e($record['family_target_details'])) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-airplane-fill text-primary me-1"></i> Travel Purpose</h5>
        <dl class="small mb-0">
          <dt>Target Destination Country</dt><dd><?= e($record['target_countries']) ?></dd>
          <dt>Visa Category</dt><dd><?= e($record['visa_category']) ?></dd>
          <dt>Intended Travel</dt><dd><?= e($record['intended_travel_date']) ?></dd>
          <dt>Duration</dt><dd><?= e($record['expected_duration']) ?></dd>
          <dt>Previous Visa?</dt><dd><?= e($record['previous_visa_yesno']) ?></dd>
          <dt>Previous Details</dt><dd><?= nl2br(e($record['previous_visa_details'])) ?></dd>
          <dt>Refusal / Deportation?</dt><dd><?= e($record['refusal_yesno']) ?></dd>
          <dt>Refusal Details</dt><dd><?= nl2br(e($record['refusal_details'])) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-mortarboard-fill text-primary me-1"></i> Education</h5>
        <dl class="small mb-0">
          <dt>Highest Level</dt><dd><?= e($record['education_level']) ?></dd>
          <dt>Institution &amp; Country</dt><dd><?= e($record['institution_country']) ?></dd>
          <dt>Field of Study</dt><dd><?= e($record['field_of_study']) ?></dd>
          <dt>Graduation Year</dt><dd><?= e($record['graduation_year']) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-translate text-primary me-1"></i> Language</h5>
        <dl class="small mb-0">
          <dt>Native Language</dt><dd><?= e($record['native_language']) ?></dd>
          <dt>English Proficiency</dt><dd><?= e($record['english_proficiency']) ?></dd>
          <dt>Test Score / Date</dt><dd><?= e($record['english_test_score']) ?></dd>
          <dt>Other Languages</dt><dd><?= nl2br(e($record['other_languages'])) ?></dd>
          <dt>French / Spanish Test</dt><dd><?= e($record['french_spanish_test_score']) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-briefcase-fill text-primary me-1"></i> Work History</h5>
        <dl class="small mb-0">
          <dt>Employment Status</dt><dd><?= e($record['employment_status']) ?></dd>
          <dt>Job Title</dt><dd><?= e($record['job_title']) ?></dd>
          <dt>Employer &amp; Industry</dt><dd><?= e($record['employer_industry']) ?></dd>
          <dt>Years Experience</dt><dd><?= e($record['years_experience']) ?></dd>
          <dt>Summary (10 years)</dt><dd><?= nl2br(e($record['employment_summary'])) ?></dd>
          <dt>Employer 1</dt><dd><?= nl2br(e($record['employer1_details'])) ?></dd>
          <dt>Employer 2</dt><dd><?= nl2br(e($record['employer2_details'])) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-cash-stack text-primary me-1"></i> Financial</h5>
        <dl class="small mb-0">
          <dt>Source of Funds</dt><dd><?= e($record['source_of_funds']) ?></dd>
          <dt>Liquid Funds</dt><dd><?= e($record['liquid_funds']) ?></dd>
          <dt>Monthly Income</dt><dd><?= e($record['monthly_income']) ?></dd>
          <dt>Own Assets?</dt><dd><?= e($record['assets_yesno']) ?></dd>
          <dt>Assets Summary</dt><dd><?= nl2br(e($record['assets_summary'])) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <h5 class="mb-2"><i class="bi bi-globe text-primary me-1"></i> Travel &amp; Background</h5>
        <dl class="small mb-0">
          <dt>Countries Visited (10 yrs)</dt><dd><?= nl2br(e($record['travel_history_countries'])) ?></dd>
          <dt>Valid Visas?</dt><dd><?= e($record['valid_visas_yesno']) ?></dd>
          <dt>Visa List</dt><dd><?= nl2br(e($record['valid_visas_list'])) ?></dd>
          <dt>Criminal Record?</dt><dd><?= e($record['criminal_record_yesno']) ?></dd>
          <dt>Criminal Details</dt><dd><?= nl2br(e($record['criminal_details'])) ?></dd>
          <dt>Medical Conditions?</dt><dd><?= e($record['medical_conditions_yesno']) ?></dd>
          <dt>Medical Details</dt><dd><?= nl2br(e($record['medical_details'])) ?></dd>
        </dl>
      </div>
    </div>
  </div>

<?php else: ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Visa Assessments</h3>
    <div class="d-flex gap-2">
      <form method="get" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Status</option>
          <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="in_review" <?= $filter_status === 'in_review' ? 'selected' : '' ?>>In Review</option>
          <option value="processed" <?= $filter_status === 'processed' ? 'selected' : '' ?>>Processed</option>
        </select>
      </form>
    </div>
  </div>

  <div class="data-table">
    <table class="table">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Target / Category</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php if (!$assessments): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No assessments submitted yet.</td></tr>
        <?php else: foreach ($assessments as $a): ?>
          <tr class="<?= !$a['is_read'] ? 'fw-bold' : '' ?>">
            <td>#<?= (int)$a['id'] ?></td>
            <td><?= e($a['full_legal_name']) ?><br><small class="text-muted"><?= e($a['phone']) ?></small></td>
            <td><?= e($a['email']) ?></td>
            <td><?= e($a['target_countries']) ?><br><small><span class="badge bg-info"><?= e($a['visa_category']) ?></span></small></td>
            <td>
              <?php if ($a['status'] === 'pending'): ?><span class="badge bg-warning text-dark">Pending</span>
              <?php elseif ($a['status'] === 'in_review'): ?><span class="badge bg-info">In Review</span>
              <?php else: ?><span class="badge bg-success">Processed</span><?php endif; ?>
            </td>
            <td><small><?= fmt_date($a['created_at'], 'M d, Y H:i') ?></small></td>
            <td class="text-end">
              <a href="visa_assessments.php?id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
              <a href="visa_assessments.php?action=delete&id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this assessment?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$admin_content = ob_get_clean();
$admin_page_title = $record ? 'Assessment #' . (int)$record['id'] : 'Visa Assessments';
require __DIR__ . '/includes/auth.php';
