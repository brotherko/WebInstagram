<?php
require_once('lib/dal.class.php');
require_once("lib/user.class.php");
require_once("consts/constants.php");
require_once("consts/messages.php");
require_once("lib/config.php");

$dal = new DAL();
$current_user = new UserService();

function home($msg, $interval = 2, $hard = false){
  printf($msg);
  if($hard){
      header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
      header("Pragma: no-cache"); // HTTP 1.0.
      header("Expires: 0");      
  }
  header("refresh: ".$interval."; url=index.php");
  exit();
}
?>