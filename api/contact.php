<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$service = trim($_POST['service_interest'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields with valid data.']);
    exit;
}

try {
    save_contact_message([
        'name' => $name, 'email' => $email, 'phone' => $phone,
        'subject' => $subject, 'service_interest' => $service,
        'message' => $message,
    ]);
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been received. We will contact you shortly.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Unable to send your message right now. Please try again later.']);
}
