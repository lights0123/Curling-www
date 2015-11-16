<?php
error_reporting(E_ERROR);
include('template.php');
try {
	if ($_GET['page'] == '') {
		$_GET['page'] = "index";
	}
	chdir(__DIR__);
	chdir("../content");
	$_GET['page'] = trim($_GET['page'], '/');
	if (startsWith($_GET['page'], 'content')) {
		$backup = $_GET;
		$_GET['page'] = substr($_GET['page'], 7);
		$_GET['page'] = trim($_GET['page'], '/');
		if ($_GET['page'] == '') {
			$_GET['page'] = "index";
		}
		$page = realpath($_GET['page']);
		if ($page) {
			$filename = end(explode('/', $page));
			if (startsWith($page, "/var/www/html/content")) {
				include($page);
				exit();
			}
		} else {
			$page = realpath($_GET['page'] . ".php");
			if ($page) {
				$filename = end(explode('/', $page));
				if (startsWith($page, "/var/www/html/content")) {
					include($page);
					exit();
				}
			}
		}
		$_GET = $backup;
	}
	if (!getPage($_GET['page'])) {
		if (!getPage($_GET['page'] . ".php")) {
			chdir("../customcontent");
			if (!getPage($_GET['page'], false)) {
				if (!getPage($_GET['page'] . ".php", false)) {
					http_response_code(404);
					createPage('/var/www/html/content/error.php', 'error');
				}
			}
		}
	}
} catch (Exception $e) {
	http_response_code(404);
	createPage('/var/www/html/content/error.php', 'error');
}


function getPage($page, $formatted = true)
{
	$page = realpath($page);
	if ($formatted) {
		if ($page) {
			$filename = end(explode('/', $page));
			if (startsWith($page, "/var/www/html/content")) {
				if (!is_dir($page)) {
					createPage($page, $filename == 'index.php' ? '/' : explode('.', $filename)[0]);
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		if ($page && startsWith($page, "/var/www/html/customcontent")) {
			if(!is_dir($page)) {
				header('Content-type: ' . getMimeType($page));
				chdir(dirname($page));
				include($page);
			} else {
				if(file_exists($page.'/index.php')){
					header('Content-type: ' . getMimeType($page.'/index.php'));
					chdir(dirname($page.'/index.php'));
					include($page.'/index.php');
				}else{
					//TODO: add file viewer
					return false;
				}
			}
		} else {
			return false;
		}
	}
	return true;
}
function getMimeType($page){
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $page);
	switch(end(explode('.',$page))){
		case 'appcache':
			$mime='text/cache-manifest';
			break;
		case 'php':
			$mime='text/html';
			break;
		default:
			break;
	}
	finfo_close($finfo);
	return $mime;
}