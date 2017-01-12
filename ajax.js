function ajaxobj(meth,url)
{
	var x=new XMLHttpRequest();
	x.open(meth,url,true);
	x.setRequestHeader("content-type","application/x-www-form-urlencoded");
	return x;
	
	}
	function ajaxReturn(x)
	{
		if(x.readyState==4 && x.status==200){
			return true;
		}
		}