<?php
error_reporting(E_ERROR);
include('template.php');
try {
	$_GET['rawpage']=$_GET['page'];
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
			if (startsWith($page, DOCUMENT_ROOT."/content")) {
				include($page);
				exit();
			}
		} else {
			$page = realpath($_GET['page'] . ".php");
			if ($page) {
				$filename = end(explode('/', $page));
				if (startsWith($page, DOCUMENT_ROOT."/content")) {
					include($page);
					exit();
				}
			}
		}
		chdir("../customcontent");
		if (!getPage($_GET['page'], false, true)) {
			getPage($_GET['page'] . ".php", false, true);
		}
		$_GET = $backup;
	}
	if (!getPage($_GET['page'])) {
		if (!getPage($_GET['page'] . ".php")) {
			chdir("../customcontent");
			if (!getPage($_GET['page'], false)) {
				if (!getPage($_GET['page'] . ".php", false)) {
					http_response_code(404);
					createPage(DOCUMENT_ROOT.'/content/error.php', 'error');
				}
			}
		}
	}
} catch (Exception $e) {
	http_response_code(404);
	createPage(DOCUMENT_ROOT.'/content/error.php', 'error');
}


function getPage($page, $formatted = true, $redirect = false)
{
	$page = realpath($page);
	if ($formatted) {
		if ($page) {
			$filename = end(explode('/', $page));
			if (startsWith($page, DOCUMENT_ROOT."/content")) {
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
		if ($page && startsWith($page, DOCUMENT_ROOT."/customcontent")) {
			if (!is_dir($page)) {
				if ($redirect) {
					echo <<<EOL
{$_GET['page']}
EOL;
					exit();
				}
				header('Content-type: ' . getMimeType($page));
				chdir(dirname($page));
				include($page);
				exit();
			} else {
				if (file_exists($page . '/index.php')) {
					if ($redirect) {
						echo <<<EOL
{$_GET['page']}
EOL;
						exit();
					}
					if(substr($_GET['rawpage'], strlen($_GET['rawpage']) - 1)!='/'){
						location($_GET['rawpage'].'/');
					}
					header('Content-type: ' . getMimeType($page . '/index.php'));
					chdir($page);
					include($page . '/index.php');
					exit();
				} else {
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

function getMimeType($page)
{
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $page);
	switch (end(explode('.', $page))) {
		case 'appcache':
			$mime = 'text/cache-manifest';
			break;
		case 'php':
			$mime = 'text/html';
			break;
		default:
			break;
	}
	finfo_close($finfo);
	return $mime;
}

function location($loc)
{
	header("Location: " . $loc);
	exit();
}