<?php
include_once("loginstatus.php");
//Only logged in users are allowed to check their notifications
if($loginok != true || $login_user ==""){
	header("location: home.php");
	exit();
}
$notelist = "";
$sql = "SELECT * FROM notifications WHERE username LIKE BINARY '$login_user' ORDER BY notedate DESC";
$query = mysql_query($sql);
$numrows = @mysql_num_rows($query);
if($numrows < 1){
 $notelist = "You do not have any notifications.";
} else{
	while($row = @mysql_fetch_array($query, MYSQL_ASSOC)){
		$notesid = $row["id"];
		$initiator = $row["initiator"];
		$type = $row["type"];
		$post = $row["post"];
		$notedate = $row["notedate"];
		$notedate = strftime("%b %d, %y",strtotime($notedate));
		//Appending the result to the Dynamic variable
		$notelist .= "<p><a href='user.php?u=$initiator'>$initiator</a> | $type<br />$post</p>";
	}
}
//Last time the user checked his/her notifications.
mysql_query("UPDATE register SET notescheck=now() WHERE username='$login_user' LIMIT 1");
?><?php
$buddyrequests = "";
$sql = "SELECT * FROM friends WHERE friend2='$login_user' AND accept='0' ORDER BY frienddate ASC";
$query = mysql_query($sql);
$numrows = @mysql_num_rows($query);
if($numrows < 1){
	$buddyrequests = 'No friend requests';
} else {
	while($row = @mysql_fetch_array($query, MYSQL_ASSOC)){
		$reqID = $row["id"];
		$friend1 = $row["friend1"];
		$frienddate = $row["frienddate"];
		$frienddate = strftime("%B %d",strtotime($frienddate));
		$thumbquery = mysql_query("SELECT photo FROM register WHERE username = '$friend1' LIMIT 1");
		$thumbrow = @mysql_fetch_row($thumbquery);
		$friend1photo = $thumbrow[0];
		$friend1pic = '<img src = "user/'.$friend1.'/'.$friend1photo.'" alt="'.$friend1.'" class="user_pic">';
		if($friend1photo== NULL){
			$friend1pic= '<img src="images/avatardefault.jpg" alt="'.$friend1.'" class="user_pic">';
		}
		$buddyrequests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$buddyrequests .= '<a href="user.php?u='.$friend1.'">'.$friend1pic.'</a>';
		$buddyrequests .= '<div class="user_info" id="user_info_'.$reqID.'">'.$frienddate.' <a href="user.php?u='.$friend1.'">'.$friend1.'</a>
		requests friendship<br /><br />';
		$buddyrequests .= '<button onclick="buddyHandler(\'accept\',\''.$reqID.'\',\''.$friend1.'\',\'user_info_'.$reqID.'\')">accept</button> or';
		$buddyrequests .= '<button onclick="buddyHandler(\'reject\',\''.$reqID.'\',\''.$friend1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$buddyrequests .= '</div>';
		$buddyrequests .= '</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>

<meta charset ="UTF-8">

<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">

<script src="display.js"></script>
<script src="ajax.js"></script>
<script type="text/javascript">
function buddyHandler(action, reqid, friend1, elem){
	var confirmstat = confirm("Press OK to '"+action+"' this friend request.");
	if(confirmstat != true){
		return false;
	}
	_(elem).innerHTML = "Processing.....";
	var ajax = ajaxobj("POST","buddy.php");
	ajax.onreadystatechange = function(){
		if(ajaxReturn(ajax) == true){
			if(ajax.responseText == "\r\n\r\naccept_ok"){
				_(elem).innerHTML = "<b>Request Accepted!</b><br /> You are now friends";
			} else if(ajax.responseText == "\r\n\r\nreject_ok"){
				_(elem).innerHTML = "<b>Request Rejected!</b><br /> You chose to reject friendship with this user";
			} else{
				_(elem).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send("action="+action+"&reqid="+reqid+"&friend1="+friend1);
}
</script>
</head>
<body>
<?php include_once("pagetop.php"); ?>
<div id="pageMiddle">
	<div id="notesBox"><h2>Notifications</h2><?php echo $notelist; ?></div>
	<div id="friendReqBox"><h2>Friend Requests</h2><?php echo $buddyrequests; ?></div>
	<div style=clear:left;"></div>
</div>
<div id="pageBottom">&copy;2015 Charger Team</div>

</body>
</html>