<?php
require_once __DIR__ . '/db.php';

/* ---------- Front-end helpers ---------- */
function get_services($only_home = false) {
    $sql = 'SELECT * FROM services WHERE status = 1';
    if ($only_home) $sql .= ' AND show_on_home = 1';
    $sql .= ' ORDER BY sort_order ASC, id ASC';
    return DB::fetchAll($sql);
}

function get_service($slug) {
    return DB::fetch('SELECT * FROM services WHERE slug = ? AND status = 1', [$slug]);
}

function get_service_features($service_id) {
    return DB::fetchAll('SELECT * FROM service_features WHERE service_id = ? ORDER BY sort_order', [$service_id]);
}

function get_slides() {
    return DB::fetchAll('SELECT * FROM slides WHERE status = 1 ORDER BY sort_order ASC');
}

function get_programs() {
    return DB::fetchAll('SELECT * FROM programs WHERE status = 1 ORDER BY sort_order ASC');
}

function get_program($slug) {
    return DB::fetch('SELECT * FROM programs WHERE slug = ? AND status = 1', [$slug]);
}

function get_program_highlights($program_id) {
    return DB::fetchAll('SELECT * FROM program_highlights WHERE program_id = ? ORDER BY sort_order', [$program_id]);
}

function get_program_details($program_id) {
    return DB::fetchAll('SELECT * FROM program_details WHERE program_id = ? ORDER BY sort_order', [$program_id]);
}

function get_testimonials($limit = 6) {
    return DB::fetchAll('SELECT * FROM testimonials WHERE status = 1 ORDER BY sort_order ASC LIMIT ' . (int)$limit);
}

function get_why_choose() {
    return DB::fetchAll('SELECT * FROM why_choose ORDER BY sort_order ASC');
}

function get_business_services() {
    return DB::fetchAll('SELECT * FROM business_services WHERE status = 1 ORDER BY sort_order ASC');
}

function get_portugal_reasons() {
    return DB::fetchAll('SELECT * FROM portugal_reasons ORDER BY sort_order ASC');
}

function get_portugal_things() {
    return DB::fetchAll('SELECT * FROM portugal_things ORDER BY sort_order ASC');
}

function get_doc_categories() {
    return DB::fetchAll('SELECT * FROM documentation_categories ORDER BY sort_order ASC');
}

function get_doc_items($category_id) {
    return DB::fetchAll('SELECT * FROM documentation_items WHERE category_id = ? ORDER BY sort_order ASC', [$category_id]);
}

function get_page($slug) {
    return DB::fetch('SELECT * FROM pages WHERE slug = ? AND status = 1', [$slug]);
}

function get_team_members($limit = null) {
    $sql = 'SELECT * FROM team_members WHERE status = 1 ORDER BY sort_order ASC, id ASC';
    if ($limit) $sql .= ' LIMIT ' . (int)$limit;
    return DB::fetchAll($sql);
}


function get_partners($category = null) {
    $sql = 'SELECT * FROM partners WHERE status = 1';
    $params = [];
    if ($category) { $sql .= ' AND category = ?'; $params[] = $category; }
    $sql .= ' ORDER BY sort_order ASC, id ASC';
    return DB::fetchAll($sql, $params);
}

function get_page_blocks($page_slug) {
    $rows = DB::fetchAll('SELECT block_key, block_value FROM page_blocks WHERE page_slug = ? ORDER BY sort_order ASC', [$page_slug]);
    $out = [];
    foreach ($rows as $r) $out[$r['block_key']] = $r['block_value'];
    return $out;
}

function get_page_block($page_slug, $key, $default = '') {
    static $cache = [];
    if (!isset($cache[$page_slug])) $cache[$page_slug] = get_page_blocks($page_slug);
    return $cache[$page_slug][$key] ?? $default;
}

function save_page_block($page_slug, $key, $value) {
    $exists = DB::fetchColumn('SELECT id FROM page_blocks WHERE page_slug = ? AND block_key = ?', [$page_slug, $key]);
    if ($exists) {
        DB::update('page_blocks', ['block_value' => $value], 'page_slug = :p AND block_key = :k', [':p' => $page_slug, ':k' => $key]);
    } else {
        // Determine sort_order: max + 1
        $max = (int)DB::fetchColumn('SELECT COALESCE(MAX(sort_order),0) FROM page_blocks WHERE page_slug = ?', [$page_slug]);
        DB::insert('page_blocks', [
            'page_slug'   => $page_slug,
            'block_key'   => $key,
            'block_value' => $value,
            'sort_order'  => $max + 1,
        ]);
    }
}


/* ---------- File upload helper ---------- */
function upload_file($field, $allowed = ['jpg','jpeg','png','gif','webp']) {
    if (empty($_FILES[$field]['name'])) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) return null;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
    $name = uniqid('img_', true) . '.' . $ext;
    $dest = UPLOAD_DIR . $name;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) return null;
    return 'assets/uploads/' . $name;
}

/* ---------- Save contact message ---------- */
function save_contact_message($data) {
    return DB::insert('contact_messages', [
        'name'             => trim($data['name'] ?? ''),
        'email'            => trim($data['email'] ?? ''),
        'phone'            => trim($data['phone'] ?? ''),
        'subject'          => trim($data['subject'] ?? ''),
        'service_interest' => trim($data['service_interest'] ?? ''),
        'message'          => trim($data['message'] ?? ''),
        'ip_address'       => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
}

function save_subscriber($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    $exists = DB::fetchColumn('SELECT id FROM subscribers WHERE email = ?', [$email]);
    if ($exists) return true;
    DB::insert('subscribers', ['email' => $email]);
    return true;
}

/* ---------- Admin auth ---------- */
function is_admin() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ' . SITE_URL . '/admin/index.php');
        exit;
    }
}

function current_admin() {
    if (!is_admin()) return null;
    return DB::fetch('SELECT * FROM admins WHERE id = ?', [$_SESSION['admin_id']]);
}

/* ---------- Format helpers ---------- */
function status_badge($status) {
    $status = (int)$status;
    if ($status === 1) return '<span class="badge bg-success">Active</span>';
    return '<span class="badge bg-secondary">Inactive</span>';
}

function truncate($text, $len = 120) {
    $text = strip_tags((string)$text);
    if (function_exists('mb_strlen')) {
        if (mb_strlen($text) <= $len) return $text;
        return mb_substr($text, 0, $len) . '…';
    }
    if (strlen($text) <= $len) return $text;
    return substr($text, 0, $len) . '…';
}

/* ---------- Visa assessment submissions ---------- */
function save_visa_assessment($data) {
    $fields = [
        'full_legal_name', 'dob', 'gender', 'birthplace_country', 'birthplace_state',
        'birthplace_city', 'nationality', 'passport_number', 'passport_issue',
        'passport_expiry', 'current_residency', 'address_country', 'address_state',
        'address_city', 'address', 'phone', 'email', 'marital_status',
        'spouse_partner_details', 'family_target_country', 'family_target_details',
        'target_countries', 'visa_category', 'intended_travel_date', 'expected_duration',
        'previous_visa_yesno', 'previous_visa_details', 'refusal_yesno', 'refusal_details',
        'education_level', 'institution_country', 'field_of_study', 'graduation_year',
        'native_language', 'english_proficiency', 'english_test_score', 'other_languages',
        'french_spanish_test_score', 'employment_status', 'job_title', 'employer_industry',
        'years_experience', 'employment_summary', 'employer1_details', 'employer2_details',
        'source_of_funds', 'liquid_funds', 'monthly_income', 'assets_yesno', 'assets_summary',
        'travel_history_countries', 'valid_visas_yesno', 'valid_visas_list',
        'criminal_record_yesno', 'criminal_details', 'medical_conditions_yesno', 'medical_details',
    ];

    $record = [];
    foreach ($fields as $field) {
        $value = $data[$field] ?? '';
        if (is_array($value) || is_object($value)) $value = '';
        $value = trim((string)$value);
        $record[$field] = $value === '' ? null : $value;
    }

    $dependentChildren = $data['dependent_children'] ?? 0;
    $record['dependent_children'] = max(0, min(10, is_numeric($dependentChildren) ? (int)$dependentChildren : 0));
    $children = [];
    $submittedChildren = $data['children'] ?? [];
    if (!is_array($submittedChildren)) $submittedChildren = [];
    foreach ($submittedChildren as $child) {
        if (!is_array($child)) continue;
        $nameValue = $child['name'] ?? '';
        $ageValue = $child['age'] ?? '';
        $nationalityValue = $child['nationality'] ?? '';
        $name = is_scalar($nameValue) ? trim((string)$nameValue) : '';
        $age = is_scalar($ageValue) ? trim((string)$ageValue) : '';
        $nationality = is_scalar($nationalityValue) ? trim((string)$nationalityValue) : '';
        if ($name === '' && $age === '' && $nationality === '') continue;
        $children[] = [
            'name' => $name,
            'age' => $age === '' ? null : max(0, min(120, (int)$age)),
            'nationality' => $nationality,
        ];
    }
    $record['children_json'] = $children
        ? json_encode($children, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE)
        : null;
    $record['ip_address'] = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 60);

    return DB::insert('visa_assessments', $record);
}

function get_visa_assessments($limit = 200, $status = null) {
    $limit = max(1, min(1000, (int)$limit));
    $sql = 'SELECT * FROM visa_assessments';
    $params = [];
    if ($status !== null && in_array($status, ['pending', 'in_review', 'processed'], true)) {
        $sql .= ' WHERE status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY created_at DESC, id DESC LIMIT ' . $limit;
    return DB::fetchAll($sql, $params);
}

function get_visa_assessment_by_id($id) {
    return DB::fetch('SELECT * FROM visa_assessments WHERE id = ?', [(int)$id]);
}
