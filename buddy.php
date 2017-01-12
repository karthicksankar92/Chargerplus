<?php 
include_once("loginstatus.php");
if($loginok != true || $login_user=="")
{
exit();
}
?>

<?php
if(isset($_POST['type']) && isset($_POST['user']))
{
$user=preg_replace('#[^a-z0-9]#i','',$_POST['user']);
$sql="select count(id) from register where username='$user' and activated='1' LIMIT 1";
$query=mysql_query($sql);
$exist_c=@mysql_fetch_row($query);
if($exist_c < 1 )
{
@mysql_close($config);
echo "$user does not exist";
exit();
}
if($_POST['type']=="friend")
{
$sql="select count(id) from friends where friend1='$user' and accept='1' or friend2='$user' AND accept='1'";
$query=mysql_query($sql);
$buddycount=@mysql_fetch_row($query);
$sql="select count(id) from userblock where blockedby='$user' and blockeduser='$login_user' LIMIT 1";
$query=mysql_query($sql);
$blockcount1 =@mysql_fetch_row($sql);
$sql="select count(id) from userblock where blockedby='$login_user' and blockeduser='$user' LIMIT 1";
$query=mysql_query($sql);
$blockcount2=@mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$login_user' and friend2='$user' and accept ='1' LIMIT 1";
$query=mysql_query($sql);
$row_count1=@mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$user' and friend2='$login_user' and accept ='1' LIMIT 1";
$query=mysql_query($sql);
$row_count2=@mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$login_user' and friend2='$user' and accept ='0' LIMIT 1";
$query=mysql_query($sql);
$row_count3=@mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$user' and friend2='$login_user' and accept ='0' LIMIT 1";
$query=mysql_query($sql);
$row_count4=@mysql_fetch_row($query);
if($buddycount[0] > 99)
{
@mysql_close($config);
echo "$user currently has the maximum number of friends";
exit();
}
else if($blockcount1[0] > 0)
{
@mysql_close($config);
echo "$user has blocked you, hence we cannot proceed.";
exit();
}
else if($blockcount2[0] > 0)
{
@mysql_close($config);
echo "you must first unblock $user in order to friend with them.";
exit();
}
else if($row_count1[0] > 0 || $row_count2[0] > 0)
{
@mysql_close($config);
echo "You are already friends with $user.";
exit();
}
else if($row_count3[0]> 0)
{
@mysql_close($config);
echo "You have a pending friend request already sent to $user.";
exit();
}
else if($row_count4[0] >0)
{
@mysql_close($config);
echo "$user has requested to friend with you first .Check your friend request.";
exit();
}
else
{
$sql="insert into friends (friend1,friend2,frienddate) values('$login_user','$user',now())";
$query=mysql_query($sql);
@mysql_close($config);
echo "friend request sent";
exit();
}
}
else if($_POST['type'] == "unfriend")
{
$sql="select count(id) from friends where friend1='$login_user' and friend2='$user' and accept='1' LIMIT 1";
$query=mysql_query($sql);
$row_count1= @mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$user' and friend2='$login_user' and accept='1' LIMIT 1";
$query=mysql_query($sql);
$row_count2=@mysql_fetch_row($query);
if($row_count1[0] > 0 || $row_count2[0]>0)
{
$sql="Delete from friends where friend1='$user' and friend2='$login_user' and accept='1' LIMIT 1";
$query=mysql_query($sql);
$sql1="Delete from friends where friend1='$login_user' and friend2='$user' and accept='1' LIMIT 1";
$query1=mysql_query($sql1);
@mysql_close($config);
echo "unfriend_ok";
exit();
}
}
else if($_POST['type'] == "cancel")
{
$sql="select count(id) from friends where friend1='$login_user' and friend2='$user' and accept='0' LIMIT 1";
$query=mysql_query($sql);
$row_count1= @mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$user' and friend2='$login_user' and accept='0' LIMIT 1";
$query=mysql_query($sql);
$row_count2=@mysql_fetch_row($query);
if($row_count1[0] > 0 || $row_count2[0]>0)
{
$sql="Delete from friends where friend1='$user' and friend2='$login_user' and accept='0' LIMIT 1";
$query=mysql_query($sql);
$sql1="Delete from friends where friend1='$login_user' and friend2='$user' and accept='0' LIMIT 1";
$query1=mysql_query($sql1);
@mysql_close($config);
echo "cancelok";
exit();
}
}
else
{
@mysql_close(config);
echo "No friendship could be found between your account and $user,therefore we cannot unfriend you.";
exit();
}
}

?>
<?php
$config="";
if (isset($_POST['action']) && isset($_POST['reqid']) && isset ($_POST['friend1'])){
	$reqid = preg_replace('#[^0-9]#', '', $_POST['reqid']);
	
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST['friend1']);
	$sql =  "SELECT COUNT(id) FROM register WHERE username='$user' AND activated='1' LIMIT 1";
	$query = mysql_query($sql);
	$exist_c = @mysql_fetch_row($query);
	if($exist_c[0] < 1){
		@mysql_close($config);
		echo "$user does not exist.";
		exit();
	}
	//Friend 1 is the one who is requesting friendship.
	if($_POST['action'] == "accept"){
		//Checks if they are already Friends.
		$sql = "SELECT COUNT(id) FROM friends WHERE friend1='$login_user' AND friend2='$user' AND accept='1' LIMIT 1";
		$query = mysql_query($sql);
		$row_count1 = @mysql_fetch_row($query);
		$sql = "SELECT COUNT(id) FROM friends WHERE friend1='$user' AND friend2='$login_user' AND accept='1' LIMIT 1";
		$query = mysql_query($sql);
		$row_count2 = @mysql_fetch_row($query);
		if($row_count1[0] > 0 || $row_count2[0] > 0){
			@mysql_close($config);
			echo "You are already friends with $user.";
			exit();
		} else {//If they are not friends.
		
			$sql = "UPDATE friends SET accept ='1' WHERE id = '$reqid' AND friend1='$user' AND friend2='$login_user' LIMIT 1";
			$query = mysql_query($sql);
			@mysql_close($config);
			echo "accept_ok";
			exit();
		}
	} else if($_POST['action'] == "reject")
	 {
		mysql_query("DELETE FROM friends WHERE id='$reqid' AND friend1='$user' AND friend2='$login_user' AND accept='0' LIMIT 1");
		@mysql_close();
		echo "reject_ok";//ajax request 
		exit();
	}
}
?>