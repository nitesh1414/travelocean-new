<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$csrfToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!verify_csrf($csrfToken)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

function visa_assessment_post_value($field) {
    $value = $_POST[$field] ?? '';
    return is_scalar($value) ? trim((string)$value) : '';
}

$name = visa_assessment_post_value('full_legal_name');
$email = visa_assessment_post_value('email');
$requiredFields = [
    'dob' => 'date of birth',
    'gender' => 'gender',
    'birthplace_country' => 'country of birth',
    'birthplace_state' => 'state or province of birth',
    'birthplace_city' => 'city of birth',
    'nationality' => 'nationality or citizenship',
    'marital_status' => 'marital status',
    'dependent_children' => 'number of dependent children',
    'address_country' => 'current country',
    'address_state' => 'current state or province',
    'address_city' => 'current city',
    'phone' => 'phone number with country code',
    'target_countries' => 'target destination country',
    'visa_category' => 'visa category',
    'previous_visa_yesno' => 'previous visa information',
    'refusal_yesno' => 'previous refusal information',
    'family_target_country' => 'family or relatives in the target destination',
    'education_level' => 'highest level of education',
    'english_proficiency' => 'English proficiency',
    'employment_status' => 'current employment status',
    'source_of_funds' => 'source of funds',
    'assets_yesno' => 'asset information',
    'valid_visas_yesno' => 'valid travel visa information',
    'criminal_record_yesno' => 'criminal record information',
    'medical_conditions_yesno' => 'medical condition information',
];

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide your full legal name and a valid email address.']);
    exit;
}

foreach ($requiredFields as $field => $label) {
    if (visa_assessment_post_value($field) === '') {
        echo json_encode(['success' => false, 'message' => 'Please provide your ' . $label . '.']);
        exit;
    }
}

$dependentChildrenValue = visa_assessment_post_value('dependent_children');
if (!preg_match('/^\\d+$/', $dependentChildrenValue) || (int)$dependentChildrenValue > 10) {
    echo json_encode(['success' => false, 'message' => 'Enter a whole number of dependent children between 0 and 10.']);
    exit;
}
$dependentChildrenCount = (int)$dependentChildrenValue;
$submittedChildren = $_POST['children'] ?? [];
if (!is_array($submittedChildren) || count($submittedChildren) !== $dependentChildrenCount) {
    echo json_encode(['success' => false, 'message' => 'Please provide details for each dependent child matching the number entered.']);
    exit;
}
for ($childIndex = 0; $childIndex < $dependentChildrenCount; $childIndex++) {
    $child = $submittedChildren[$childIndex] ?? null;
    if (!is_array($child)) {
        echo json_encode(['success' => false, 'message' => 'Please provide details for each dependent child matching the number entered.']);
        exit;
    }
    $childName = $child['name'] ?? '';
    $childAge = $child['age'] ?? '';
    $childNationality = $child['nationality'] ?? '';
    if (!is_scalar($childName) || trim((string)$childName) === ''
        || !is_scalar($childAge) || !preg_match('/^\\d{1,3}$/', trim((string)$childAge)) || (int)$childAge > 120
        || !is_scalar($childNationality) || trim((string)$childNationality) === '') {
        echo json_encode(['success' => false, 'message' => 'Please enter the name, age, and nationality for each dependent child.']);
        exit;
    }
}

$phone = visa_assessment_post_value('phone');
if (!preg_match('/^\\+[1-9][0-9\\s().-]{5,24}$/', $phone) || strlen(preg_replace('/\\D/', '', $phone)) < 7) {
    echo json_encode(['success' => false, 'message' => 'Please enter a complete phone number including a leading + and country code.']);
    exit;
}

if (visa_assessment_post_value('family_target_country') === 'Yes' && visa_assessment_post_value('family_target_details') === '') {
    echo json_encode(['success' => false, 'message' => 'Please specify the relation and residency or immigration status of your family member or relative.']);
    exit;
}

try {
    $id = save_visa_assessment($_POST);
    echo json_encode([
        'success' => true,
        'message' => 'Your Free Visa Assessment has been submitted successfully. Our consultants will review it and contact you shortly.',
        'id' => $id
    ]);
} catch (Throwable $e) {
    // Keep implementation details out of the response, but log enough for the
    // server administrator to identify database/schema problems.
    error_log('[visa_assessment] Submission failed: ' . $e->getMessage());

    $sqlState = $e instanceof PDOException ? (string)$e->getCode() : '';
    if ($sqlState === '42S02') {
        echo json_encode([
            'success' => false,
            'message' => 'Visa assessment storage is not initialized. Import sql/visa_assessment.sql into the database configured for this site, then try again.'
        ]);
        exit;
    }
    if ($sqlState === '42S22') {
        echo json_encode([
            'success' => false,
            'message' => 'The existing visa assessment table is missing required columns. Run sql/upgrade_visa_assessments.sql against the configured database, then try again.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unable to submit your assessment right now. Please try again later.']);
}
