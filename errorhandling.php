<?php
$message ="No message";
$msg="";
$msg=preg_replace('#[^a-z 0-9.:_()]#i','',$_GET['msg']);
if($msg =="activation failure")
{
$message='<h2> Activation Error</h2> Sorry there seems to have some problem.we solve
ourselves of this issue and we will contact you via email when we resolve this issue';
}
else if($msg=="activation_success")
{
$message='<h2>Activation success</h2>Your account is now activated .<a href="login.php">Click here to log in</a>';
}
else
{
$message =$msg;
}
?>
<div><?php echo $message;?></div>
