<?php
//To display randome people on the page who have uploaded their profile photos
include_once("loginstatus.php");
$sql = "SELECT username, photo FROM register WHERE photo IS NOT NULL AND activated='1' ORDER BY RAND() LIMIT 32";
$query = mysql_query($sql);
$userList = "";
while ($row = @mysql_fetch_array($query, MYSQL_ASSOC))  {
           $u = $row["username"];
           $avatar = $row["photo"];
           $profilePic = 'user/'.$u.'/'.$photo;
		   echo "$profilePic";
           $userList .= '<a href="user.php?u='.$u.'" title="'.$u.'"><img src="'.$profilePic.'" alt="'.$u.'" style="width:100px; height:100px;  margin:10px;"></a>' ;
}

$sql = "SELECT COUNT(id)  FROM register WHERE activated='1' ";
$query = @mysql_query( $sql);
$row=@mysql_fetch_row($query);
$userCount = $row[0] ;
?>
<?php 
include_once("loginstatus.php");
$sql ="select username from register where activated ='1'";
$query = mysql_query($sql);
$usernumrows=@mysql_num_rows($query);
$userList ="";
while ($row =@mysql_fetch_array($query,MYSQL_ASSOC))
{
$u =$row["username"];
$userList ='<a href="user.php?u='.$u.'">'.$u.'</a> $nbsp; | $nbsp; ';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset ="UTF-8">
<title>Home Page</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style.css">
<script src="display.js"></script>


</head>
<body>
<div id="pageTop">   
<?php

$header="";
$env= '<img src="images/notes.png" width="22" height="12" alt="Notes" title="This env is for logged in users">';
$loginlink='a href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
if($loginok == true)
{
$sql="select notedate from register where username='$login_user' LIMIT 1";
$query=mysql_query($sql);
$row=@mysql_fetch_row($query);
$notedate=$row[0];
$sql="select id from notifications where username='$login_user' and notedate > '$notedate' LIMIT 1";
$query=mysql_query($sql);
$numrows=@mysql_num_rows($query);
if($numrows=0)
{
$env='<a href="notification.php" title="Your Notifications"><img src="images/notestill.jpg" width="22" height="12" alt="Notifications"></a>';
}
else
{
$env='<a href="notification.php" title="You have new Notification"><img src="images/noteflash.jpg" width="22" height="12" alt="Notifications"></a>';
}
$loginlink='<a href="user.php?u='.$login_user.'">'.$login_user.'</a> &nbsp;|&nbsp; <a href="signout.php">Sign Out</a>';
}
?>
<?php include_once("pagetop.php");?>
<div id="pageMiddle">&nbsp;</div>
<div id="pageBottom">&copy;2015 Charger Team</div>

</body>
</html>