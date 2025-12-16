<?php
include "config.php";

$table = $_REQUEST['table'];
$dataJson = $_REQUEST['data'];
$batchSize = isset($_REQUEST['batch_size']) ? (int)$_REQUEST['batch_size'] : 100; // Default batch size
$startFrom = isset($_REQUEST['start_from']) ? (int)$_REQUEST['start_from'] : 0; // For resuming

if (empty($table) || !preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
    echo json_encode(array('response' => false, 'error' => 'Invalid table name'));
    exit;
}

$data = json_decode($dataJson, true);
if ($data === null || !is_array($data)) {
    echo json_encode(array('response' => false, 'error' => 'Invalid data'));
    exit;
}

$totalRecords = count($data);
$processed = 0;
$inserted = 0;
$skipped = 0;
$errors = 0;

// Process in batches
$batchData = array_slice($data, $startFrom, $batchSize);
$batchProcessed = 0;

foreach ($batchData as $row) {
    if (!is_array($row)) continue;

    $fields = array_keys($row);
    $values = array_values($row);

    // Add role and created_at
    $fields[] = 'role';
    $values[] = 'admin';

    $fields[] = 'created_at';
    $values[] = date('Y-m-d H:i:s');

    // Build WHERE clause for duplicate check
    $whereConditions = [];
    $whereValues = [];
    foreach ($row as $key => $val) {
        $whereConditions[] = "`$key` = ?";
        $whereValues[] = $val;
    }

    $whereClause = implode(' AND ', $whereConditions);

    // Check if record already exists
    $checkSql = "SELECT id FROM `$table` WHERE $whereClause LIMIT 1";
    $checkStmt = mysqli_prepare($db, $checkSql);
    if (!$checkStmt) {
        $errors++;
        continue;
    }

    mysqli_stmt_bind_param($checkStmt, str_repeat('s', count($whereValues)), ...$whereValues);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $skipped++;
        mysqli_stmt_close($checkStmt);
        $batchProcessed++;
        continue; // Skip duplicate
    }

    mysqli_stmt_close($checkStmt);

    // Prepare placeholders
    $placeholders = str_repeat('?,', count($fields) - 1) . '?';

    $sql = "INSERT INTO `$table` (" . implode(',', array_map(function($f) { return "`$f`"; }, $fields)) . ") VALUES ($placeholders)";

    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        $errors++;
        continue;
    }

    // Bind parameters
    $types = str_repeat('s', count($values));
    mysqli_stmt_bind_param($stmt, $types, ...$values);

    if (mysqli_stmt_execute($stmt)) {
        $inserted++;
    } else {
        $errors++;
    }

    mysqli_stmt_close($stmt);
    $batchProcessed++;
}

$processed = $startFrom + $batchProcessed;
$progress = ($totalRecords > 0) ? round(($processed / $totalRecords) * 100, 2) : 100;

if ($processed < $totalRecords) {
    // More batches to process
    echo json_encode(array(
        'response' => 'progress',
        'processed' => $processed,
        'total' => $totalRecords,
        'progress' => $progress,
        'inserted' => $inserted,
        'skipped' => $skipped,
        'errors' => $errors,
        'next_start' => $processed
    ));
} else {
    // All done
    echo json_encode(array(
        'response' => 'success',
        'processed' => $processed,
        'total' => $totalRecords,
        'progress' => 100,
        'inserted' => $inserted,
        'skipped' => $skipped,
        'errors' => $errors
    ));
}
?>
