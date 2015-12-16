<?php
include_once 'contentGlobals.php';
addHandler('content',function($opage,$rawpage) {
	$opage = substr($opage, 7);
	$opage = trim($opage, '/');
	if ($opage == '') {
		$opage = "index";
	}
	$page = realpath($opage);
	if ($page) {
		if (startsWith($page, DOCUMENT_ROOT . "/content")) {
			include($page);
			exit;
		}
	} else {
		$page = realpath($opage . ".php");
		if ($page) {
			if (startsWith($page, DOCUMENT_ROOT . "/content")) {
				include($page);
				exit;
			}
		}
	}
	chdir("../customcontent");
	getPage($opage, false, true);
	getPage($opage . ".php", false, true);
});