<?php
require('inc/config.php');
require('inc/auth.php');
require('inc/utilities.php');
require('inc/kissMySQL.php');

// Get the current version of WordPress
$wp_core = wp_core_version();

// Connect to the database
$wmdb = new kissMySQL(DB_USER, DB_PASS, DB_NAME);

// Get all sites
$sites = get_sites();

// Get all plugins
$plugins = get_plugins();
?><!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>WordPress Monitor</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<style>
		.component{
			width: 80%;
		}
		.installed,
		.available{
			width: 10%;
			text-align: center;
		}
	</style>
</head>
	<div class="container">
		<h1>WP Monitor</h1>
		<?php foreach($sites as $site):?>
		<hr />
		<h2><?php echo $site->name?>
			<small><a href="http://<?php echo $site->url?>/wp-admin/" target="_blank"><?php echo $site->url?></a></small></h2>
		<table class="table">
			<thead>
				<tr>
					<th class="component">Component</th>
					<th class="installed">Installed</th>
					<th class="available">Available</th>
				</tr>
			</thead>
			<tbody>
				<tr class="<?php echo $site->version == $wp_core ? 'success' : 'danger'?>">
					<td class="component">Core</td>
					<td class="installed"><?php echo $site->version?></td>
					<td class="available"><?php echo $wp_core?></td>
				</tr>
				<?php foreach($site->plugins as $plugin): $plugindata = $plugins[$plugin->name];
				$class = '';
				if($plugindata->version == 'n/a'){
					$class = 'active';
				}elseif($plugin->version != $plugindata->version){
					$class = 'warning';
				}
				?>
				<tr class="<?php echo $class?>">
					<td class="component">
						<?php if($plugindata->slug):?>
							<a href="http://wordpress.org/plugins/<?php echo $plugindata->slug?>/changelog/" target="_blank"><?php echo $plugindata->name?></a>
						<?php else:?>
							<?php echo $plugindata->name?>
						<?php endif;?>
					</td>
					<td class="installed"><?php echo $plugin->version?></td>
					<td class="available"><?php echo $plugindata->version?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php endforeach;?>
	</div>
<body>
</body>
</html>