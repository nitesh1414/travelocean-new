<?php
/**
 * Sends a plain-text notice after a visa assessment has been stored.
 * SMTP settings are read from the admin-managed settings table.
 */
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';

function visa_assessment_email_body(array $assessment)
{
    $id = isset($assessment['id']) ? (int)$assessment['id'] : 0;
    $submittedAt = trim((string)($assessment['created_at'] ?? ''));
    $applicantEmail = $assessment['email'] ?? '';
    $applicantEmail = is_scalar($applicantEmail) ? trim((string)$applicantEmail) : '';
    $lines = [
        'NEW VISA ASSESSMENT SUBMISSION',
        'Assessment ID: #' . $id,
        'Submitted: ' . ($submittedAt !== '' ? $submittedAt : date('Y-m-d H:i:s')),
        'Applicant email / reply-to: ' . ($applicantEmail !== '' ? $applicantEmail : '(not provided)'),
        '',
    ];

    $sections = [
        'Personal and Identity Information' => [
            'Full legal name' => 'full_legal_name',
            'Date of birth' => 'dob',
            'Gender' => 'gender',
            'Birth country' => 'birthplace_country',
            'Birth state / province' => 'birthplace_state',
            'Birth city' => 'birthplace_city',
            'Current nationality / citizenships held' => 'nationality',
            'Passport number' => 'passport_number',
            'Passport issue date' => 'passport_issue',
            'Passport expiry date' => 'passport_expiry',
            'Current residency' => 'current_residency',
            'Current country' => 'address_country',
            'Current state / province' => 'address_state',
            'Current city' => 'address_city',
            'Current address' => 'address',
            'Phone' => 'phone',
        ],
        'Marital and Family Status' => [
            'Marital status' => 'marital_status',
            'Spouse / partner details' => 'spouse_partner_details',
            'Number of dependent children' => 'dependent_children',
        ],
        'Intended Travel and Visa Purpose' => [
            'Target destination country' => 'target_countries',
            'Visa category' => 'visa_category',
            'Intended travel date' => 'intended_travel_date',
            'Expected duration' => 'expected_duration',
            'Applied for a visa before' => 'previous_visa_yesno',
            'Previous visa details' => 'previous_visa_details',
            'Previous refusal / cancellation / deportation' => 'refusal_yesno',
            'Refusal details' => 'refusal_details',
            'Immediate family / relatives in destination' => 'family_target_country',
            'Family / relative details' => 'family_target_details',
        ],
        'Education' => [
            'Highest education level' => 'education_level',
            'Institution and country' => 'institution_country',
            'Field of study' => 'field_of_study',
            'Graduation year' => 'graduation_year',
        ],
        'Language' => [
            'Native language' => 'native_language',
            'English proficiency' => 'english_proficiency',
            'English test score' => 'english_test_score',
            'Other languages' => 'other_languages',
            'French / Spanish test score' => 'french_spanish_test_score',
        ],
        'Employment' => [
            'Employment status' => 'employment_status',
            'Job title' => 'job_title',
            'Employer and industry' => 'employer_industry',
            'Years of experience' => 'years_experience',
            'Employment summary' => 'employment_summary',
            'Employer 1 details' => 'employer1_details',
            'Employer 2 details' => 'employer2_details',
        ],
        'Financial Information' => [
            'Source of funds' => 'source_of_funds',
            'Liquid funds' => 'liquid_funds',
            'Monthly income' => 'monthly_income',
            'Owns assets' => 'assets_yesno',
            'Assets summary' => 'assets_summary',
        ],
        'Travel and Background' => [
            'Countries visited in the last 10 years' => 'travel_history_countries',
            'Has valid visas' => 'valid_visas_yesno',
            'Valid visas list' => 'valid_visas_list',
            'Criminal record' => 'criminal_record_yesno',
            'Criminal record details' => 'criminal_details',
            'Medical conditions' => 'medical_conditions_yesno',
            'Medical details' => 'medical_details',
        ],
    ];

    foreach ($sections as $heading => $fields) {
        $lines[] = '=== ' . $heading . ' ===';
        foreach ($fields as $label => $key) {
            $value = $assessment[$key] ?? '';
            $value = is_scalar($value) ? trim((string)$value) : '';
            if ($value === '') $value = '(not provided)';
            $value = str_replace(["\r\n", "\r"], "\n", $value);
            $valueLines = explode("\n", $value);
            $lines[] = $label . ': ' . array_shift($valueLines);
            foreach ($valueLines as $valueLine) $lines[] = '  ' . $valueLine;
        }

        if ($heading === 'Marital and Family Status') {
            $children = $assessment['children_json'] ?? null;
            if (is_string($children) && $children !== '') {
                $decodedChildren = json_decode($children, true);
                $children = is_array($decodedChildren) ? $decodedChildren : [];
            }
            if (!is_array($children)) $children = is_array($assessment['children'] ?? null) ? $assessment['children'] : [];
            if ($children) {
                $lines[] = 'Dependent children details:';
                foreach (array_values($children) as $index => $child) {
                    if (!is_array($child)) continue;
                    $childName = trim((string)($child['name'] ?? ''));
                    $childAge = trim((string)($child['age'] ?? ''));
                    $childNationality = trim((string)($child['nationality'] ?? ''));
                    $lines[] = '  Child ' . ($index + 1) . ': ' . $childName . ' | Age: ' . $childAge . ' | Nationality: ' . $childNationality;
                }
            } else {
                $lines[] = 'Dependent children details: None';
            }
        }
        $lines[] = '';
    }

    return implode("\r\n", $lines);
}

function visa_assessment_configured_mailer()
{
    $recipient = trim((string)setting('visa_assessment_notification_email', 'info@traveloceans.eu'));
    if ($recipient === '') $recipient = 'info@traveloceans.eu';
    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('The visa assessment notification recipient is not a valid email address.');
    }

    $host = trim((string)setting('smtp_host'));
    $portValue = trim((string)setting('smtp_port', '587'));
    $port = filter_var($portValue === '' ? '587' : $portValue, FILTER_VALIDATE_INT);
    if ($port === false || $port < 1 || $port > 65535) {
        throw new RuntimeException('The configured SMTP port is invalid.');
    }

    $fromEmail = trim((string)setting('smtp_from_email'));
    if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $smtpUsernameForFrom = trim((string)setting('smtp_username'));
        if (filter_var($smtpUsernameForFrom, FILTER_VALIDATE_EMAIL)) {
            $fromEmail = $smtpUsernameForFrom;
        } else {
            $fromEmail = trim((string)setting('site_email'));
        }
    }
    if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $fromEmail = 'info@traveloceans.eu';
    }
    $fromName = trim((string)setting('smtp_from_name'));
    if ($fromName === '') $fromName = trim((string)setting('site_name', 'Travel Oceans'));
    if ($fromName === '') $fromName = 'Travel Oceans';
    $fromName = preg_replace('/[\r\n]+/', ' ', $fromName);

    $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
    $mailer->Encoding = 'base64';
    $mailer->setFrom($fromEmail, $fromName);
    $mailer->addAddress($recipient);
    $mailer->isHTML(false);
    $mailer->Timeout = 20;

    if ($host === '') {
        // PHP mail() depends on a working mail transfer agent on the web server.
        $mailer->isMail();
    } else {
        $encryption = strtolower(trim((string)setting('smtp_encryption', 'tls')));
        if (!in_array($encryption, ['tls', 'ssl', 'none'], true)) {
            throw new RuntimeException('The configured SMTP encryption mode is invalid.');
        }

        $username = trim((string)setting('smtp_username'));
        $password = (string)setting('smtp_password');
        if ($username !== '' && $password === '') {
            throw new RuntimeException('An SMTP username is configured, but no SMTP password is saved.');
        }

        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = $port;
        $mailer->SMTPAuth = $username !== '';
        $mailer->Username = $username;
        $mailer->Password = $password;
        if ($encryption === 'tls') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->SMTPAutoTLS = true;
        } elseif ($encryption === 'ssl') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mailer->SMTPAutoTLS = false;
        } else {
            $mailer->SMTPSecure = '';
            $mailer->SMTPAutoTLS = false;
        }
    }

    return $mailer;
}

function visa_assessment_send_mailer($mailer)
{
    $sent = $mailer->send();
    if (!$sent) {
        $error = trim((string)$mailer->ErrorInfo);
        throw new RuntimeException($error !== '' ? $error : 'The configured email transport rejected the message.');
    }
    return true;
}

function send_visa_assessment_notification(array $assessment)
{
    $mailer = visa_assessment_configured_mailer();
    $replyEmail = trim((string)($assessment['email'] ?? ''));
    if (filter_var($replyEmail, FILTER_VALIDATE_EMAIL)) {
        $replyName = trim((string)($assessment['full_legal_name'] ?? ''));
        $replyName = preg_replace('/[\r\n]+/', ' ', $replyName);
        $mailer->addReplyTo($replyEmail, $replyName);
    }

    $mailer->Subject = 'New Visa Assessment Submission #' . (int)($assessment['id'] ?? 0);
    $mailer->Body = visa_assessment_email_body($assessment);
    return visa_assessment_send_mailer($mailer);
}

function send_visa_assessment_test_email()
{
    $mailer = visa_assessment_configured_mailer();
    $mailer->Subject = '[Travel Oceans] Email delivery test';
    $mailer->Body = implode("\r\n", [
        'This is a test message from the Travel Oceans visa assessment email settings.',
        'It contains no applicant information.',
        'Sent at: ' . date('Y-m-d H:i:s T'),
        '',
        'A successful test confirms the configured mail transport accepted the message.',
        'It does not guarantee inbox delivery. Check spam, quarantine, and your sender-domain email authentication if it is missing.',
    ]);
    return visa_assessment_send_mailer($mailer);
}
