<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$name  = trim($_POST['full_legal_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide your full legal name and a valid email address.']);
    exit;
}

try {
    $id = save_visa_assessment($_POST);
    echo json_encode([
        'success' => true,
        'message' => 'Your Free Visa Assessment has been submitted successfully. Our consultants will review it and contact you shortly.',
        'id' => $id
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Unable to submit your assessment right now. Please try again later.']);
}
