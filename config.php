<?php
$host = "junction.proxy.rlwy.net";  // Docker service name
$username = "root";
$password = "cyIgFzjjbzRiVbiHkemiUCKftdfPqBOn";
$dbname = "chirag";
$port = 14359;


// Attempt to connect
$db = @new mysqli($host,$username,$password,$dbname,$port);

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