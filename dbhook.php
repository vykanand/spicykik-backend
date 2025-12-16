<?php

$host = $_REQUEST['host'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$dbname = $_REQUEST['dbname'];

$myfile = fopen("config.php", "w") or die("Unable to open file!");
$txt = "<?php
^host = '".$host."';
^username = '".$username."';
^password = '".$password."';
^dbname = '".$dbname."';

^db = new mysqli(^host,^username,^password,^dbname);

?>";

fwrite($myfile, $txt);
fclose($myfile);


$path_to_file = 'config.php';
$file_contents = file_get_contents($path_to_file);
$file_contents = str_replace("^","$",$file_contents);
file_put_contents($path_to_file,$file_contents);

$msg = array('dbhook' => 'Database Installed Successfully');
echo json_encode($msg);
?>