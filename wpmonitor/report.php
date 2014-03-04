<?php
// Load the configs
require('inc/config.php');

// Run setup check
require('inc/setup.php');

$input = file_get_contents('php://input');

if($input){
	$data = json_decode($input);
	file_put_contents('logs/'.time().'-'.$_SERVER['REMOTE_ADDR'], $data);

	if(!$data || !is_object($data)){
		header('HTTP/1.1 400 Bad Request');
		exit;
	}

	header('HTTP/1.1 200 OK');

	if(!$sid = $wmdb->get_var("SELECT id FROM sites WHERE url = %s", $data->url)){
		$wmdb->replace('sites', array(
			'name' => $data->name,
			'url' => $data->url,
			'version' => $data->version
		));
		$sid = $wmdb->insert_id;
	}else{
		$wmdb->update('sites', array(
			'name' => $data->name,
			'url' => $data->url,
			'version' => $data->version
		), array('id' => $sid));
	}

	$wmdb->delete('plugins', array('site' => $sid));
	foreach($data->plugins as $plugin => $version){
		$wmdb->insert('plugins', array(
			'site' => $sid,
			'name' => $plugin,
			'version' => $version
		));
	}

	die($sid);
}else{
	header('HTTP/1.1 400 Bad Request');
	exit;
}