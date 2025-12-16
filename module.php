<?php
$trec = strtolower($_REQUEST['dest']);
$src = getcwd()."/custom";

$dest = getcwd()."/".$trec;

// shell_exec("cp -r $src $dest");
// echo shell_exec("mkdir oorp");
// echo shell_exec("sudo cp -r $src $dest");

// echo "<H3>Copy Paste completed!</H3>";

function xcopy($source, $dest, $permissions = 0755)
{
    $sourceHash = hashDirectory($source);
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        if($sourceHash != hashDirectory($source."/".$entry)){
             xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
    }

    // Clean up
    $dir->close();
    return true;
}

// In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated

function hashDirectory($directory){
    if (! is_dir($directory)){ return false; }

    $files = array();
    $dir = dir($directory);

    while (false !== ($file = $dir->read())){
        if ($file != '.' and $file != '..') {
            if (is_dir($directory . '/' . $file)) { $files[] = hashDirectory($directory . '/' . $file); }
            else { $files[] = md5_file($directory . '/' . $file); }
        }
    }

    $dir->close();

    return md5(implode('', $files));
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                else
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
            }
        }
        rmdir($dir);
    }
}






// $cfi = file_get_contents('side.json');

// $cfi = substr($cfi, 4);
// $endlast = substr($cfi, 0, -1);

// $silt = "{name:'".$_REQUEST['dest']."',url:'".$_REQUEST['dest']."/boot.html'}";
// $Ns = $endlast.",". $silt."]";
// $beij = "tms=". $Ns;



// file_put_contents('side.json', $beij);

include "config.php";

error_log("Module creation started for: $trec");

if (empty($trec) || !preg_match('/^[a-zA-Z0-9_]+$/', $trec)) {
    error_log("Invalid module name: $trec");
    echo json_encode(array('response' => false, 'error' => 'Invalid or empty module name'));
    exit;
}

$name = ucwords($trec);
$de = $trec."/boot.html";

// Step 1: Check if navigation already exists (case-insensitive)
$checkSql = "SELECT * FROM navigation WHERE LOWER(nav) = LOWER('$name')";
$checkResult = mysqli_query($db, $checkSql);
if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    error_log("Navigation already exists: $name");
    echo json_encode(array('response' => false, 'error' => 'Module already exists'));
    exit;
}

// Step 1: Insert navigation
$sql = "INSERT INTO navigation (nav, urn) VALUES ('$name', '$de')";
$result = mysqli_query($db, $sql);
if (!$result) {
    error_log("Failed to insert navigation: " . mysqli_error($db));
    echo json_encode(array('response' => false, 'error' => 'Failed to create navigation entry'));
    exit;
}
error_log("Navigation inserted successfully");

$data = $_REQUEST['dta'];
$appname = $trec;

$temparray = json_decode($_REQUEST['dta']);
if ($temparray === false) {
    error_log("Invalid JSON in dta: " . $_REQUEST['dta']);
    echo json_encode(array('response' => false, 'error' => 'Invalid JSON data'));
    exit;
}

if (empty($temparray) || !is_array($temparray)) {
    error_log("Invalid fields array");
    // Rollback navigation
    mysqli_query($db, "DELETE FROM navigation WHERE nav='$name'");
    echo json_encode(array('response' => false, 'error' => 'Invalid or empty fields array'));
    exit;
}

// Step 2: Check if table already exists
$tableCheckSql = "SHOW TABLES LIKE '$appname'";
$tableCheckResult = mysqli_query($db, $tableCheckSql);
if ($tableCheckResult && mysqli_num_rows($tableCheckResult) > 0) {
    error_log("Table already exists: $appname");
    // Rollback navigation
    mysqli_query($db, "DELETE FROM navigation WHERE nav='$name'");
    echo json_encode(array('response' => false, 'error' => 'Table already exists'));
    exit;
}

// Step 2: Create table
$sql2 = 'CREATE TABLE `'.$appname.'` (id INT NOT NULL AUTO_INCREMENT, ';

foreach($temparray as $field) {
    $cleanField = preg_replace('/[^a-zA-Z0-9_]/', '_', $field);
    if (!empty($cleanField)) {
        $sql2 .= ' `'.$cleanField.'` TEXT NULL,';
    }
}
$sql2 .= ' role VARCHAR(50) DEFAULT "admin", created_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY ( `id` ))';

$result2 = mysqli_query($db, $sql2);
if (!$result2) {
    error_log("Failed to create table: " . mysqli_error($db));
    // Rollback navigation
    mysqli_query($db, "DELETE FROM navigation WHERE nav='$name'");
    echo json_encode(array('response' => false, 'error' => 'Failed to create table'));
    exit;
}
error_log("Table created successfully");

// Step 3: Copy files
// Clean any existing module folder
if (is_dir($dest)) {
    rrmdir($dest);
}
$copyResult = xcopy($src, $dest);
if (!$copyResult) {
    error_log("Failed to copy files");
    // Rollback table and navigation
    mysqli_query($db, "DROP TABLE `$appname`");
    mysqli_query($db, "DELETE FROM navigation WHERE nav='$name'");
    // Remove the partially created module folder
    rrmdir($dest);
    echo json_encode(array('response' => false, 'error' => 'Failed to copy module files'));
    exit;
}
error_log("Files copied successfully");

$msg = array('response' => 'success','module' => $name,'created' => $trec."/boot.html");
echo json_encode($msg);
?>