<?php
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
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				header('Content-type: ' . finfo_file($finfo, $page));
				finfo_close($finfo);
				include($page);
			} else {
				if(file_exists($page.'/index.php')){
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					header('Content-type: ' . finfo_file($finfo, $page.'/index.php'));
					finfo_close($finfo);
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