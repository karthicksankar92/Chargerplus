<?php 
include_once("loginstatus.php");
//Initialize variable 
$u="";
$sex="Male";
$ulevel="";
$profilePic="";
$profilePicbtn="";
$photoform="";
$joindate="";
$lastsession="";
//Username is set
if(isset($_GET["u"]))
{
$u=preg_replace('#[^a-z0-9]#i','',$_GET['u']);
}
else
{
header("location:charger.php");
exit();
}
//select the user
$sql="select * from register where username='$u' and activated='1' LIMIT 1 ";
$user_query=mysql_query($sql) or die(mysql_error());
//Make sure user exists
$numrows=@mysql_num_rows($user_query);
if($numrows < 1)
{
echo "User does not exist,press back";
exit();
}
//check to see if the viewer is owner
$owner ="no";
if($u==$login_user && $loginok==true)
{
	$owner="yes";
	$profilePicbtn = '<a href="#" onclick="return false;" onmousedown= "editProfilepic(\'photoform\')">Edit Profile Pic</a>';
	$photoform  = '<form id="photoform" enctype= "multipart/form-data" method="post" action="gallery.php">';
	$photoform  .=  '<h4>Change your Profile Picture</h4>';
	$photoform  .=  '<input  type="file"  name="avatar"  required >';
	$photoform  .=  '<p><input  type="submit"  value="Upload"></p >';
	$photoform  .= '</form >';	
}

//Fetch the user values
while($row=@mysql_fetch_array($user_query,MYSQL_ASSOC))
{
$profileid=$row["id"];
$gender=$row["gender"];
$ulevel=$row["usertype"];
$signup=$row["signup"];
$lastlogin=$row["lastlogin"];
$joindate=strftime("%b %d %Y",strtotime($signup));
$lastsession=strftime("%b %d %Y",strtotime($lastlogin));
if($gender=='f' || $gender == 'F')
{
$sex="Female";
}
} 
?>

<?php
$buddyHTML ='';
$buddy_view='';
$sql = "SELECT COUNT(id) FROM friends WHERE friend1='$u' AND accept ='1' OR friend2='$u' AND accept='1'";
$query = mysql_query($sql);
$query_count = @mysql_fetch_row($query);
$buddycount = $query_count[0];
if($buddycount < 1)
{
	$buddyHTML = $u." has no friends yet";
} else {
	$maxbuddy = 18;
	$allbuddies = array();
	$sql = "SELECT friend1, friend2 FROM friends WHERE friend2='$u' AND accept='1' ORDER BY RAND() LIMIT $maxbuddy";
	$query = mysql_query($sql);
	while($row = @mysql_fetch_array($query, MYSQL_ASSOC)){
		array_push($allbuddies, $row["friend1"]);
	}
	
	$sql = "SELECT friend2 FROM friends WHERE friend1='$u' AND accept='1' ORDER BY RAND() LIMIT $maxbuddy";
	$query = mysql_query($sql);
	while($row = @mysql_fetch_array($query, MYSQL_ASSOC)){
		array_push($allbuddies, $row["friend2"]);
	}
	$buddyArrayCount = count($allbuddies);
	if($buddyArrayCount > $maxbuddy){
		array_splice($allbuddies, $maxbuddy);
	}
	if($buddycount > $maxbuddy){
		array_splice($allbuddies, $maxbuddy);
		$buddy_view  = '<a href="viewbuddy.php?u='.$u.'">View all<a/>';
	}
	$or = '';
	foreach($allbuddies as $key => $user){
		$or .= "username='$user' OR ";
	}
	//Compound query
	$or = chop($or, "OR ");
	$sql = "SELECT username, photo FROM register WHERE $or";
	$query = mysql_query($sql);
	while($row = @mysql_fetch_array($query)){
		$buddyusername = $row["username"];
		$buddyphoto = $row["photo"];
		if($buddyphoto != ""){
			$buddypic = 'user/'.$buddyusername.'/'.$buddyphoto.'';//to get the custom picture
		} else {
			$buddypic = 'default.jpg';//the default picture
		}
		//All friends go to buddyHTML
		$buddyHTML .= '<a href="user.php?u='.$buddyusername.'"><img class="friendpics" src="'.$buddypic.'" alt="'.$buddyusername.'" title="'.$buddyusername.'"></a>';
	}
}
?>

<?php 
$isbuddy=false;
$ownerBlockViewer=false;
$viewerBlockowner=false;
$requestalreadysent=false;
  if($u !=$login_user && $loginok ==true)
{
$buddycheck ="SELECT COUNT(id) FROM friends WHERE friend1='$login_user' AND friend2='$u' AND accept='1' LIMIT 1";
$query=mysql_query($buddycheck);
$row_count1 = @mysql_fetch_row($query);
$sql = "SELECT COUNT(id) FROM friends WHERE friend1='$u' AND friend2='$login_user' AND accept='1' LIMIT 1";
$query = mysql_query($sql);
$row_count2 = @mysql_fetch_row($query);
if($row_count1[0] > 0 || $row_count2[0] > 0)
{
$isbuddy=true;
@mysql_close($query);
}
$block_check2="select count(id) from userblock where blockedby='$u' and blockeduser='$login_user' LIMIT 1";
$query=mysql_query($block_check2);
$blockcount1 =@mysql_fetch_row($query);
$sql="select count(id) from userblock where blockedby='$login_user' and blockeduser='$u' LIMIT 1";
$query=mysql_query($sql);
$blockcount2=@mysql_fetch_row($query);
 	if($blockcount1[0] > 0 || $blockcount2[0] > 0)
	{
 			$viewerBlockowner=true;
			@mysql_close($query);
 	}
$pendingrequest="select count(id) from friends where friend1='$login_user' and friend2='$u' and accept ='0' LIMIT 1";
$query=mysql_query($pendingrequest);
$row_count3=@mysql_fetch_row($query);
$sql="select count(id) from friends where friend1='$u' and friend2='$login_user' and accept ='0' LIMIT 1";
$query=mysql_query($sql);
$row_count4=@mysql_fetch_row($query);
if($row_count3[0] > 0 || $row_count4[0] > 0)
{
$requestalreadysent=true;
@mysql_close($query);
}
	
}
?>
<?php 
$buddybutton='<button disabled>Request Friendship</button>';
$blockbutton='<button disabled>Block User</button>';
//Logic for friend button
if($isbuddy == true) 
{
	$buddybutton='<button onclick="buddyToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
}
else if($loginok == true && $u!= $login_user && $ownerBlockViewer == false)
{
	$buddybutton='<button onclick="buddyToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request Friendship</button>';
}
// Logic for Block Button 
if($viewerBlockowner == true)
{
	$blockbutton='<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock user </button>';
}
else if($loginok == true && $u!= $login_user)
{
	$blockbutton='<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block user </button>';
}
// Logic for cancel request
if($requestalreadysent == true) 
{
	$buddybutton='<button onclick="buddyToggle(\'cancel\',\''.$u.'\',\'friendBtn\')">Cancel Pending Request</button>';
}

?>
<?php
$coverpic ="";
$sql="select imagename from gallery where username='$u' ORDER BY RAND() LIMIT 1";
$query=mysql_query($sql);
if(@mysql_num_rows($query)>0)
{
	$row=@mysql_fetch_row($query);
	$imagename=$row[0];
	$coverpic='<img src="user/'.$u.'/'.$imagename.'" alt="pic">';
	}
?>
<?php
$profilePic ="";
$sql="select photo from register where username='$u' ORDER BY RAND() LIMIT 1";
$query=mysql_query($sql);
if(@mysql_num_rows($query)>0)
{
	$row=@mysql_fetch_row($query);
	$imageN=$row[0];
	$profilePic='<img src="user/'.$u.'/'.$imageN.'" alt="pic">';
}
?>

<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $u;?></title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style.css">
<script src="display.js"></script>
<script src="ajax.js"></script>
<script type="text/javascript"> 
function buddyToggle(type,user,elem)
{
	var confirmstat =confirm("Press ok to confirm the '"+type+"' action for user <?php echo $u;?>.");
	if(confirmstat !=true)
	{
		return false;
	}
	
	_(elem).innerHTML ="Please wait...";
	var ajax = ajaxobj("POST","buddy.php");
	ajax.onreadystatechange=function()
		{
			if(ajaxReturn(ajax)==true)
				{
					if(ajax.responseText =="\r\n\r\nfriend_request_sent ")
						{
							_(elem).innerHTML='Friend Request sent to';
							_(elem).innerHTML='<button onclick="buddyToggle(\'cancel\',\'<?php echo $u; ?>\',\'friendBtn\')">Cancel Pending Request</button>';	
									
						}
					else if (ajax.responseText =="unfriend_ok")
						{
						alert("You unfriended \'<?php echo $u ;?>\'");
							_(elem).innerHTML='<button onclick="buddyToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Unfriend</button>';
							
						
						}
						else if (ajax.responseText =="\r\n\r\ncancelok")
						{
						alert("Your pending friend request to \'<?php echo $u ;?>\' has been cancelled");
						_(elem).innerHTML='<button onclick="buddyToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request friendship</button>';
							
						
						}
					else
						{
						alert("Your Friend request sent to \'<?php echo $u ;?>\'");		
							_(elem).innerHTML='<button onclick="buddyToggle(\'cancel\',\'<?php echo $u; ?>\',\'friendBtn\')">Cancel Request Pending</button>';	
						  
						}
				}
		}
		ajax.send("type="+type+"&user="+user);
}	

function blockToggle(type,blockee,elem)
{
	var confirmstat =confirm("Press Ok to confirm the '"+type+"' action on user <?php echo $u;?>.");
	if(confirmstat !=true)
	{
		return false;
	}
	var elem=document.getElementById(elem);
	elem.innerHTML ="Please wait while we are processing your request...";
	var ajax= ajaxobj("POST","blockuser.php");
	ajax.onreadystatechange=function()
	{
		if(ajaxReturn(ajax)==true)
			{
				if(ajax.responseText =="\r\nblocked_ok")
				{
	        		
					
					elem.innerHTML='<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock user</button>';
					alert("You blocked \'<?php echo $u ;?>\'");		
  				}
			    else if (ajax.responseText =="\r\nunblocked_ok")
				{
					alert("You unblocked \'<?php echo $u ;?>\'");	
					elem.innerHTML='<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
					
				}
			
			}
	 }
		ajax.send("type="+type+"&blockee="+blockee);
}	

</script>


</head>
<body>
<?php include_once("pagetop.php");?>
<div id="pageMiddleRest">
<div id = "profile_pic_box">
	
    
	<?php echo $profilePicbtn;?>
    <?php echo $photoform;?>
    <?php echo $profilePic;?>
    
    
</div> 
<div id="photo_showcase" onclick="window.location='photos.php?u=<?php echo $u;?>';" title="view <?php echo $u;?>">
<?php echo $coverpic;?>
</div>
<h3 style="color:blue;">Welcome <?php echo $u; ?></h3>
<p>Join date :<?php echo $joindate;?></p>
<p>Last session:<?php echo $lastsession; ?></p>
<hr />
<p>Request:<span id ="friendBtn"><?php echo $buddybutton;?></span> <?php echo $u. " has ".$buddycount." friends"; ?> <?php echo $buddy_view;?></p>
<p>Block:<span id="blockBtn"><?php echo $blockbutton; ?></span></p>
<hr />
<p><?php echo $buddyHTML;?></p>
<hr />
<?php include_once("status.php");?>
</div>

<div id="pageBottom">&copy;2015 Charger Team</div>

</body>
</html>