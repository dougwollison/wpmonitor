<?php
require('inc/config.php');
require('inc/kissMySQL.php');

$wmdb = new kissMySQL(DB_USER, DB_PASS, DB_NAME);

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
		$wmdb->replace('plugins', array(
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