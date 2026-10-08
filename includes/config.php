<?php

/**
 * Travel Oceans CMS - Site Configuration
 */

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
//define('DB_HOST', 'localhost');
//define('DB_NAME', 'u588033801_tonew');
//define('DB_USER', 'u588033801_tonew');
//define('DB_PASS', 'LIVEpro@1234');
//define('DB_CHARSET', 'utf8mb4');

define('DB_HOST', 'localhost');
define('DB_NAME', 'u588033801_tonew');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site paths — auto-detect base URL so it works on any port / subfolder
if (!defined('SITE_URL')) {
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme   = $is_https ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Strip any path beyond the script directory (e.g. /traveloceans/index.php -> /traveloceans)
    $script   = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = rtrim(str_replace('\\', '/', dirname($script)), '/');
    // If we're in admin/api/includes, fall back to the public root
    if (preg_match('~(.*?)/(admin|api|includes|assets|sql)(/|$)~', $basePath, $m)) {
        $basePath = $m[1];
    }
    define('SITE_URL', $scheme . '://' . $host . $basePath);
}
define('SITE_NAME', 'Travel Oceans');
define('SITE_TAGLINE', 'Travel Across the Oceans');

// Upload directories
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// Default timezone
date_default_timezone_set('Europe/Lisbon');

// CSRF token helper
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token)
{
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

// Helper escape
function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Format datetime
function fmt_date($d, $f = 'M d, Y')
{
    if (!$d) return '';
    $ts = is_numeric($d) ? (int)$d : strtotime($d);
    return $ts ? date($f, $ts) : '';
}
