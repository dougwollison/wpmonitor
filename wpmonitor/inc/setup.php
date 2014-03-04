<?php
require('inc/kissMySQL.php');

// Connect to the database
$wmdb = new kissMySQL(DB_USER, DB_PASS, DB_NAME);

if(!$wmdb->ready){
	die('Unable to connect to database');
}

// Create the users table
$wmdb->query("CREATE TABLE `users` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(20) NOT NULL DEFAULT '',
	`password` char(64) NOT NULL DEFAULT '',
	`email` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`)
)");

// Create the sites table
$wmdb->query("CREATE TABLE `sites` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`url` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`version` varchar(9) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `url` (`url`)
)");

// Create the plugins table
$wmdb->query("CREATE TABLE `plugins` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`site` bigint(20) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`version` varchar(12) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `site_plugin_version` (`site`,`name`,`version`)
);");