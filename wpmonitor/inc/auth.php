<?php
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    header('WWW-Authenticate: Basic realm="Premise: WP Monitor"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authorization Required';
    exit;
} else {
	list($user, $pass) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
	if($user != AUTH_USER || $pass != AUTH_PASS){
		header('WWW-Authenticate: Basic realm="Premise: WP Monitor"');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Access Denied';
	    exit;
	}
}