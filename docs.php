<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header('Content-Type: application/json');

include "config.php";

// Function to scan for API endpoints in api.php file
function scanApiFile($filePath, $moduleName) {
    if (!file_exists($filePath)) {
        return null;
    }
    
    $content = file_get_contents($filePath);
    $endpoints = [];
    
    // Detect GET endpoints
    preg_match_all('/if\s*\(\s*isset\s*\(\s*\$_GET\s*\[\s*["\']([^"\']+)["\']\s*\]\s*\)\s*\)/i', $content, $getMatches);
    foreach ($getMatches[1] as $param) {
        $endpoints[] = [
            'method' => 'GET',
            'param' => $param,
            'description' => 'GET endpoint for ' . $param
        ];
    }
    
    // Detect POST endpoints
    preg_match_all('/if\s*\(\s*isset\s*\(\s*\$_POST\s*\[\s*["\']([^"\']+)["\']\s*\]\s*\)\s*\)/i', $content, $postMatches);
    foreach ($postMatches[1] as $param) {
        $endpoints[] = [
            'method' => 'POST',
            'param' => $param,
            'description' => 'POST endpoint for ' . $param
        ];
    }
    
    // Detect REQUEST endpoints
    preg_match_all('/if\s*\(\s*isset\s*\(\s*\$_REQUEST\s*\[\s*["\']([^"\']+)["\']\s*\]\s*\)\s*\)/i', $content, $requestMatches);
    foreach ($requestMatches[1] as $param) {
        $endpoints[] = [
            'method' => 'POST',
            'param' => $param,
            'description' => 'REQUEST endpoint for ' . $param
        ];
    }
    
    return $endpoints;
}

// Get all navigation records
$sql = "SELECT id, nav, urn FROM navigation ORDER BY nav ASC";
$result = mysqli_query($db, $sql);

$modules = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $nav = $row['nav'];
        $urn = $row['urn'];
        
        // Extract folder from urn (e.g., "delivery/boot.html" -> "delivery")
        $folder = dirname($urn);
        if ($folder === '.') {
            $folder = $nav;
        }
        
        // Build api.php path
        $apiPath = __DIR__ . '/' . $folder . '/api.php';
        
        // Check if api.php exists
        if (file_exists($apiPath)) {
            $endpoints = scanApiFile($apiPath, $nav);
            
            if ($endpoints && count($endpoints) > 0) {
                $modules[] = [
                    'id' => $row['id'],
                    'name' => $nav,
                    'urn' => $urn,
                    'folder' => $folder,
                    'apiPath' => $folder . '/api.php',
                    'endpoints' => $endpoints
                ];
            }
        }
    }
}

$db->close();

echo json_encode([
    'success' => true,
    'count' => count($modules),
    'modules' => $modules
]);
?>
