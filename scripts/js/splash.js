function return_login(e)
{
	if(e.keyCode == 13)
	{
		document.getElementById("loginarrow").click();
	}
}

function print_login_error()
{
	var login_errors = new Array('Oh bother... looks like your credentials are incorrect!', 
								 'Whoops... wrong credentials... try again?', 
								 'Are you sure you remember your credentials? Because they\'re incorrect.', 
								 'Oh man... can\'t log you in. Try again?', 
								 'Incorrect username or password... we\'d tell you which one, but that would just make it easier for hackers to hack us.', 
								 'Incorrect credentials (please don\'t hate us!)');
				
	var greeting = login_errors[Math.floor(Math.random()*(login_errors.length-1))+1];

	document.getElementById('cntlgnm').innerHTML = greeting;
}
