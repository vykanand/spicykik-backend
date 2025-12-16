<?php
// Default DB settings (fall back if project-config.json missing or invalid)
// $host = "junction.proxy.rlwy.net";
// $username = "root";
// $password = "cyIgFzjjbzRiVbiHkemiUCKftdfPqBOn";
// $dbname = "chirag";
// $port = 14359;

// Load overrides from project-config.json if present
if (file_exists(__DIR__ . '/project-config.json')) {
    $cfg = json_decode(file_get_contents(__DIR__ . '/project-config.json'), true);
    if (is_array($cfg) && isset($cfg['database']) && is_array($cfg['database'])) {
        $dbcfg = $cfg['database'];
        $host = $dbcfg['host'] ?? $host;
        $username = $dbcfg['username'] ?? $username;
        $password = $dbcfg['password'] ?? $password;
        $dbname = $dbcfg['database'] ?? $dbname;
        $port = $dbcfg['port'] ?? $port;
    }
}

// Attempt to connect
$db = @new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($db->connect_error) {
    die(json_encode([
        'error' => true,
        'message' => 'Database connection failed',
        'details' => $db->connect_error,
        'code' => $db->connect_errno
    ]));
}
$db->set_charset("utf8mb4");
?>