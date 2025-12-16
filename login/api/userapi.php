<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
require "../../config.php";

if (isset($_REQUEST["register"])) {

   $name=$_REQUEST['name'];
  $password=$_REQUEST['password'];
  $email=$_REQUEST['email'];
  $phone=$_REQUEST['phone'];
  $address=$_REQUEST['address'];
  $type=$_REQUEST['type'];

  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
  $apikey = $randomString;
  $sql = "INSERT INTO users (name,password,apikey,phone,email,address,type) VALUES ('$name','$password','$apikey','$phone','$email','$address','$type')";
  $result = mysqli_query($db,$sql)or die(mysqli_error($db));
  if($result){
$message = array("response" => "success");
  }else{
    $message = array("response" => "failed");
  }
  echo json_encode($message);

}

if (isset($_REQUEST["login"])) {
  $name=$_REQUEST['name'];
  $password=$_REQUEST['password'];
$sqlcheck = "SELECT * FROM users WHERE email = '$name' AND password = '$password'";
// echo $sqlcheck;
  $resultcheck = mysqli_query($db, $sqlcheck)or die(mysqli_error($db));
  $rwcheck = mysqli_num_rows($resultcheck);
  // var_dump($rwcheck);
if($rwcheck > 0){
  $fulldata = array();
while($r = mysqli_fetch_assoc($resultcheck)) {
  $fulldata[] = $r;
}

$s22 = "UPDATE users SET loginstatus= 'True' WHERE email = '$name'";
  $r22 = mysqli_query($db, $s22)or die(mysqli_error($db));

  $message = $fulldata;
  }else{
    $message = array("response" => "failed");
  }
  echo json_encode($message);
}
?>