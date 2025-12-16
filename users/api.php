<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");

//`created_at`DATETIME DEFAULT CURRENT_TIMESTAMP,
//config

include "../config.php";


$cwd = getcwd();
preg_match("/[^\/]+$/", $cwd, $matches);
$last_word = $matches[0];
$table_name = $last_word;


if (isset($_GET["show"]))
{

    $sql = "SELECT * FROM $table_name order by created_at DESC";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    $fulldata = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        echo $r['gid'].'<br>';

    }
    echo json_encode($fulldata);
}


if (isset($_GET["getfirstcontent"]))
{

    $sql = "SHOW COLUMNS FROM $table_name";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    
    if (!$result) {
    echo 'Could not run query: ' . mysqli_error();
    exit;
}
$fulldata = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fulldata[] = $row['Field'];
    }
}
array_pop($fulldata);
    echo json_encode($fulldata);
}


if (isset($_GET["getcontent"]))
{

    $sql = "SELECT id,email,phone,name,loginstatus,apikey,role,address,created_at FROM $table_name order by id ASC";
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

if (isset($_GET["shid"]))
{
    $id = $_GET['id'];
    $sql = "SELECT * FROM $table_name where id = '$id'";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    $fulldata = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $fulldata[] = $r;

    }
    echo json_encode($fulldata);

}

if (isset($_GET["updatrole"]))
{
    $ah=$_REQUEST['ah'];
     $id=$_REQUEST['id'];
$sql = "UPDATE $table_name SET access = '$ah' where id = '$id'";
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
