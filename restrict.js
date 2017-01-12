function restrict(elem)
{
var ta =_(elem);
var rx = new RegExp;
if(elem =="email")
{
rx=/['  "]/gi;
}
else if(elem == "username"){
rx = /[^a-z0-9]/gi;
}
ta.value = ta.value.replace(rx,"");
}
