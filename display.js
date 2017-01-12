function _(x)
{
	return document.getElementById(x);
}

function editProfilepic(x)
{
	var x = _(x);
	if(x.style.display == 'block')
	{
		x.style.display = 'none';
	}
	else
	{
		x.style.display = 'block';
	}
}