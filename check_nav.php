<?php
include "config.php";

$nav = '';

$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
error_log("Content-Type: $contentType, Method: $requestMethod");

// First try _REQUEST (handles GET query params and POST form data)
$nav = $_REQUEST['nav'] ?? '';
error_log("_REQUEST nav: '" . ($nav ?: 'empty') . "'");

// Always try to parse the body for JSON if _REQUEST is empty
if (empty($nav)) {
    $input = file_get_contents('php://input');
    error_log("Raw input: '" . $input . "'");
    if (!empty($input)) {
        // Try JSON first
        $data = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['nav'])) {
            $nav = $data['nav'];
            error_log("Parsed from JSON: '$nav'");
        } else {
            error_log("JSON decode error: " . json_last_error_msg());
            // Try parsing as form data
            parse_str($input, $formData);
            if (isset($formData['nav'])) {
                $nav = $formData['nav'];
                error_log("Parsed from form data: '$nav'");
            }
        }
    }
}

error_log("Final nav value: '$nav'");

if (empty($nav)) {
    echo json_encode(array('exists' => false, 'error' => 'No nav provided', 'nav' => $nav));
    exit;
}

// Sanitize the nav name the same way as in the frontend and module creation
$sanitized = preg_replace('/[^a-zA-Z0-9_]/', '_', $nav);
$sanitized = preg_replace('/^[^a-zA-Z_]/', 'mod_', $sanitized);
$trec = strtolower($sanitized);
$name = ucwords($trec);

error_log("Original nav: '$nav', Sanitized: '$sanitized', Final name: '$name'");

$result = $db->query("SELECT * FROM navigation WHERE nav = '$name'");
if($result && $result->num_rows > 0) {
    echo json_encode(array('exists' => true, 'nav' => $nav, 'sanitized' => $name));
} else {
    echo json_encode(array('exists' => false, 'nav' => $nav, 'sanitized' => $name));
}
?>
