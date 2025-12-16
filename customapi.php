<?php

include "config.php";

if (isset($_REQUEST["bulkins"])) {
    $table = $_REQUEST['dest'];
    $data = json_decode($_REQUEST['dta'], true); // Decode as associative array

    if ($data === false) {
        echo json_encode(array('response' => false, 'error' => 'Invalid JSON data'));
        exit;
    }

    if (empty($data) || !is_array($data)) {
        echo json_encode(array('response' => false, 'error' => 'Invalid or empty data'));
        exit;
    }

    $keymain = array_keys($data[0]);
    $cleanKeys = array_map(function($k) { return preg_replace('/[^a-zA-Z0-9_]/', '_', $k); }, $keymain);
    $batchSize = 1000; // Insert in batches of 1000 for scalability
    $totalInserted = 0;
    $errors = [];

    // Split data into batches
    $batches = array_chunk($data, $batchSize);

    foreach ($batches as $batch) {
        $rtv = '';
        foreach ($batch as $ixor) {
            $aix = '';
            $coma1 = "'";
            foreach ($keymain as $akey) {
                $value = isset($ixor[$akey]) ? mysqli_real_escape_string($db, $ixor[$akey]) : '';
                $aix .= $coma1 . $value . "'";
                $coma1 = ", '";
            }
            $rtv .= "(" . $aix . "),";
        }

        $kmain = "(" . implode(", ", array_map(function($k) { return '`'.$k.'`'; }, $cleanKeys)) . ")";
        $rmain = rtrim($rtv, ",");

        $sql = "INSERT INTO `$table` $kmain VALUES $rmain";

        $result = mysqli_query($db, $sql);
        if (!$result) {
            $errors[] = mysqli_error($db);
        } else {
            $totalInserted += count($batch);
        }
    }

    if (empty($errors)) {
        echo json_encode(array('response' => true, 'inserted' => $totalInserted));
    } else {
        echo json_encode(array('response' => false, 'error' => implode('; ', $errors)));
    }
}
?>
