<?php
// Report all errors accept E_NOTICE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

define('DB_NAME', 'web-intro');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_HOST', '127.0.0.1');

define('JWT_SECRET_KEY', '4e21a261a42ed84f6317d51c31438d9643e2f81eb8b647feabc2b364a25d2349');
