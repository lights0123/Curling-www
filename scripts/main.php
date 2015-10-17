<?php
session_start();
function page_create($title = "CSC Bonspiel App Webpage", $menu)
{
	echo <<<EOF
<html>
<head>
<title>$title</title>
<link rel='stylesheet' type='text/css' href='/css/main.css' />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>if (typeof jQuery === 'undefined') {
  document.write(unescape('%3Cscript%20src%3D%22/js/jquery-2.1.4.min.js%22%3E%3C/script%3E'));
  }
</script>
<!--[if lt IE 9]>
<META http-equiv="refresh" content="0;URL=unsupported">
<body>
<![endif]-->
<script src="/js/menu.js"></script>
</head>
<div id="container">
EOF;
	menu($menu);
}

function menu($menu)
{
	$home = "";
	$data = "";
	$contact = "";
	$about = "";
	$signin = "";
	$signup = "";
	switch ($menu) {
		case '/':
			$home = " class=\"active\"";
			break;
		case 'data':
			$data = " class=\"active\"";
			break;
		case 'contact':
			$contact = " class=\"active\"";
			break;
		case 'about':
			$about = " class=\"active\"";
			break;
		case 'login':
			$signin = " class=\"active\"";
			break;
		case 'signup':
			$signup = " class=\"active\"";
			break;
	}
	echo <<<EOF
<div id='menu'>
<ul>
   <li$home><a href='/'><span>Home</span></a></li>
   <li$data><a href='/data'><span>View Data</span></a></li>
   <li$contact><a href='/contact'><span>Contact</span></a></li>
   <li class='last'$about><a href='/about'><span>About</span></a></li>
EOF;
	if (isset($SESSION['loggedin'])) {
		echo <<<EOF
<li class='right'><a href='/signout'><span>Sign Out</span></a></li>
</ul>
</div>
EOF;
	} else {
		echo <<<EOF
<li class='right'$signin><a href='/login'><span>Login</span></a></li>
<li class='right'$signup><a href='/signup'><span>Sign Up</span></a></li>
</ul>
</div>
EOF;
	}

}

function footer()
{
	echo <<<EOF
<p>Footer</p>
EOF;

}