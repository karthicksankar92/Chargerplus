<?php
session_start();
//if user logged in,log out them
if(isset($_SESSION["username"]))
{
header("location:user.php?u=".$_SESSION["username"]);
exit();
}
?>
<?php
//Login using ajax
if(isset($_POST["e"]))
{
//Connect to the database
include_once("config.php");
//Gather the data
$e=@mysql_real_escape_string($_POST['e']);
$p=$_POST['p'];
//Get user ip address
$ip =preg_replace('#[^0-9.]#','',getenv('REMOTE_ADDR')); 

//Error Handling
	if($e=="" || $p=="" )
	{
	echo "login_failed";
	exit();
	}
	else
	{
// ERROR HANDLING-End
$sql="select id,username,password from register where email='$e' LIMIT 1";
$query=mysql_query($sql) or die(mysql_error());
$row=@mysql_fetch_row($query);
$db_id=$row[0];
$db_username=$row[1];
$db_pass=$row[2];
	if($p!=$db_pass)
	{
	echo "login_failed";
	exit();
	}
	else
	{
$_SESSION['userid']=$db_id;
$_SESSION['username']=$db_username;
$_SESSION['password']=$db_pass;
setcookie("id",$db_id,strtotime('+30 days'),"/","","",TRUE);
setcookie("user",$db_username,strtotime('+30 days'),"/","","",TRUE);
setcookie("pass",$db_pass,strtotime('+30 days'),"/","","",TRUE);
//Update  "IP" And "LASTlOGIN" 
$sql="update register SET ip='$ip',lastlogin=now() where username='$db_username' LIMIT 1";
$query=mysql_query($sql)or die(mysql_error());
echo $db_username;
exit();
	}
}
exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset ="UTF-8">
<title>Login Page</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style.css">
<script src="display.js"></script>
<script src="ajax.js"></script>
<script src="empty.js"></script>
<script text/javascript>
function login()
{
var e=_("email").value;
var p=_("password").value;
	if(e=="" || p=="")
	{
	_("status").innerHTML="Please fill in all details";
	}
	else
	{
	_("loginbtn").style.display="none";
	_("status").innerHTML='Please wait...';
	var ajax = ajaxobj("POST","login.php");
	ajax.onreadystatechange=function()
		{
		if(ajaxReturn(ajax)==true)
			{
			if(ajax.responseText =="login_failed")
				{
	        	_("status").innerHTML="Login unsuccessful,please try again.";	
				_("loginbtn").style.display="block";	
  				}
			else
				{
			window.location="user.php?u="+ajax.responseText;
				}
			}
		}
		ajax.send("e="+e+"&p="+p);
	}
}	
</script>


</head>
<body>
<?php include_once("pagetop.php"); ?>
<div id="pageMiddle">
<h3>Log In </h3>
<!-- Login form -->
<form  id="loginform" onSubmit="return false;">
<div>Email:</div>
<input id="email" type="text"  onfocus="emptyElement('status')" maxlength="88">
<div>Password:</div>
<input id="password" type= "password"  onfocus="emptyElement('status')"  maxlength="100">
<input id="loginbtn" type="submit"  name="Log In" value="Log In" onClick="login()">
<p id="status"></p>
<a href="#">Forgot Your Password?</a>
</form>
</div>
<div id="pageBottom">&copy;2015 Charger Team</div>

</body>
</html>