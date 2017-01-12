<?php
session_start();
//session handling
if(isset($_SESSION["username"]))
{
header("location:message.php?msg=NO to that weenis");
exit();
}
?>

<?php
//Ajax calls this Ano Check code to execute
if(isset($_POST["validateANumber"]))
{
	include_once("config.php");
	$Ano = preg_replace('#[aA][^0-9]#i','',$_POST['validateANumber']);
	$sql = "select id from register where Ano='$Ano' LIMIT 1" ;
	$query = mysql_query($sql);
	$Ano_check = @mysql_num_rows($query);
	
	if(strlen($Ano)!= 9)
	{
		echo '<strong style="color:#F00;">Please enter a valid 9 character A number </strong>';
		exit();
	}
	if($Ano[0]!='a' && $Ano[0]!='A')
	{
		echo '<strong style="color:"F00;"> Ano  must begin with a letter <b>A</b></strong>';
		exit();
	}
	if($Ano_check < 1)
	{
		echo '<strong style ="color:#009900;">' .$Ano.' is OK </strong>';
		exit();
	}
	else
	{
		echo '<strong style ="color:#F00;">'.$Ano.'is taken </strong>';
		exit();
	}
}
?>
<?php
//username is validated using ajax
if(isset($_POST["validateusername"]))
{
include_once("config.php");
$username = preg_replace('#[^a-z0-9]#i','',$_POST['validateusername']);
$sql = "select id from register where username='$username' LIMIT 1" ;
$query=mysql_query($sql);
$unamerow=@mysql_num_rows($query);
if(strlen($username)<3 || strlen($username)> 16)
{
echo '<strong style="color:#F00;">3 - 16 characters please </strong>';
exit();
}
if(is_numeric($username[0]))
{
echo '<strong style="color:"F00;> Username must begin with a letter</strong>';
exit();
}
if($unamerow<1)
{
echo '<strong style ="color:#009900;">' .$username.' is OK </strong>';
exit();
}
else
{
echo '<strong style ="color:#F00;">'.$username.'is taken </strong>';
exit();
}
}
?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<title>Sign Up</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style.css">
<script src="display.js"></script>
<script src="ajax.js"></script>
<script src="fade.js"></script>
<script src="restrict.js"></script>
<script src="empty.js"></script>
<script src="validateusername.js"></script>
<script src="terms.js"></script>
<script src="ano.js"></script>
<script>

function signup()
{
var Ano=_("Ano").value;
var fname=_("firstname").value;
var lname=_("lastname").value;
var u=_("username").value;
var e=_("email").value
var p1=_("pass1").value;
var p2=_("pass2").value;
var date=_("date").value;
var month=_("month").value;
var year=_("year").value;
var g=_("gender").value;
var d=_("department").value;
var c=_("courselevel").value;
var que=_("sec").value;
var ans=_("ans").value;

var status=_("status");
if(Ano == "" || fname == "" || lname == "" || u == "" || e ==""|| p1 == "" || p2 == "" || date == "" ||month=="" || year== "" || g == "" || d == "" || c == "" || que =="" || ans=="")
	{
	status.innerHTML ="Please fill all the details";
	}
else if(p1 !=p2)
	{
	status.innerHTML="Password mismatch";
	}
else if(_("terms").style.display=="none")
	{
status.innerHTML="Please view the terms of use";
	}
else 
	{
_("signupbtn").style.display="none";
status.innerHTML="Please Wait....";
var ajax =ajaxobj("POST","validate.php");
ajax.onreadystatechange=function()
		{
		if(ajaxReturn(ajax)==true)
			{
			if(ajax.responseText !="onsuccess")
				{
	        	status.innerHTML=ajax.responseText;	
				_("signupbtn").style.display="block";	
  				}
			else
				{
			window.scrollTo(0,0);
			_("signupform").innerHTML="OK " +u+",Check your email inbox and junk mail box at <u>"+e+"</u> in moment to sign up process by activating your account.You will not be able to do anything on the site until you successfully activated.";
				}
			}
		}
		ajax.send("Ano="+Ano+"&fname="+fname+"&lname="+lname+"&u="+u+"&e="+e+"&p="+p1+"&date="+date+"&month="+month+"&year="+year+"&g="+g+"&d="+d+"&c="+c+"&que="+que+"&ans="+ans);
	}
}
</script>



</head>
<body>
<?php include_once("pagetop.php");?>
<div id="pageMiddle">
<h3>Sign Up </h3>
<form name="signupform" id="signupform" onSubmit="return false;">
<div>A Number:</div>
<input id="Ano" type="text"  onBlur="validateANumber()" onKeyUp="restrict('Ano')" maxlength="9">
<span id="Anostatus"></span>
<div>First Name:</div>
<input id="firstname" type= "text"  onfocus="emptyElement('status')"  maxlength="16">
<div>Last Name:</div>
<input id="lastname" type= "text"  onfocus="emptyElement('status')"  maxlength="16">
<div>Username:</div>
<input id="username" type="text" onBlur="validateusername()" onKeyUp="restrict('username')"   maxlength="16">
<span id="unamestatus"></span>
<div>Email:</div>
<input id="email" type= "text"  onfocus="emptyElement('status')" onKeyUp= "restrict('email')" maxlength="88">
<div>Create Password:</div>
<input id="pass1" type= "password"  onfocus="emptyElement('status')"  maxlength="16">
<div>Confirm Password:</div>
<input id="pass2" type= "password"  onfocus="emptyElement('status')"  maxlength="16">

<div>Date:</div>
<select name="date" id="date" onFocus="emptyElement('status')">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option> </select>
<div>Month:</div>
<select name="month" id="month" onFocus="emptyElement('status')" >
<option value="Jan">Jan</option>
<option value="Feb">Feb</option>
<option value="Mar">Mar</option>
<option value="Apr">Apr</option>
<option value="May">May</option>
<option value="Jun">Jun</option>
<option value="Jul">Jul</option>
<option value="Aug">Aug</option>
<option value="Sep">Sep</option>
<option value="Oct">Oct</option>
<option value="Nov">Nov</option>
<option value="Dec">Dec</option>
</select>
<div>Year:</div>
<select name="year" id="year" onFocus="emptyElement('status')">
<option value="1970">1970</option><option value="1971">1971</option><option value="1972">1972</option><option value="1973">1973</option><option value="1974">1974</option><option value="1975">1975</option><option value="1976">1976</option><option value="1977">1977</option><option value="1978">1978</option><option value="1979">1980</option><option value="1981">1982</option><option value="1983">1984</option><option value="1985">1985</option><option value="1986">1986</option><option value="1987">1987</option><option value="1988">1988</option><option value="1989">1989</option><option value="1990">1990</option><option value="1991">1991</option><option value="1992">1992</option><option value="1993">1993</option><option value="1994">1994</option><option value="1995">1995</option><option value="1996">1996</option><option value="1997">1997</option><option value="1998">1998</option><option value="1999">1999</option><option value="2000">2000</option><option value="2001">2001</option><option value="2002">2002</option><option value="2003">2003</option><option value="2004">2004</option><option value="2005">2005</option><option value="2006">2006</option><option value="2007">2007</option><option value="2008">2008</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option></select>

<div>Gender:</div>
<select id="gender"   onfocus="emptyElement('status')">
<option value=""></option>
<option value="m">Male</option>
<option value="f">Female</option>
</select>
<div>Department:</div>
<input id="department" type= "text"  onfocus="emptyElement('status')"  maxlength="16">
<div>Course Level:</div>
<input id="courselevel" type= "text"  onfocus="emptyElement('status')"  maxlength="16">
<div>
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
<a href="#" onClick="return false" onMouseDown="openTerms()">
View the Terms of use
</a>
</div>
<div id="terms" style="display:none;">
    <h3>Charger+ Terms Of Use</h3>
    <p>1.</p>
    <p>2.</p>
    <p>3.</p>
</div>
<br/><br/>
<input id="signupbtn" type="submit"  name="Create Charger+ Account" value="Create Charger+ Account" onClick="signup()">
<span id="status"></span>
</form>
</div>
<div id="pageBottom">&copy;2015 Charger Team</div>

</body>
</html>