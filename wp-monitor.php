<?php
/*
Plugin Name: WordPress Monitor
Description: Checks for the current version of WordPress and active plugins, reporting to a central private database.
Version: 1.0
Author: Doug Wollison
Author URI: http://dougw.me
License: GPL2
*/

define('WM_REPORT_URL', 'http://mydomain.com/wpmonitor/report.php');
define('WM_CHECK_INTERVAL', 60*60*24);

add_action('init', function(){
	global $wpdb, $wp_version, $blog_id;

	// Only attempt a check one hasn't been run in the desired interval
	if(time() - get_option('wp_monitor_last_check', time()) > WM_CHECK_INTERVAL) return;
	update_option('wp_monitor_last_check', time());

	// Get all active plugins
	$active_plugins = get_option('active_plugins');
	if(is_multisite()){
		// Also get all sitewide plugins where applicable
		$active_plugins = array_merge($active_plugins, array_keys(get_site_option( 'active_sitewide_plugins')));
	}

	$plugins = array();
	foreach($active_plugins as $plugin){
		// Get the slug of the plugin (file or folder name)
		$name = $plugin == basename($plugin) ? pathinfo($plugin, PATHINFO_FILENAME) : dirname($plugin);

		// Get the version of the installed plugin
		$pdata = get_file_data(WP_PLUGIN_DIR.'/'.$plugin, array('Version' => 'Version', 'Plugin URI' => 'Plugin URI'), 'plugin');

		// Skip if no version found
		if(!$pdata['Version']) continue;

		// Add to the list
		$plugins[$name] = $pdata['Version'];
	}

	// Determin the site's URL
	$site_url = site_url();

	// Strip any protocol (prevents duplicate entries)
	$site_url = preg_replace('#^https?://#', '', $site_url);

	// If this is a network, using domain mapping, we need to get the primary domain
	// (prevents duplicate entries)
	if(is_multisite() && defined('SUNRISE_LOADED')){
		$table = $wpdb->base_prefix . 'domain_mapping';
		$domain = $wpdb->get_var($wpdb->prepare("SELECT domain FROM $table WHERE blog_id = %d", $blog_id));

		if($domain){
			$site_url = $domain;
		}
	}

	$data = array(
		'url' => $site_url,
		'name' => get_bloginfo('name'),
		'version' => $wp_version,
		'plugins' => $plugins
	);

	$json = json_encode($data);

	// Hash the data for comparison
	$hash = md5($json);

	if(get_option('wp_monitor_last_update') == $hash) return;
	update_option('wp_monitor_last_update', $hash);

	// Send the data to the report endpoint
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, WM_REPORT_URL);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($json))
	);

	curl_exec($ch);
});