<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");


include "config.php";

$table_name = "navigation";


if (isset($_REQUEST["shownav"]))
{

    $sql = "SELECT * FROM $table_name";
    $result = mysqli_query($db, $sql) or die(mysqli_error($db));
    $fulldata = array();
    while ($r = mysqli_fetch_assoc($result))
    {
        $fulldata[] = $r;

    }
    echo json_encode($fulldata);
}



if (isset($_REQUEST["instbl"]))
{
$data = $_REQUEST['dta'];
$appname = $_REQUEST['appname'];

$temparray = json_decode($_REQUEST['dta']);

$sql = 'CREATE TABLE '.$appname.' (id INT NOT NULL AUTO_INCREMENT, ';

 foreach($temparray as $field)
    {
        $sql .= ' '.$field.' TEXT NULL,';
    }
        $sql .= '  created_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY ( `id` ))';

$result = mysqli_query($db, $sql);



if ($result) {
    $msg = array('response' =>$appname);

}else{
$msg=null;
}

echo json_encode($msg);

}

?>