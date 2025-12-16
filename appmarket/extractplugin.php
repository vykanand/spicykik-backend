<?php

// Validate required parameters
if (!isset($_POST['action']) || !isset($_POST['plugin_name'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No action or plugin name specified'
    ]);
    exit;
}

$plugin_name = $_POST['plugin_name'];
$action = $_POST['action'];
$src = "./{$plugin_name}/{$plugin_name}";
$dest = "../plugins/{$plugin_name}";

// Recursive directory operations
function rrmdir($dir) {
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? rrmdir($path) : unlink($path);
    }
    return rmdir($dir);
}

function rchmod($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = "$dir/$file";
        if (is_dir($path)) {
            chmod($path, 0755);
            rchmod($path);
        } else {
            chmod($path, 0644);
        }
    }
}

// Handle different actions
switch ($action) {
    case 'check':
        echo json_encode([
            'success' => true,
            'exists' => is_dir($dest),
            'error' => null
        ]);
        break;

    case 'delete':
        $result = rrmdir($dest);
        echo json_encode([
            'success' => $result,
            'error' => $result ? null : 'Failed to delete plugin directory'
        ]);
        break;

    case 'install':
        if (!file_exists($src . '.zip')) {
            echo json_encode([
                'success' => false,
                'error' => "Plugin zip file not found at: {$src}.zip"
            ]);
            break;
        }

        if (!file_exists("../plugins")) {
            if (!mkdir("../plugins", 0755, true)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to create plugins directory'
                ]);
                break;
            }
        }

        try {
            $archive = new PharData($src . '.zip');
            $archive->extractTo($dest, null, true);
            rchmod($dest);
            chmod($dest, 0755);
            
            echo json_encode([
                'success' => true,
                'error' => null
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => "Extraction failed: {$e->getMessage()}"
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action specified'
        ]);
}

exit;
