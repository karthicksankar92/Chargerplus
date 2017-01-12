<?php
// Registeration using ajax call
if(isset($_POST["u"]))
{
//Connect to the database
include_once("config.php");
//collect all  the details
$Ano=$_POST['Ano'];
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$u=preg_replace('#[^a-z0-9]#i','',$_POST['u']);
$e=@mysql_real_escape_string($_POST['e']);
$p=$_POST['p'];
$date=$_POST['date'];
$month=$_POST['month'];
$year=$_POST['year'];
$g=preg_replace('#[^a-z]#','',$_POST['g']);
$d=$_POST['d'];
$c=$_POST['c'];
$que=$_POST['que'];
$ans=$_POST['ans'];
//Get user ip address
$ip =preg_replace('#[^0-9.]#','',getenv('REMOTE_ADDR'));

//Duplicate data checks for Ano, username and Email
/*$sql="select id from registeration where Ano='$Ano' LIMIT 1";
$query =mysql_query($sql);
$a_check=mysql_num_rows($query);*/

//-------------------------------------------------------------
$sql="select id from register where username='$u' LIMIT 1";
$query =mysql_query($sql);
$user_check=@mysql_num_rows($query);
//-------------------------------------------------------------
$sql ="select id from register where email='$e' LIMIT 1";
$query =mysql_query($sql);
$email_check=@mysql_num_rows($query); 

//Error Handling
if($Ano==""||$fname==""||$lname==""||$u==""|| $e=="" || $p=="" || $g==""||$date==""||$month==""||$year==""||$d==""||$c=="" ||$que=="" || $ans=="")
{
echo "Please fill in all the details to proceed further";
exit();
}
/*else if($a_check>0)
{
echo "The A number you entered is already in use in the system";
exit();
}*/
else if($user_check>0)
{
echo "Username already taken :(";
exit();
}
else if($email_check >0)
{
echo "Email already in use :(";
exit();
}/*
else if(strlen($Ano)< 0 || strlen($Ano)>9)
{
echo "A number must be 9 characters";
exit();
}
else if($Ano[0]!='a' && $Ano[0]!='A')
{
echo 'A number must  begin with a letter <b>A</b>';
exit();
}*/
else if(strlen($u)< 3 || strlen($u)>16)
{
echo "Username must be between 3 and 16 characters";
exit();
}
else if(is_numeric($u[0]))
{
echo 'Username cannot begin with a number';
exit();
}
else
{
//Error handling-End
//Begin inserting the data

//Add user
$sql="insert into register (Ano,firstname,lastname,username,email,password,gender,date,month,year,department,courselevel,ip,signup,lastlogin,notescheck)
       values ('$Ano','$fname','$lname','$u','$e','$p','$g','$date','$month','$year','$d','$c','$ip',now(),now(),now()) ";
$query=mysql_query($sql);
 $sql ="select id from register where  username='$u' AND email='$e' AND password='$p' LIMIT 1";
$query=mysql_query($sql);
$row=@mysql_fetch_array($query);
$id=$row['id'];
//Inserting row in the user Security table
$sql ="insert into usersecurity(id,username,background,question,answer) values ('$id','$u','orginal','$que','$ans')";
$query=mysql_query($sql);

//create directory (folder) to hold user's files
if(!file_exists("user/$u"))
{
mkdir("user/$u",0755);
}
//Email the user their activation link
$to ="$e";
$from="king.warrior50@gmail.com" ;
$subject='Charger+ Account Activation';
$message ='<!DOCTYPE html><html><head><meta charset="UTF-8"><title> Charger + Message</title></head><body font-family:Tahoma,Geneva,sans-serif;">
<div style="padding :10px;background:#333;font-size:24px;color:#ccc;"><a href="index.php"><img src="images/logo.png" width ="36" height="30" alt="Charger+ "><style="border:none;float:left;"></a> Charger+ Account Activation </div><div style="padding :24px;font-size:17px;">Hello
'.$u.',<br/><br />click the link below to activate your account when ready:<br/><br /><a href="activation.php?id='.$id.'&u='.$u.'&e='.$e.'&p='.$p.'">Click here to activate your account now</a><br/><br/>Login after successful activation using your:<br/>"Email Address:<b>'.$e.'</b><br/>" password: <b>'.$p.'</b></div></body></html>';
$headers="From:$from\n";
$headers="MIME-Version:1.0\n";
$headers="Content-type:text/html;charset=iso-8859-1\n";
if(mail($to,$subject,$message,$headers))
{
echo "Mail sent ";
echo "onsuccess";
}
else
{
echo "sending failed";
}

exit();
}
exit();
}

?>