<?php

define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);

$split = explode('/', DOCUMENT_ROOT);
unset($split[count($split) - 1]);
define("SETTINGS_ROOT", implode('/', $split));

define("USES_HTTPS", (
		!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| $_SERVER['SERVER_PORT'] == 443
);

define("DOMAIN_NAME", $_SERVER['SERVER_NAME']);