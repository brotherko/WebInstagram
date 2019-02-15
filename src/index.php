<?php
require_once 'lib/base.php';

include 'sections/member.php'; 
if($current_user->is_loggedin()){
  include 'sections/upload.php'; 
}
include 'sections/album.php';
?>