function validateANumber()
{
var Ano=_("Ano").value;
if(Ano != "" )
   {
	_("Anostatus").innerHTML ='Checking...';
	var ajax = ajaxobj("POST","signup.php");
	ajax.onreadystatechange =function()
	   {
		if(ajaxReturn(ajax)==true)
			{
			_("Anostatus").innerHTML =ajax.responseText;
			}
		}
		ajax.send("validateANumber="+Ano);
	}
}