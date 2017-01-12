

<!DOCTYPE html>
<html>
<head>

<meta charset ="UTF-8">
<title>Sign Up</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">

<script src="display.js"></script>
<script src="ajax.js"></script>
<script src="empty.js"></script>
<script>

function signup()
{
var e=_("email").value;
var p1=_("pass1").value;
var p2=_("pass2").value;
var que=_("sec").value;
var ans=_("ans").value;

var status=_("status");
if(e == "" || p1 == "" || p2 == ""|| que=="" || ans==""  )
	{
	status.innerHTML ="Fill out all the form data";
	}
else if(p1 !=p2)
	{
	status.innerHTML="Your password fields do not macth";
	}

else 
	{
_("signupbtn").style.display="none";
status.innerHTML="Please Wait....";
var ajax =ajaxobj("POST","validate1.php");
ajax.onreadystatechange=function()
		{
		if(ajaxReturn(ajax)==true)
			{
			if(ajax.responseText !="success")
				{
	        	status.innerHTML=ajax.responseText;	
				_("signupbtn").style.display="block";	
  				}
			else if (ajax.responseText =="wrong")
			{
			status.innerHTML="Security question and answer mismatched";
			}
			else
				{
			alert("Password Changed Successfully");
			status.innerHTML="Now login using your new password";
				}
			}
		}
		ajax.send("&e="+e+"&p="+p1+"&que="+que+"&ans="+ans);
	}
}
</script>
<link rel="stylesheet" type="text/css" href="my.css" />

</head>
<body>

	<center><h1><u>Change Password</u></h1></center>
	<form name="signupform" id="signupform" onSubmit="return false;">
<div>Email:</div>
<input id="email" type= "text"  onfocus="emptyElement('status')"  maxlength="88">
<div>Create Password:</div>
<input id="pass1" type= "password"  onfocus="emptyElement('status')"  maxlength="16">
<div>Confirm Password:</div>
<input id="pass2" type= "password"  onfocus="emptyElement('status')"  maxlength="16">
<div>Security Question</div>
<select id="sec" onfocus="emptyElement('status')">
<option value="What was your childhood nickname?">1.What was your childhood nickname?</option>
<option value="In what city did you meet your spouse/significant other?">2.In what city did you meet your spouse/significant other?</option>
<option value="What is the name of your favorite childhood friend?">3.What is the name of your favorite childhood friend?</option>
<option value="What street did you live on in third grade?">4.What street did you live on in third grade?</option>
<option value="What was the name of your first stuffed animal?">5.What was the name of your first stuffed animal?</option>
<option value="What is the middle name of your oldest child?">6.What is the middle name of your oldest child?</option>
<option value="What is your oldest sibling's middle name?">7.What is your oldest sibling's middle name? </option>
<option value="What school did you attend for sixth grade?">8.What school did you attend for sixth grade?</option>
</select>
<div>Answer:</div>
<input id="ans" type="answer" name="answer" value="" onfocus="emptyElement('status')" >
<input id="signupbtn" type="submit"  name="Change Password " value="Change password" onClick="signup()">
<span id="status"></span>
	</form>
</body>
</html>