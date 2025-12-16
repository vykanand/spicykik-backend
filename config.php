<?php
// Default DB settings (fall back if project-config.json missing or invalid)
$host = "junction.proxy.rlwy.net";
$username = "root";
$password = "cyIgFzjjbzRiVbiHkemiUCKftdfPqBOn";
$dbname = "spicykik";
$port = 14359;

// Attempt to connect (credentials are intentionally hard-coded)
$db = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($db->connect_error) {
    // Log full error server-side (do not expose details to clients)
    error_log("Database connection failed: (" . $db->connect_errno . ") " . $db->connect_error);
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Database connection failed'
    ]);
    exit;
}

$db->set_charset("utf8mb4");
?>