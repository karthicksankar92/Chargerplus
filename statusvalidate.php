
<?php 
include_once("loginstatus.php");
if($loginok != true || $login_user=="")
{
exit();
}
?>
<?php
if(isset($_POST['action']) && $_POST['action']=="statpost")
{
// Make sure post data is not empty
if(strlen($_POST['data']) <1 )
{
@mysql_close($config);
echo "data empty";
exit();
}
//Make sure type is either a or c
if($_POST['type'] !="a" && $_POST['type'] !="c")
{
@mysql_close($config);
echo "type_unknown";
exit();
}
//clean all of the $_post vara that will interact with the database
$type=preg_replace('#[^a-z]#','',$_POST['type']);
$accname=preg_replace('#[^a-z0-9]#i','',$_POST['user']);
$data=htmlentities($_POST['data']);
$data=mysql_real_escape_string($data);
//Make sure account name exists
// insert the status
$sql="insert into status (postedon,postedby,category,content,postdate) values('$accname','$login_user','$type','$data',now())";
$query=mysql_query($sql);
$config="";
$id=@mysql_insert_id($config);
mysql_query("update status set pid='$id' where id='$id' LIMIT 1");
//Count posts of type "a"
$sql="select count(id) from status where postedby='$login_user' and category='a'";
$query=mysql_query($sql);
$row=@mysql_fetch_row($query);
if($row[0]> 9)
{
$sql="select id from status where postedby='$login_user' and category='a' order by id ASC LIMIT 1";
$query=mysql_query($sql);
$row=@mysql_fetch_row($query);
$oldest=$row[0];
mysql_query("Delete from status where pid='$oldest'");
}
//insert notification to all friends
$friends= array();
$query=mysql_query("select friend1 from friends where friend2='$login_user' and accept='1'");
while($row=@mysql_fetch_array($query, MYSQL_ASSOC) )
{
	array_push($friends,$row["friend1"]);
}
$query=mysql_query("select friend2 from friends where friend1='$login_user' and accept='1'");
while($row=@mysql_fetch_array($query, MYSQL_ASSOC))
{
	array_push($friends,$row["friend2"]);
}
for($i=0;$i < count($friends);$i++)
{
$friend=$friends[$i];
$app="Status Post";
$notification=$login_user.'posted on:<br/><a href="user.php?u='.$accname.'#stat'.$id.'">'.$accname. '</a>';
mysql_query("insert into notifications(username,initiator,type,post,notedate) values('$friend','$login_user','$app','$notification',now())");
}
@mysql_close($config);
echo "postok | $id";
exit();
}
?>
<?php
if(isset($_POST['action']) && $_POST['action'] =="stat_reply")
{
	//Make sure data is not empty
	if(strlen($_POST['data']) < 1){
			@mysql_close($config);
			echo "data_empty";
			exit();
	}
	//clean the posted variables
	$sid=preg_replace('#[^0-9]#','',$_POST['sid']);
	$accname = preg_replace('#[^a-z0-9]#i','',$_POST['user']);
	$data=htmlentities($_POST['data']);
	$data=@mysql_real_escape_string($data);
	// Make sure acc name exists
	
	$sql ="select count(id) from register where username='$accname' and activated='1' LIMIT 1";
	$query=@mysql_query($sql);
	$row=@mysql_fetch_row($query);
	if($row[0]<1)
	{
		@mysql_close($config);
		echo "$account_no_exists";
		exit();
	}
	//insert the status reply post
	$sql="insert into status(pid,postedon,postedby,category,content,postdate) values('$sid','$accname','$login_user','b','$data',now())";
	$query=@mysql_query($sql);
	$id=@mysql_insert_id($config);
	//insert notification for everybody
	$sql="select postedby from status where pid='$sid' and postedby!='$login_user' GROUP BY postedby";
	$query=@mysql_query($sql);
	while($row=@mysql_fetch_array($query, MYSQL_ASSOC))
	{
	$partici=$row["postedby"];
	$app="Status Reply";
	$notification=$login_user.'commented here:<br /><a href="user.php?u='.$accname.'stat'.$sid.'">click here to view the conversation</a>';
	@mysql_query("insert into notifications (username,initiator,type,post,notedate) values('$partici','$login_user','$app','$notification',now())");
	}
	@mysql_close($config);
	echo "replyok | $id";
	exit();
}

?>
<?php
if(isset($_POST['action']) && $_POST['action'] =="del_stat")
{
	if(!isset($_POST['statid']) || $_POST['statid']=="")
	{
		mysql_close($config);
		echo "status id is missing";
		exit();
	}
	$statid=preg_replace('#[^0-9]#','',$_POST['statid']);
	//check to make sure the owner's comment 
	$query=mysql_query("select postedon,postedby from status where id='$statid' LIMIT 1 ");
	$accname ="";
	$author = "";
	while($row=@mysql_fetch_array($query))
	{
		$accname=$row["postedon"];
		$author=$row["postedby"]; 
	}
	if($author == $login_user || $accname==$login_user)
	{
		mysql_query("Delete from status where id='$statid'");
		@mysql_close($config);
		echo "deleteok";
		exit();
	}
}

?>
<?php
if(isset($_POST['action']) && $_POST['action'] =="del_reply")
{
if(!isset($_POST['replyid']) || $_POST['replyid']=="")
{
@mysql_close($config);
exit();
}
$replyid=preg_replace('#[^0-9]#','',$_POST['replyid']);
//check to make sure the person deleting is owner or poster
$query=mysql_query("select pid,postedon,postedby from status where id='$replyid' LIMIT 1 ");
while($row=@mysql_fetch_array($query))
{
$sid=$row["pid"];
$accname=$row["postedon"];
$author=$row["postedby"]; 
}
if($author==$login_user || $accname==$login_user)
{
mysql_query("Delete from status where id='$replyid'");
@mysql_close($config);
echo "deleteok";
exit();
}
}

?>