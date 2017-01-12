<?php
session_start();
//set session data to an empty array
$_SESSION =array();
// Expire thoeir cookie files
if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"]))
{
setcookie("id",'',strtotime('-5 days'),'/');
setcookie("user",'',strtotime('-5 days'),'/');
setcookie("pass",'',strtotime('-5 days'),'/');
}
//Destroy the session variables
session_destroy();
//Double check to see if their session exists
if(isset($_SESSION['username']))
{
header("location:message.php?msg=Error:_Logout_failed");
}
else
{
header("location:charger.php");
exit();
}
?>