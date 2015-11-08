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

function menu($activeItem)
{
	$menu = array(
		'/' => array('Home', null),
		'/data' => array('View Data', null),
		'/contact' => array('Contact', null),
		'/about' => array('About', null)
	);
	if (isset($SESSION['loggedin'])) {
		$menu['/signout'] = array('Sign Out', 'right');
	} else {
		$menu['/login'] = array('Login', 'right');
		$menu['/signup'] = array('Sign Up', 'right');
	}

	echo <<<EOF
<div id='menu'>
<ul>
EOF;
	foreach ($menu as $link => $data) {
		$active = ($activeItem == "/" && $link == "/") || (trim($link, '/') == $activeItem);
		$classes="";
		if($data[1]!=null){
			$classes=$data[1];
		}
		if($active){
			if($data[1]){
				$classes.=' ';
			}
			$classes .= "active";
		}
		if($classes!=""){
			$classes=' class="'.$classes.'"';
		}
		echo <<<EOF
<li$classes><a href='$link'><span>$data[0]</span></a></li>
EOF;
	}
	echo <<<EOF
</ul>
</div>
EOF;
}

function footer()
{
	echo <<<EOF
<p>Copyright © 2015 Ben Schattinger. Some rights reserved by the <a href="/license">license</a>.</p>
<p><a href="/tos">Terms of Service</a> | <a href="/privacy">Privacy Policy</a></p>
EOF;

}

function startsWith($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}
