<?php
date_default_timezone_set('Asia/Manila');
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
 
$url = $protocol . $_SERVER['HTTP_HOST'];

define('BASE_URL', $url);

define('LOGO_TEXT', 'Company');

define('SERVER_DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);