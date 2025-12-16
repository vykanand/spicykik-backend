<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");

//`created_at`DATETIME DEFAULT CURRENT_TIMESTAMP,
//config

include "../config.php";

$table_name = basename(getcwd());

// Get plugin mappings from navigation table
if (isset($_GET["getpluginmap"])) {
    $sql = "SELECT plugin FROM navigation WHERE nav = '$table_name'";
    $result = mysqli_query($db, $sql);
    
    $pluginMap = array();
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (!empty($row['plugin'])) {
            $pluginMap = json_decode($row['plugin'], true);
            if (!is_array($pluginMap)) {
                $pluginMap = array();
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($pluginMap);
    exit();
}

if (isset($_GET["getfirstcontent"])) {
    $user_role = isset($_GET['role']) ? $_GET['role'] : '';
    $sql = $user_role === 'admin' 
        ? "SELECT * FROM $table_name LIMIT 1"
        : "SELECT * FROM $table_name WHERE role='$user_role' LIMIT 1";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    
    $fulldata = array();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $fulldata = array_keys($row);
    }
    array_pop($fulldata);
    echo json_encode($fulldata);
}


if (isset($_GET["getcontent"]))
{

    $sql = "SELECT * FROM $table_name order by created_at DESC";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    $fulldata = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $fulldata[] = $r;

    }
    echo json_encode($fulldata);
}

if (isset($_REQUEST["setcontent"]))
{
    $data = $_POST;
    unset($data["setcontent"]);
    $key = array_keys($data); //get key( column name)
    $value = array_values($data); //get values (values to be inserted)
    $sql = "INSERT INTO $table_name ( " . implode(',', $key) . ") VALUES('" . implode("','", $value) . "')";

    $result = mysqli_query($db, $sql) or die(mysqli_error($db));

    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}

if (isset($_POST["edcontent"]))
{
    unset($_POST["edcontent"]);
    $query = "UPDATE $table_name SET";
    $comma = " ";
    foreach($_POST as $key => $val) {
    if( ! empty($val)) {
        $query .= $comma . $key . " = '" . mysqli_real_escape_string($db,trim($val)) . "'";
        $comma = ", ";
    }
}

$product_id = $_POST['id'];

$query = $query . "WHERE id = '".$product_id."' ";

$result = mysqli_query($db, $query) or die(mysqli_error($db));

    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}



if (isset($_GET["delcontentsa"]))
{
    $id = $_GET['id'];
    $sql = "DELETE FROM $table_name WHERE id='$id'";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    if ($result)
    {
        $message = array(
            "response" => "success"
        );
    }
    else
    {
        $message = array(
            "response" => "failed"
        );
    }
    echo json_encode($message);

}

$db -> close();
?>
