<?php
	/**
	 * Plugin Name:  Auto Teaser
	 * Plugin URI:   http://www.koljanolte.com/wordpress/plugins/auto-teaser/
	 * Description:  Automatically generates a teaser for posts, pages and custom post types.
	 * Version:      1.1.0
	 * Author:       Kolja Nolte
	 * Author URI:   http://www.koljanolte.com
	 * License:      GPLv2 or later
	 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
	 */

	/**
	 * Stop script when the file is called directly.
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Include all PHP files in the includes directory.
	 */
	$include_files = glob(dirname(__FILE__) . "/includes/*.php");
	foreach($include_files as $include_file) {
		include($include_file);
	}