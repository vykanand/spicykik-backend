<?php
// Ensure file has no BOM and uses LF line endings (this rewrite normalizes EOLs)
// Add CORS and lightweight diagnostic endpoints so the upgrade UI can pre-check
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Respond to preflight requests quickly
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Lightweight ping endpoint for pre-checks
if (isset($_GET['action']) && $_GET['action'] === 'ping') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'message' => 'downloader reachable']);
    exit;
}

// Lightweight info endpoint to aid debugging server execution
if (isset($_GET['action']) && $_GET['action'] === 'info') {
    header('Content-Type: application/json');
    $eol = (defined('PHP_EOL') && PHP_EOL === "\r\n") ? 'CRLF' : 'LF';
    echo json_encode([
        'status' => 'ok',
        'php_sapi' => php_sapi_name(),
        'php_version' => PHP_VERSION,
        'eol' => $eol
    ]);
    exit;
}

// Minimal automatic patcher: loads immediately, no UI, no whitelisting.
// Hard-coded URL. Downloads upgrade.zip, extracts, patches /custom and /appmarket.
// WARNING: Anyone visiting this file triggers a patch.
/* ----------------------------------------------------
   CONFIG 
---------------------------------------------------- */

$ZIP_URL = "https://github.com/vykanand/billion-upgrade/raw/refs/heads/main/upgrade.zip";

$BASE_DIR = __DIR__;
$DOWNLOADS_DIR = $BASE_DIR . "/downloads";
$TMP_DIR = $DOWNLOADS_DIR . "/tmp";
$ZIP_PATH = $DOWNLOADS_DIR . "/upgrade.zip";
$MAX_ZIP_BYTES = 200 * 1024 * 1024; // 200MB


/* ----------------------------------------------------
   HELPERS
---------------------------------------------------- */

function ensure_dir($d)
{
    if (!is_dir($d))
        @mkdir($d, 0755, true);
}

function download_zip($url, $dest)
{
    $fp = fopen($dest, 'wb');
    if (!$fp)
        return ["ok" => false, "error" => "Cannot open file for writing"];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    fclose($fp);

    if ($res === false)
        return ["ok" => false, "error" => $err];

    return ["ok" => true];
}

function extract_zip($zip, $dest)
{
    // Use PharData extraction like the working extractor in appmarket/extractplugin.php
    if (!class_exists('PharData')) {
        return ["ok" => false, "error" => "PharData not available"];
    }

    try {
        $archive = new PharData($zip);
        ensure_dir($dest);
        $archive->extractTo($dest, null, true);
        return ["ok" => true];
    } catch (Exception $e) {
        return ["ok" => false, "error" => "Extraction failed: " . $e->getMessage()];
    }
}

function xcopy_collect($srcRoot, $dstRoot)
{
    $srcRoot = rtrim($srcRoot, DIRECTORY_SEPARATOR);
    $dstRoot = rtrim($dstRoot, DIRECTORY_SEPARATOR);
    ensure_dir($dstRoot);

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $copied = [];

    foreach ($it as $f) {
        $src = $f->getPathname();
        $rel = substr($src, strlen($srcRoot));
        if (strpos($rel, DIRECTORY_SEPARATOR) === 0)
            $rel = substr($rel, 1);
        if (strpos($rel, "..") !== false)
            continue; // safety

        $dst = $dstRoot . DIRECTORY_SEPARATOR . $rel;

        if ($f->isDir()) {
            ensure_dir($dst);
        } else {
            ensure_dir(dirname($dst));
            copy($src, $dst);
            $copied[] = $rel;
        }
    }

    return $copied;
}

function rrmdir($dir)
{
    if (!is_dir($dir))
        return;
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object == '.' || $object == '..')
            continue;
        $path = $dir . DIRECTORY_SEPARATOR . $object;
        if (is_dir($path) && !is_link($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

// Build a list of file copy mappings (src -> dst -> rel)
function collect_copy_map($srcRoot, $dstRoot)
{
    $srcRoot = rtrim($srcRoot, DIRECTORY_SEPARATOR);
    $dstRoot = rtrim($dstRoot, DIRECTORY_SEPARATOR);
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $map = [];
    foreach ($it as $f) {
        if ($f->isFile()) {
            $src = $f->getPathname();
            $rel = substr($src, strlen($srcRoot));
            if (strpos($rel, DIRECTORY_SEPARATOR) === 0)
                $rel = substr($rel, 1);
            if (strpos($rel, "..") !== false)
                continue;
            $dst = $dstRoot . DIRECTORY_SEPARATOR . $rel;
            $map[] = ['src' => $src, 'dst' => $dst, 'rel' => $rel];
        }
    }
    return $map;
}

function create_zip_from_dir($dir, $zipPath)
{
    // Use PharData ONLY to create a .tar archive
    if (!class_exists('PharData')) {
        return ["ok" => false, "error" => "PharData not available"];
    }

    // ensure .tar extension
    $tarPath = preg_replace('/\.zip$/i', '.tar', $zipPath);
    try {
        if (file_exists($tarPath))
            @unlink($tarPath);
        $phar = new PharData($tarPath);
        $phar->buildFromDirectory($dir);
        return ["ok" => true, "path" => $tarPath];
    } catch (Exception $e) {
        return ["ok" => false, "error" => "PharData backup failed: " . $e->getMessage()];
    }
}


/* ----------------------------------------------------
   START AUTO PATCH
---------------------------------------------------- */

header("Content-Type: application/json");

ensure_dir($DOWNLOADS_DIR);
// Clear any previous temporary extraction state to avoid stale files
if (is_dir($TMP_DIR)) {
    rrmdir($TMP_DIR);
}
ensure_dir($TMP_DIR);

$response = [
    "status" => "starting",
    "steps" => []
];

/* -------------------- 1. Download ------------------- */

$step = download_zip($ZIP_URL, $ZIP_PATH);
if (!$step["ok"]) {
    echo json_encode(["status" => "error", "error" => "Download failed", "details" => $step], JSON_PRETTY_PRINT);
    exit;
}
$response["steps"][] = "Downloaded ZIP";

/* -------------------- 2. Extract -------------------- */

$extractDir = $TMP_DIR . "/extracted";
@mkdir($extractDir, 0755, true);

$step = extract_zip($ZIP_PATH, $extractDir);
if (!$step["ok"]) {
    echo json_encode(["status" => "error", "error" => "Extraction failed", "details" => $step], JSON_PRETTY_PRINT);
    exit;
}
$response["steps"][] = "Extracted ZIP";


/* -------------------- 3. Locate folders dynamically -------------------- */

// Collect directories found in the extracted tree. Map by lower-cased folder name
// to one or more actual source paths. We'll copy each found folder into the
// repository's top-level folder with the same name (case-insensitive).
$foundDirs = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($extractDir, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$ignoreNames = ['.git', '__macosx', '.github', '.gitignore'];

foreach ($it as $f) {
    if ($f->isDir()) {
        $name = $f->getFilename();
        $lname = strtolower($name);
        if (in_array($lname, $ignoreNames, true))
            continue;
        // Only consider reasonable folder names (alphanumeric, dash, underscore)
        if (!preg_match('/^[a-z0-9_\-]+$/i', $name))
            continue;
        if (!isset($foundDirs[$lname]))
            $foundDirs[$lname] = [];
        $foundDirs[$lname][] = $f->getPathname();
    }
}

if (empty($foundDirs)) {
    echo json_encode(["status" => "error", "error" => "No usable folders found in ZIP"], JSON_PRETTY_PRINT);
    exit;
}

$response["steps"][] = "Found folders: " . implode(', ', array_keys($foundDirs));


/* -------------------- 4. Consolidated backup & copy -------------------- */

// Build a single consolidated map of all files to copy across all found folders
$globalMaps = [];
foreach ($foundDirs as $lname => $paths) {
    $dst = $BASE_DIR . DIRECTORY_SEPARATOR . $lname;
    ensure_dir($dst);
    foreach ($paths as $src) {
        $maps = collect_copy_map($src, $dst);
        foreach ($maps as $m) {
            // annotate with the target folder name for grouping later
            $m['lname'] = $lname;
            $globalMaps[] = $m;
        }
    }
}

$patched = [];

// Determine which destination files already exist -- we'll backup only those
$toBackupAll = [];
foreach ($globalMaps as $m) {
    if (file_exists($m['dst']))
        $toBackupAll[] = $m['dst'];
}
// Also always include the repository `users` directory files in the backup
$usersDir = $BASE_DIR . DIRECTORY_SEPARATOR . 'users';
if (is_dir($usersDir)) {
    $uit = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($usersDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($uit as $f) {
        if ($f->isFile()) {
            $toBackupAll[] = $f->getPathname();
        }
    }
    $response['steps'][] = "Included users directory in backup";
}
// dedupe
$toBackupAll = array_values(array_unique($toBackupAll));

// Create a single backup archive if there are files to backup
if (!empty($toBackupAll)) {
    $backupBase = $DOWNLOADS_DIR . DIRECTORY_SEPARATOR . 'backups';
    ensure_dir($backupBase);
    // date format dd-mm-YYYY_His to match request, avoid colons in filename
    $ts = date('d-m-Y_His');
    $tmpBackup = $DOWNLOADS_DIR . DIRECTORY_SEPARATOR . 'backup_tmp_' . $ts;
    ensure_dir($tmpBackup);

    foreach ($toBackupAll as $fullDst) {
        $relRepo = ltrim(substr($fullDst, strlen($BASE_DIR)), DIRECTORY_SEPARATOR);
        $target = $tmpBackup . DIRECTORY_SEPARATOR . $relRepo;
        ensure_dir(dirname($target));
        copy($fullDst, $target);
    }

    $archivePath = $backupBase . DIRECTORY_SEPARATOR . 'backup_' . $ts . '.tar';
    $zres = create_zip_from_dir($tmpBackup, $archivePath);
    // remove tmp backup tree
    rrmdir($tmpBackup);
    if ($zres['ok']) {
        $response['backup'] = ['archive' => $zres['path'], 'count' => count($toBackupAll)];
    } else {
        $response['backup'] = ['error' => $zres['error']];
    }
}

// Now perform the copies, grouped by lname for the response
foreach ($globalMaps as $m) {
    ensure_dir(dirname($m['dst']));
    if (!copy($m['src'], $m['dst']))
        continue;
    if (!isset($patched[$m['lname']]))
        $patched[$m['lname']] = [];
    $patched[$m['lname']][] = $m['rel'];
}

// dedupe per folder
foreach ($patched as $k => $list) {
    $patched[$k] = array_values(array_unique($list));
}

$response["steps"][] = "Files patched (consolidated)";


/* -------------------- 5. Output -------------------- */

echo json_encode([
    "status" => "ok",
    "patched" => $patched,
    "steps" => $response["steps"],
    "zip" => $ZIP_PATH,
    "extracted" => $extractDir
], JSON_PRETTY_PRINT);

exit;


?>