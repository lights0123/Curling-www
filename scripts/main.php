<?php
include('globals.php');
session_start();
function page_create($title = "CSC Bonspiel App Webpage", $menu)
{
	echo <<<EOF
<html>
<head>
<title>$title</title>
<link rel='stylesheet' type='text/css' href='/css/main.css' />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
if (typeof jQuery === 'undefined') {
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

function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

// $amount is the amount to read from either end, for example, with $amount being 5:
// This is a test
// ↑↑↑↑↑
// Now, $amount being -5:
// This is another test
//                ↑↑↑↑↑
function getChars($string, $amount){
	if($amount < 0){
		return substr($string, $amount);
	}elseif($amount > 0){
		return substr($string, 0, $amount);
	}else{
		return '';
	}
}

function getSelf(){
	$file=explode('/',debug_backtrace()[0]['file']);
	$content=array_search('content',$file);
	if($content==false){
		$content=array_search('customcontent',$file);
	}
	$index=$content;
	for($i=0;$i<=$index;$i++){
		unset($file[$i]);
	}
	$file = implode('/',$file);
	$file = endsWith($file,'.php') ? substr($file, 0, -4) : $file;
	return $file;
}