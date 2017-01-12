<?php
$ustatus="";
$statli="";
if($owner =="yes")
{
	$ustatus='<textarea id="statxt" onkeyup="statmax(this,250)" placeholder="What&#39;s now with you'.$u.'?"></textarea>';
	$ustatus.='<button id="statbtn" onClick="poststat(\'statpost\',\'a\',\''.$u.'\',\'statxt\')">Post</button>';
	}
	else //if($isbuddy==true && $login_user !=$u)
	{
		
		$ustatus='<textarea id="statxt" onkeyup="statmax(this,250)" placeholder="Hi'.$login_user.',Say something to '.$u.'"></textarea>';
		$ustatus.='<button id="statbtn" onClick="poststat(\'statpost\',\'c\',\''.$u.'\',\'statxt\')">Post status</button>';
		}
?>
<?php 
$sql="select * from status where postedon='$u' and category='a' or postedon='$u' and category='c' order by postdate DESC LIMIT 20";
$query=mysql_query($sql);
$numrow=@mysql_num_rows($query);

while($row=@mysql_fetch_array($query))
{
$statid=$row["id"];
$accname=$row["postedon"];
$author=$row["postedby"];
$postdate=$row["postdate"];
$data=$row["content"];
$data=nl2br($data);
$data=str_replace("&amp;","&",$data);
$data=stripslashes($data);
$statdelbtn='';
if($author == $login_user || $accname==$login_user)
{
	$statdelbtn='<span id="sd'.$statid.'"><a href="#" onClick="return false;" onmousedown="delstatus(\''.$statid.'\');" title="Delete this status and Its replies">delete status</a></span> &nbsp; &nbsp;';
	}
	//Gather up status rep
	$statrep="";
	$query1=mysql_query("select * from status where pid='$statid' and category='b' order by postdate ASC");
	$numrows1=@mysql_num_rows($query1);
	if($numrows1 >0) 
	{
		while($row2=@mysql_fetch_array($query1))
		{
			$statrepid=$row2["id"];
			$repauthor=$row2["postedby"];
			$repdata=$row2["content"];
			$repdata=nl2br($repdata);
			$reppostdate=$row2["postdate"];
			$repdata=str_replace("&amp;","&",$repdata);
			$repdata=stripslashes($repdata);
			$repdelbtn='';
			if($repauthor==$login_user || $accname==$login_user)
			{
				$repdelbtn='<span id="sdb'.$statrepid.'"><a href="#" onclick="return false;" onmousedown="delstatus(\''.$statrepid.'\',\'rep'.$statrepid.'\');" title ="Delete this comment">remove</a></span>';
			}
				$statrep .= '<div id="rep'.$statrepid.'" class="repbox"><div><b>Reply By <a href="user.php?u='.$repauthor.'">'.$repauthor.'&nbsp;&nbsp;&nbsp;'.$reppostdate.'&nbsp;&nbsp;&nbsp;</b>'.$repdelbtn.'<br/></a>'.$repdata.'</div></div>';
				}
	}
		$statli .= '<div id="stat'.$statid.'" class ="statbox"><div><b>Posted by <a href="user.php?u='.$author.'">'.$author.'&nbsp;&nbsp;&nbsp;'.$postdate.'&nbsp;&nbsp;&nbsp;</b>'.$statdelbtn.'<br /></a>'. $data.'</div>'.$statrep.'</div>';
		//if($isbuddy == true || $login_user == $u)
		//{
			$statli.='<textarea id="reptxt'.$statid.'" class="reptxt" onkeyup="statmax(this,250)" placeholder="Write a comment here"></textarea><button id="repbtn'.$statid.'" onclick="replystatus('.$statid.',\''.$u.'\',\'reptxt'.$statid.'\',this)">Reply</button>';
			//}
		}

?>
<script>

function poststat(action,type,user,ta)
{
	var data=_(ta).value;
	if(data=="")
	{
		alert("Type something first");
		return false;
	}
	_("statbtn").disabled=true;
	var ajax=ajaxobj("POST","statusvalidate.php");
	ajax.onreadystatechange= function()
	{
		if(ajaxReturn(ajax)== true)
		{
			var datArray=ajax.responseText.split("|");
			if(datArray[0]=="postok")
			{
				var sid =datArray[1];
				data=data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				var currentHTML=_("statarea").innerHTML; 
				_("statarea").innerHTML='<div id="stat'+sid+'" class="statbox"><div><b>Posted by you now:</b> <span id="sd'+sid+'">< a href="#" onclick="return false;" 								                onmousedown="delstatus(\''+sid+'\',\'stat'+sid+'\');" title ="Delete this status and its replies"> delete status</a></span><br/>'+data+'</div></div><textarea                 id="reptxt'+sid+'" class="reptxt" onkeyup="statmax(this,250)" placeholder="write a comment here"></textarea><button id="repbtn'+sid+'" onclick="replystat('+sid+',\'                <?php echo $u;?>\',\'reptxt'+sid+'\',this)">Reply</button>'+currentHTML;
	            _("statbtn").disabled=false;
	            _(ta).value="";
	        }
	        else{
	            alert(ajax.responseText);
	        }
	   }
    }
	ajax.send("action="+action+"&type="+type+"&user="+user+"&data="+data);
}


function replystatus(sid,user,ta,btn)
{
	var data=_(ta).value; 
	if(data=="")
	{
		alert("Type something first");
		return false;
	}
	_("repbtn"+sid).disabled=true;
	var ajax=ajaxobj("POST","statusvalidate.php");
	ajax.onreadystatechange= function()
	{
		if(ajaxReturn(ajax)== true)
		{
			var datarray=ajax.responseText.split("|");
			if(datarray[0]=="replyok")
			{
				var rid=datarray[1];
				data=data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				_("stat"+sid).innerHTML += '<div id="rep'+rid+'" class="repbox"><div><b>Reply by you just now:</b><span id="sdb'+rid+'"><a href="#" onclick="return false;"                 onmousedown="delreply(\''+rid+'\',\'rep'+rid+'\');" title ="Delete this comment ">Remove</a></span><br/>'+data+'</div></div>';
				_("repbtn"+sid).disabled=false;
				_(ta).value="";
			}
			else{
				alert(ajax.responseText);
			}
		}
	}
	ajax.send("action=stat_reply&sid="+sid+"&user="+user+"&data="+data);
}

function delreply(repid,repbox)
{
	var conf=confirm("press ok to confirm deletion of this reply");
	if(conf!=true)
	{
		return false;
		}
	var ajax= ajaxobj("POST","statusvalidate.php");
	ajax.onreadystatechange=function()
	{
		if(ajaxReturn(ajax)==true)
			{
				if(ajax.responseText =="deleteok")
				{
					
					_(repbox).style.display='none';
				}
				else
				{
					alert(ajax.responseText);
					}
	        		
				}
			}
	
		ajax.send("action=del_reply&repid="+repid);
	}
	
function delstatus(statid,statbox)
{
	var confirmstat=confirm("press ok to confirm deletion of this status and its replies");
	if(confirmstat!=true)
	{
		return false;
	}
	var ajax= ajaxobj("POST","statusvalidate.php");
	ajax.onreadystatechange=function()
	{
		if(ajaxReturn(ajax)==true)
			{
				if(ajax.responseText =="deleteok")
				{
					_(statbox).style.display='none';
					_("reptxt"+statid).style.display='none';
					_("repbtn"+statid).style.display='none';
				}
				else
				{
					alert(ajax.responseText);
				}
	        		
		}
	}
	
	ajax.send("action=del_stat&statid="+statid);
}
	
function statmax(field,maxlimit)
	{
  if(field.value.length > maxlimit)
   {
	alert(maxlimit+"maximum character limit reached");
	field.value = field.value.substring(0,maxlimit);
	}
}
</script>
<div id="ustatus">
<?php echo $ustatus; ?> 
</div>
<div id="statarea">
<?php echo $statli; ?>
</div>


<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css">
</head>
</html>