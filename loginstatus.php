
<?php
session_start();
include_once("config.php");
$config="";
// connection to database or session_start(),be careful.
$loginok =false;
$loginid="";
$login_user="";
$login_pwd="";
//  Verification of User function
function evalLoggedregister($config,$id,$u,$p)
{
$sql ="select ip from register where id='$id' and username='$u' and password ='$p' and activated='1' LIMIT 1 ";
$query=mysql_query($sql) or die(mysql_error());
$numrows=mysql_num_rows($query);
if($numrows >0)
{
return true;
}

}

if(isset($_SESSION["userid"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]))
{
$loginid =preg_replace('#[^0-9]#','',$_SESSION['userid']);
$login_user =preg_replace('#[^a-z0-9]#i','',$_SESSION['username']);
$login_pwd =preg_replace('#[^a-z0-9]#i','',$_SESSION['password']);
//verification of  User
$loginok=evalLoggedregister($config,$loginid,$login_user,$login_pwd);
}
else if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"]))
{
$_SESSION['userid']= preg_replace('#[^0-9]#','',$_COOKIE['id']);
$_SESSION['username']= preg_replace('#[^a-z0-9]#i','',$_COOKIE['user']);
$_SESSION['password']= preg_replace('#[^a-z0-9]#i','',$_COOKIE['pass']);
$loginid =$_SESSION['userid'];
$login_user =$_SESSION['username'];
$login_pwd =$_SESSION['password'];
//Verification of User
$loginok=evalLoggedregister($config,$loginid,$login_user,$login_pwd);
if($loginok==true)
{
//update their last login 
$sql="update register set lastlogin=now() where id='$loginid' LIMIT 1";
$query=mysql_query($sql)  or die(mysql_error());
}
}
?>