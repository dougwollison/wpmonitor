<?php
define('WM_ROOT', dirname(__DIR__));
define('WM_CACHE_EXPIRY', 60*60*24);

define('AUTH_USER', 'username');
define('AUTH_PASS', 'password');

define('DB_USER', 'wpmonitor');
define('DB_PASS', 'password');
define('DB_NAME', 'wpmonitor');

define('API_CORE_VERSION_URL', 'http://api.wordpress.org/core/version-check/1.7/');
define('API_PLUGIN_INFO_URL', 'http://api.wordpress.org/plugins/info/1.0/');