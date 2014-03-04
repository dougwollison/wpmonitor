<?php
// Load the configs and utilities
require('inc/config.php');
require('inc/utilities.php');

// Run setup check
require('inc/setup.php');

// Run authentication check
require('inc/auth.php');

// Get the current version of WordPress
$wp_core = wp_core_version();

// Get all sites
$sites = get_sites();

// Get all plugins
$plugins = get_plugins();

require('inc/admin.php');