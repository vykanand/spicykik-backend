<?php
$dbconfigPath = __DIR__ . '/dbconfig/config.json';
$config = json_decode(file_get_contents($dbconfigPath), true);

if (!$config || !isset($config['database'])) {
    error_log("Database config not found or invalid");
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Database connection failed']);
    exit;
}

$dbConfig = $config['database'];
$host = $dbConfig['host'];
$username = $dbConfig['username'];
$password = $dbConfig['password'];
$dbname = $dbConfig['database'];
$port = $dbConfig['port'];

$ssl = $dbConfig['ssl'] ?? [];
$caPath = __DIR__ . '/dbconfig/' . ($ssl['ca_file_path'] ?? 'ca.pem');

$db = mysqli_init();

$flags = 0;
if (!empty($ssl['enabled'])) {
    $flags = MYSQLI_CLIENT_SSL;
    if (!empty($ssl['verify_server'])) {
        $flags |= MYSQLI_CLIENT_SSL_VERIFY_SERVER_CERT;
    }
    mysqli_ssl_set($db, null, null, $caPath, null, null);
}
mysqli_real_connect($db, $host, $username, $password, $dbname, $port, null, $flags);

if ($db->connect_error) {
    error_log("Database connection failed: (" . $db->connect_errno . ") " . $db->connect_error);
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Database connection failed']);
    exit;
}

$db->set_charset($dbConfig['charset'] ?? 'utf8mb4');
?>