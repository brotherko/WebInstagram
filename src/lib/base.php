<?php
require_once('lib/dal.class.php');
require_once("lib/user.class.php");
require_once("consts/constants.php");
require_once("consts/messages.php");
require_once("lib/config.php");

$dal = new DAL();
$current_user = new UserService();

function home($msg, $interval = 2){
  printf($msg);
  header("refresh: ".$interval."; url=index.php");
  exit();
}
?>