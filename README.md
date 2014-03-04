WP Monitor
==========

A simple way to keep track of your numerous WordPress installs.

WP Monitor comes in 2 parts: a plugin you activate on every install,
and a minisite that let's you view the version stats of all your
installs in one place. It will only list this information; you must
log into each website to take action, for security reasons.

How to Install
--------------

Place the `wpmonitor` folder contents where you'd like the monitor
to be located (e.g. mydomain.com/wpmonitor). Fill out the config.php
args with your database credentials, and it'll setup the database
tables as needed.

Copy `wp-monitor.php` to your WordPress install's plugin folder.
For easy editing, try placing it in one place on your server, and
then simply hardlink it in each plugin folder. Just configure the
report url to where your monitor is installed, and you're all set.

How it Works
------------

The plugin hooks into the `init` action, checking every 24 hours or
so (a configurable value) for changes to the WordPress core or plugin
versions. If a change is detected, it sends this information to your
central monitor's report script.

The report scripts logs the site in the database, along with all listed
plugins and their versions. When you visit the monitor, it will fetch
the current version of WordPress, and that of each plugin (this data is
cached for a configurable amount of time).

Your sites are listed in alphabetical order, with links to each site's
admin section. Any plugins that are found in WordPress.org's repository
will be linked to their changelog. The core will be highlighted green or
red, depending on if it's up to date or not. Out of date plugins will
appear yellow, and custom/private plugins will be highlighted in gray.