<?php
include_once("loginstatus.php");
$u="";
if(isset($_GET["u"]))
{
	$u = preg_replace('#[^a-z0-9]#i','',$_GET['u']);
	}
	else{
		header("location:charger.php");
		exit();
		}
		$photo_form="";
		//check to see if the viewer is the account owner
		$owner="no";
		$uploadPhoto="";
		if($u== $login_user && $loginok== true)
		{
			$owner="yes";
			$uploadPhoto ='<form id="photo_form" enctype="multipart/form-data" method="post" action="gallery.php">';
			$uploadPhoto .='<h3>Hi '.$u.', add a new photo into one of the galleries</h3>';
			$uploadPhoto .='<b> Choose Photo Gallery:</b>';
			$uploadPhoto .='<select name="gallery" required>';
			$uploadPhoto .=		'<option value=""></option>';
			$uploadPhoto .=		'<option value="Myself">Myself</option>';
			$uploadPhoto .=		'<option value="Family">Family</option>';
			$uploadPhoto .=		'<option value="MyClicks">My Clicks</option>';
			$uploadPhoto .=		'<option value="Friends">Friends</option>';
			$uploadPhoto .=		'<option value="MyPaintings">My Paintings</option>';
			$uploadPhoto .='</select>';
			$uploadPhoto .='&nbsp; &nbsp; &nbsp;<b> Choose Photo:</b>';
			$uploadPhoto .='<input type="file" name="photo" accept="image/*" required>';
			$uploadPhoto .= '<p><input type="submit" value ="upload photo Now"></p>';
			$uploadPhoto .= '</form>';
			}
			//select the user galleries
			$galleryList="";
			$sql="SELECT DISTINCT photogallery from gallery where username='$u'";
			$query=mysql_query($sql);
			if(@mysql_num_rows($query)<1)
			{
				$galleryList="This user has not uploaded any photos yet.";
			}
			else
			{
				while($row=@mysql_fetch_array($query,MYSQL_ASSOC))
				{
				$gallery=$row["photogallery"];
				$queryCount=mysql_query("SELECT COUNT(id) FROM gallery where username='$u' AND photogallery='$gallery'");
				$rowCount=@mysql_fetch_row($queryCount);
				$count=$rowCount[0];
				$imgQuery=mysql_query("select imagename from gallery where username='$u' and photogallery='$gallery' order by uploadeddate DESC");
				$imgRow=@mysql_fetch_row($imgQuery);
				$file=$imgRow[0];
				$galleryList .='<div>';
				$galleryList .=	'<div onclick="showGallery(\''.$gallery.'\',\''.$u.'\')">';
				$galleryList .=		'<img src="user/'.$u.'/'.$file.'" alt="Cover photo">';
				$galleryList .=	'</div>';
				$galleryList .=	'<b>'.$gallery.'</b> ('.$count.')';
				$galleryList .= '</div>';
				}
			}
?>

<!DOCTYPE html>
<html>  
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $u;?>Photos</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style.css">
<style type="text/css">
form#photo_form
{
background:#F3FDD0;border:#AFD80E 1px solid;padding:20px;
}
div#galleries{}
div#galleries > div
{
float:left; margin:20px; text-align:center; cursor:pointer;
}
div#galleries > div > div
{
height:100px;overflow:hidden;
}
div#galleries > div > div > img
{
width:150px;cursor:pointer;
}
div#photos
{
display:none;border:#666 1px solid;padding:20px;
}
div#photos > div
{ float:left; width:125px; height:80px; overflow:hidden; margin:20px;
}
div#photos > div > img
{
width:125px; cursor:pointer;
}
div#picbox
{
display:none;padding-top:36px;
}
div#picbox > img
{
max-width:800px; display:block; margin:0px auto;
}
div#picbox > button
{
display:block; float:right; font-size:36px;padding:3px 16px;
} 
</style>
<script src="display.js"></script>
<script src="ajax.js"></script>
<script>
function showGallery(gallery,user)
{
	_("galleries").style.display="none";
	_("section_title").innerHTML=user+'&#39;s'+gallery+' Gallery &nbsp;<button onclick="backToGalleries()">Go Back</button>';
	_("photos").style.display="block";
	_("photos").innerHTML="Loading photos..."; 
	var ajax= ajaxobj("POST","gallery.php");
	ajax.onreadystatechange=function()
		{
			if(ajaxReturn(ajax)==true)
				{
					_("photos").innerHTML='';
					var pics=ajax.responseText.split("|||");
					for(var i=0;i<pics.length;i++)
					{
						var pic=pics[i].split("|");
						_("photos").innerHTML+='<div> <img onclick="photoShowcase(\''+pics[1]+'\')" src="user/'+user+'/'+pic[1]+'\"></div>';
						
					}
					
							_("photos").innerHTML+='<p style="clear:left;"></p>';			
				}
		}
		ajax.send("show=galpics&gallery="+gallery+"&user="+user);
}	
function backToGalleries()
{ 
	_("photos").style.display="none";
	_("section_title").innerHTML="<?php echo $u ?>&#39;s Photo Galleries";
	_("galleries").style.display="block";
}
function photoShowcase(picdata)
{
		var data=picdata.split("|");
		_("section_title").style.display="none";
		_("photos").style.display="none";
		_("picbox").style.display="block";
		_("picbox").innerHTML='<button onclick="closePhoto()">x</button>';
		_("picbox").innerHTML += '<img src="user/<?php echo $u; ?>/'+data[1]+'" alt="photo">';
		if("<?php echo $owner ?>"=="yes")
		{
			_("picbox").innerHTML +='<p id="deletelink"><a href="#" onclick="return false;"onmousedown="deletePhoto(\''+data[0]+'\')">Delete Photo <?php echo $u;?></a></p>';
		}
}
		
function closePhoto()
{
		_("picbox").innerHTML='';
		_("picbox").style.display="none";
		_("photos").style.display="block";
		_("section_title").style.display="block";
} 

function deletePhoto(id)
{
			var confirmstat=confirm("Press Ok to confirm the delete action on this photo. ");
			if(confirmstat!=true)
			{
				return false;
			}
				_("deletelink").style.visibility="hidden";
				var ajax=ajaxobj("POST","gallery.php");
				ajax.onreadystatechange = function()
				{
					if(ajaxReturn(ajax)==true)
					{
						if(ajax.responseText=='deleted_ok')
						{
							alert("This picture has been deleted successfully.we will now refresh the page for you.");
							windows.location="photos.php?u=<?php echo $u; ?>";
							}
						}
			
					}
					
					
					ajax.send("delete=delete&id="+id);
}
			
</script>
</head>
<body>
<?php include_once("pagetop.php");?>
<div id="pageMiddle">
<div id="photo_form"> <?php echo $uploadPhoto; ?></div>
<h2 id="section_title"><?php echo $u; ?>&#39;s Photo Galleries</h2>
<div id="galleries"><?php  echo $galleryList; ?></div>
<div id="photos"></div>
<div id="picbox"></div>
<p style="clear:left">These photos belong to <a href="user.php?u=<?php echo $u; ?>"><?php echo $u;?></a></p>
</div>
<div id="pageBottom">&copy;2015 Charger Team</div>
</body>
</html>