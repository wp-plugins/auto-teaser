<?php
	/**
	 * Installs the default values if plugin has been activated for the first time.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	function auto_teaser_install() {
		if(get_option("auto_teaser_installed") != "") {
			return true;
		}
		/** Use update_option() to create the default options  */
		foreach(auto_teaser_get_default_settings() as $setting => $value) {
			update_option($setting, $value);
		}
		/** Set option to remember if Auto Teaser has already been installed */
		update_option("auto_teaser_installed", "true");

		return true;
	}

	register_activation_hook(__FILE__, "auto_teaser_install");

	/**
	 * Initializes the settings page.
	 *
	 * @since 0.1
	 */
	function auto_teaser_init_settings_page() {
		$settings_page = add_options_page(__("Auto Teaser settings", "auto_teaser"), __("Auto Teaser", "auto_teaser"), "edit_posts", "auto-teaser", "auto_teaser_settings_page");
		/** Load scripts and styles if user is on settings page */
		add_action("admin_head-" . $settings_page, "auto_teaser_admin_scripts_and_styles");
	}

	add_action("admin_menu", "auto_teaser_init_settings_page");

	/**
	 * Loads styles and scripts used in the admin interface.
	 *
	 * @since 0.1
	 */
	function auto_teaser_admin_scripts_and_styles() {
		wp_enqueue_style("auto-teaser-admin", auto_teaser_get_plugin_url() . "styles/admin.css", array(), auto_teaser_get_plugin_version());
		wp_enqueue_style("auto-teaser-font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css", array(), "4.3.0");

		wp_enqueue_script("auto-teaser-admin", auto_teaser_get_plugin_url() . "scripts/admin.js", array(), auto_teaser_get_plugin_version());
	}

	/**
	 * Loads the plugin's translation files.
	 *
	 * @since 0.1
	 */
	function auto_teaser_load_localizations() {
		load_plugin_textdomain("auto_teaser", false, dirname(plugin_basename(__FILE__)) . "/../languages");
	}

	add_action("init", "auto_teaser_load_localizations");

	/**
	 * Replaces excerpts with the generated teaser.
	 *
	 * @internal param $post_id
	 *
	 * @since    0.1
	 *
	 * @return bool|string
	 */
	function auto_teaser_replace_excerpt() {
		global $post;

		return auto_teaser_get_auto_teaser($post->ID);
	}

	/** Only apply if activated */
	if(auto_teaser_get_setting("replace_excerpt") == "on") {
		add_filter("get_the_excerpt", "auto_teaser_replace_excerpt");
	}