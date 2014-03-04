<?php
function wp_core_version(){
	// Get the JSON decoded data from the version check url
	$wp = json_decode(file_get_contents(API_CORE_VERSION_URL));

	// Return the first offer's version
	return $wp->offers[0]->version;
}

function get_sites(){
	global $wmdb;

	// Get all sites in the database
	$sites = $wmdb->get_results("SELECT * FROM sites ORDER BY name");

	// Run through each site and get their plugins
	foreach($sites as $site){
		$site->plugins = $wmdb->get_results("SELECT * FROM plugins WHERE site = $site->id AND name != 'wp-monitor' ORDER BY name");
	}

	return $sites;
}

function get_plugins(){
	global $wmdb;

	// Get the names of all plugins listed in the database (no duplicates)
	$found_plugins = $wmdb->get_col("SELECT DISTINCT(name) FROM plugins WHERE name != 'wp-monitor' ORDER BY name");

	$plugins = array();
	// Run through each plugin, and attempt to get it's repo data
	foreach($found_plugins as $plugin){
		// Calculate the name of the cache file
		$cache = WM_ROOT.'/cache/'.md5($plugin);

		// If no (recent) cache exists,
		if(!file_exists($cache) || time() - filemtime($cache) > WM_CACHE_EXPIRY){
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => API_PLUGIN_INFO_URL,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => http_build_query(array(
					'action' => 'plugin_information',
					'request' => serialize((object) array(
						'slug' => $plugin,
						'fields' => array(
							'added' => false,
							'compatibility' => false,
							'donate_link' => false,
							'downloaded' => false,
							'homepage' => false,
							'last_updated' => false,
							'rating' => false,
							'tags' => false,
							'tested' => false,
							'sections' => false,
						),
					)),
				)),
			));
			$result = curl_exec($ch);

			// Store the result in the cache
			file_put_contents($cache, $result);
		}else{
			// Load the cached data
			$result = file_get_contents($cache);
		}

		// Unserialize the data
		if($result && $data = unserialize($result)){
			$plugins[$plugin] = $data;
		}else{
			// Create dummy data if no match was found
			$plugins[$plugin] = (object) array(
				'name' => $plugin,
				'version' => 'n/a',
				'slug' => '',
			);
		}
	}

	return $plugins;
}