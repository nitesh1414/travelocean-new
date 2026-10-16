<?php
/**
 * Lightweight, read-only country -> state/province -> city lookup for the
 * searchable location controls on the visa assessment form.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');

function location_response($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    location_response(405, ['items' => [], 'message' => 'Only GET requests are supported.']);
}

$type = isset($_GET['type']) && is_string($_GET['type']) ? trim($_GET['type']) : '';
$countryValue = $_GET['country'] ?? '';
$stateValue = $_GET['state'] ?? '';
$countryCode = is_string($countryValue) ? strtoupper(trim($countryValue)) : '';
$stateCode = is_string($stateValue) ? strtoupper(trim($stateValue)) : '';

if (!in_array($type, ['states', 'cities'], true) || !preg_match('/^[A-Z]{2}$/', $countryCode)) {
    location_response(400, ['items' => [], 'message' => 'A valid lookup type and country code are required.']);
}

if ($type === 'cities' && ($stateCode === '' || !preg_match('/^[A-Z0-9.-]{1,12}$/', $stateCode))) {
    location_response(400, ['items' => [], 'message' => 'A valid state or province code is required.']);
}

$countryStates = null;
$dataFile = __DIR__ . '/../assets/data/countries_states_cities.json';
if (is_file($dataFile) && is_readable($dataFile)) {
    $countries = json_decode(file_get_contents($dataFile), true);
    if (is_array($countries)) {
        foreach ($countries as $country) {
            if (strtoupper((string)($country['code'] ?? '')) === $countryCode) {
                $countryStates = $country['states'] ?? [];
                break;
            }
        }
    }
}

// Local fallback remains useful in development where the production catalog may not be present.
if (!is_array($countryStates)) {
    $fallbackFile = __DIR__ . '/../assets/data/locations.json';
    if (!is_file($fallbackFile) || !is_readable($fallbackFile)) {
        location_response(503, ['items' => [], 'message' => 'Location suggestions are temporarily unavailable.']);
    }
    $locations = json_decode(file_get_contents($fallbackFile), true);
    if (!is_array($locations)) {
        location_response(503, ['items' => [], 'message' => 'Location suggestions are temporarily unavailable.']);
    }
    $countryStates = $locations[$countryCode] ?? [];
}
if (!is_array($countryStates)) {
    location_response(200, ['items' => []]);
}

if ($type === 'states') {
    $items = [];
    foreach ($countryStates as $state) {
        if (!empty($state['name']) && !empty($state['code'])) {
            $items[] = ['name' => $state['name'], 'code' => $state['code']];
        }
    }
    location_response(200, ['items' => $items]);
}

foreach ($countryStates as $state) {
    if (($state['code'] ?? '') === $stateCode) {
        $cities = array_values(array_unique($state['cities'] ?? []));
        location_response(200, ['items' => $cities]);
    }
}

location_response(200, ['items' => []]);
