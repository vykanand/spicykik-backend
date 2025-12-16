<?php

$dest = $_REQUEST['dest'];
$appn = $_REQUEST['appn'];

$fname = $appn.'.zip';
$trget = $appn;

$f = file_put_contents($fname, fopen($dest, 'r'), LOCK_EX);
if(FALSE === $f)
    die("Couldn't write to file.");
$zip = new ZipArchive;
$res = $zip->open($fname);
if ($res === TRUE) {
  $zip->extractTo($trget);
  $zip->close();
unlink($fname);
  echo json_encode(array('response' => 'extracted' ));
  //
} else {
  //
	 echo json_encode(array('response' => 'failed' ));
}

?>