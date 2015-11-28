<?php

define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);

$split = explode('/', DOCUMENT_ROOT);
unset($split[count($split) - 1]);
define("SETTINGS_ROOT", implode('/', $split));