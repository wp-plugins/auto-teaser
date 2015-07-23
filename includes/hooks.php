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
		$root = auto_teaser_get_plugin_url();
		wp_enqueue_style("auto-teaser-admin", "$root/styles/admin.css", array(), auto_teaser_get_plugin_version());
		wp_enqueue_style("auto-teaser-font-awesome", "$root/fonts/css/font-awesome.min.css", array(), "4.3.0");

		wp_enqueue_script("auto-teaser-admin", "$root/scripts/admin.js", array(), auto_teaser_get_plugin_version());
	}

	/**
	 * Loads the plugin's translation files.
	 *
	 * @since 0.1
	 */
	function auto_teaser_load_translations() {
		$translation_path = plugin_dir_path(__FILE__) . "../languages/" . get_site_option("WPLANG") . ".mo";
		load_textdomain("auto_teaser", $translation_path);
	}

	add_action("init", "auto_teaser_load_translations");

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