<?php
/**
 * The authentication script.
 * You can replace this with your preferred system.
 * Simply have it exit to the login form should it
 * not authenticate properly.
 */

session_start();

if(isset($_GET['logout'])){
	// Destroy the auth token
	unset($_SESSION['auth']);
	
	// Redirect back to referrer if possible
	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']){
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
}

$authenticated = false;

// First, check if an auth session is present
if(isset($_SESSION['auth'])){
	// Validate auth session, show login form if failed
	if(sha1(AUTH_USER.AUTH_PASS) == $_SESSION['auth']){
		$authenticated = true;
	}
}
// Alternately, check if credentials were posted
elseif(isset($_POST['username']) && isset($_POST['password'])){
	// Validate credentials, show login form if failed
	if(AUTH_USER == $_POST['username'] && AUTH_PASS == $_POST['password']){
		$authenticated = true;
		
		// Set the auth session
		$_SESSION['auth'] = sha1(AUTH_USER.AUTH_PASS);
		
		// Redirect back to referrer if possible
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']){
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit;
		}
	}
}

// If not authenticated, show login form and exit
if(!$authenticated){
	require('login.php');
	exit;
}