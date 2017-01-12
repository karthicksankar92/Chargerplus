<?php
include_once("loginstatus.php");
if($loginok != true  || $login_user == ""){
	exit();
}
?>
<?php
if(isset($_POST["show"]) && $_POST["show"] =="galpics")
{
$photostring="";
$gallery=preg_replace('#[^a-z 0-9,]#i','',$_POST["gallery"]);
$user= preg_replace('#[^a-z0-9]#i','',$_POST["user"]);
$sql="select * from gallery where username='$user' and photogallery='$gallery' order by uploadeddate ASC";
$query=mysql_query($sql);
while($row=@mysql_fetch_array($query))
{
$id=$row["id"];
$imagename= $row["imagename"];
$caption=$row["caption"];
$uploadeddate=$row["uploadeddate"];
$photostring .="$id|$imagename|$caption|$uploadeddate|||";
}
@mysql_close($config);
$photostring =trim($photostring,"|||");
echo $photostring;

exit();
}

?>

<?php
if(isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"] !="")
{
	$imgName = $_FILES["avatar"]["name"];
	$imgTempLoc =$_FILES["avatar"]["tmp_name"];
	$imgType = $_FILES["avatar"]["type"];
	$imgSize = $_FILES["avatar"]["size"];
	$imgErrorMsg = $_FILES["avatar"]["error"];
	$extension = explode(".",$imgName);
	//grabs the last element in the array
	$imgExt = end($extension);
 	list($width,$height) = getimagesize($imgTempLoc);
	if($width < 10 || $height < 10)
	{
		header("location: message.php?msg=ERROR: That image has no dimension");
		exit();
	}
	$img_name = rand(100000000000,999999999999).".".$imgExt;
	if($imgSize > 1048576)
	{
		header("location: message.php?msg=ERROR: Your image file is larger than 1MB");
		exit();
	}
	else if(!preg_match("/\.(gif|jpg|png)$/i",$imgName))
	{
		header("location: message.php?img=ERROR: Your image file is not jpg,gif or png type");
		exit();
	}
	else if($imgErrorMsg==1)
	{
		header("location: message.php?msg=Error: An unknown error occurred");
		exit();
	}
	$sql = "SELECT photo FROM register WHERE username='$login_user' LIMIT 1";
	$query = mysql_query($sql);
	$row = @mysql_fetch_row($query);
	$avatar = $row[0];
	if($avatar!="")
	{
		$picurl="user/$login_user/$avatar";
		echo "$avatar";
		if(file_exists($picurl)) 
		{
			unlink($picurl);
		}
	}
	$changeLoc = move_uploaded_file($imgTempLoc,"user/$login_user/$img_name");
	if($changeLoc != true)
	{
		header("location: message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("imageResize.php");
	$actual_file = "user/$login_user/$img_name";
	$modified_file = "user/$login_user/$img_name";
	$widthMax = 100;
	$heightMax = 100;
	img_resize($actual_file,$modified_file,$widthMax,$heightMax,$imgExt);
	$sql = "UPDATE register SET photo='$img_name' WHERE username='$login_user' LIMIT 1";
	$query = mysql_query($sql);
	@mysql_close($config);
	header("location:user.php?u=$login_user");
	exit();
}
?>
<?php 
if(isset($_FILES["photo"]["name"]) && isset($_POST["gallery"]))
{
$sql="select count(id) from gallery where username='$login_user'";
$query=mysql_query($sql);
$row = @mysql_fetch_row($query);
if($row[0] > 14)
{
header("location:message.php?msg=You can load upto 15 pictures.");
exit();
}
$gallery= preg_replace('#[^a-z 0-9,]#i','',$_POST["gallery"]);
$imgName =$_FILES["photo"]["name"];
$imgTempLoc=$_FILES["photo"]["tmp_name"];
$imgSize=$_FILES["photo"]["size"];
$imgType=$_FILES["photo"]["type"];
$imgErrorMsg=$_FILES["photo"]["error"];
$extension = explode(".", $imgName);
$imgExt = end($extension);
//To ensure no two images have the same name.
	$img_name = date("DMjGisY")."".rand(1000,9999).".".$imgExt; //WedFeb272120452015RAND.jpg
	list($width, $height) = getimagesize($imgTempLoc);
	if($width < 10 || $height < 10){
		header("location:message.php?msg=ERROR: That image has no dimensions");
		exit();
	}
	if($imgSize > 1048576){
		header("location:message.php?msg=ERROR: Your image file is larger than 1MB");
		exit();	
	} else if(!preg_match("/\.(gif|jpg|png)$/i", $imgName)){
		header("location:message.php?msg=ERROR: Your image file is not jpg, gif, or png type");
		exit();
	} else if($imgErrorMsg == 1){
		header("location:message.php?msg=ERROR: An unknown error occured");
		exit();
	}
	$changeLoc = move_uploaded_file($imgTempLoc, "user/$login_user/$img_name");
	if($changeLoc != true){
		header("location:message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("imageResize.php");
	$widthMax = 800;
	$heightMax = 600;
	if($width > $widthMax || $height > $heightMax){
		$actual_file ="user/$login_user/$img_name";
		$modified_file ="user/$login_user/$img_name";
		img_resize($actual_file, $modified_file, $widthMax, $heightMax, $imgExt);
	}
	$sql = "INSERT INTO gallery (username, photogallery, imagename, uploadeddate) VALUES('$login_user','$gallery','$img_name',now())";
	$query = mysql_query($sql);
	@mysql_close($config);
	header("location:photos.php?u=$login_user");
	exit();
}
?><?php
//Deletes
if(isset($_POST["delete"]) && $_POST["id"] != ""){
	$id = preg_replace('#[^0-9]#', '', $_POST["id"]);
	$query = mysql_query("SELECT username, imagename FROM gallery WHERE id='$id' LIMIT 1");
	$row = mysql_fetch_row($query);
	$user = $row[0];
	$imgname= $row[1];
	//To verify if it's the owner who is deleting the avatar.
	if($user == $login_user){
		$picurl = "user/$login_user/$imgname";
		if(file_exists($picurl)){
			//Unlink in the folder system if you are deleting the pic.
			unlink($picurl);
			$sql = "DELETE FROM gallery WHERE id = '$id' LIMIT 1";
			$query = mysql_query($sql);
		}
	}
	@mysql_close($config);
	echo "deleted_ok";
	exit();
}
?>



<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
</html>