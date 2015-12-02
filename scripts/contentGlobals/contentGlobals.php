<?php
include_once '../main.php';
if(!function_exists('getPage')) {
	function getPage($page, $formatted = true, $redirect = false)
	{
		$page = realpath($page);
		if (!$page) return false;
		if ($formatted) {
			$filename = end(explode('/', $page));
			if (startsWith($page, DOCUMENT_ROOT . "/content")) {
				if (!is_dir($page)) {
					createPage($page, $filename == 'index.php' ? '/' : explode('.', $filename)[0]);
					exit();
				}
			}
			return false;
		} else {
			if (startsWith($page, DOCUMENT_ROOT . "/customcontent")) {
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
						if (substr($_GET['rawpage'], strlen($_GET['rawpage']) - 1) != '/') {
							location($_GET['rawpage'] . '/');
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
			}
			return false;
		}
	}
}
if(!function_exists('getMimeType')) {
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
}
if(!function_exists('addHandler')) {
	function addHandler($case, $function)
	{
		global $functions;
		$functions[] = array($case, $function);
	}
}