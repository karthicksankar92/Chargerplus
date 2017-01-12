<?php
//Connect to the database
include_once("config.php");
//collect all  the details
$e=@mysql_real_escape_string($_POST['e']);
$p=$_POST['p'];
$que=$_POST['que'];
$ans=$_POST['ans'];
$uname="";
$ques="";
$answ="";
//Error Handling
if( $e=="" || $p=="" || $que=="" || $ans=="")
	{
	echo "Please fill in all the details to proceed further";
	exit();
	}
else 
	{
	$sql2="select username from register where email='$e'";
	$query2=mysql_query($sql2);
	if($row=@mysql_fetch_row($query2))
		{
		$uname=$row[0];
		}
	$sql1="select question,answer from usersecurity where username='$uname'";
	$query1=mysql_query($sql1);
	if($row1=@mysql_fetch_row($query1))
		{
		$ques=$row1[0];
		$answ=$row1[1];
		}
	if($ques!=$que || $answ!=$ans)
		{
		echo "wrong";
		}
		//change password
	else
		{
		$sql="update register  set password='$p' where email='$e' "; 
		$query=mysql_query($sql);
		echo "success";
		}
	}
?>