function validateusername()
{
var u=_("username").value;
if(u != "" )
   {
	_("unamestatus").innerHTML ='Checking...';
	var ajax = ajaxobj("POST","signup.php");
	ajax.onreadystatechange =function()
	   {
		if(ajaxReturn(ajax)==true)
			{
			_("unamestatus").innerHTML =ajax.responseText;
			}
		}
		ajax.send("validateusername="+u);
	}
}