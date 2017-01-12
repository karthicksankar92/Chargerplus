<?php 
include_once("loginstatus.php");
if($loginok != true || $login_user=="")
{
	exit();
}
?>
<?php 
if(isset($_POST['type']) && isset($_POST['blockee']))
{
	$blockeduser =preg_replace('#[^a-z0-9]#i','',$_POST['blockee']);
	$sql="select COUNT(id) from register where username='$blockeduser' and activated ='1' LIMIT 1";
	$query=mysql_query($sql);
	$exist_c=@mysql_fetch_row($query);
	$query = mysql_query($sql);
	$exist_c = @mysql_fetch_row($query);
	//$existcount = $exist_c[0];
	//echo "$exist_c";
	if($exist_c < 1 ){
		@mysql_close($config);
		echo "$blockeduser does not exist no please";
		exit();
	}
	$sql="select id from userblock where blockedby='$login_user' and blockeduser='$blockeduser' LIMIT 1";
	$query=mysql_query($sql);
	$numrows=@mysql_num_rows($query);
	if($_POST['type']=="block")
	{
		if($numrows >0)
		{
			@mysql_close($config);
			echo "you already have this user blocked.";
			exit();
		}
		else
		{
			$sql = "Insert into userblock(blockedby,blockeduser,blockedon) values ('$login_user','$blockeduser',now())";
			$query=mysql_query($sql) or die(mysql_error());
			//@mysql_close($config);
			echo "blocked_ok";
			exit();
		}
	}
	else if($_POST['type']=="unblock")
	{
		if($numrows==0)
		{
			@mysql_close($config);
			echo "You do not have this user blocked,therefore we cannot unblock them.";
			exit();
		}
		else
		{
			$sql="delete from userblock where blockedby='$login_user' and blockeduser='$blockeduser' LIMIT 1";
			$query=mysql_query($sql) or die(mysql_error());
			$sql="delete from userblock where blockedby='$blockeduser' and blockeduser='$login_user' LIMIT 1";
			$query=mysql_query($sql) or die(mysql_error());
			@mysql_close($config);
			echo "unblocked_ok";
			exit();
		}
	}
}
?>