<?php
	/**
	 * Returns the absolute path to the plugin directory.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	function auto_teaser_get_plugin_path() {
		$path = str_replace("\\", "/", plugin_dir_path(dirname(__FILE__)));

		return $path;
	}

	/**
	 * Displays the absolute path to the plugin directory.
	 *
	 * @since 0.1
	 */
	function auto_teaser_the_plugin_path() {
		echo auto_teaser_get_plugin_path();
	}

	/**
	 * Returns the URL to the plugin directory.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	function auto_teaser_get_plugin_url() {
		$url = plugin_dir_url(dirname(__FILE__));

		return $url;
	}

	/**
	 * Displays the URL to the plugin directory.
	 *
	 * @since 0.1
	 */
	function auto_teaser_the_plugin_url() {
		echo auto_teaser_get_plugin_url();
	}

	/**
	 * Returns the current plugin version parsed from auto-teaser.php.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	function auto_teaser_get_plugin_version() {
		$auto_teaser_php_path = auto_teaser_get_plugin_path() . "auto-teaser.php";
		$auto_teaser_php      = esc_attr(file_get_contents($auto_teaser_php_path));
		preg_match("'Version:(.*)\n'", $auto_teaser_php, $version);

		return $version[1];
	}

	/**
	 * Displays the current plugin version parsed from auto-teaser.php.
	 *
	 * @since 0.1
	 */
	function auto_teaser_the_plugin_version() {
		echo auto_teaser_get_plugin_version();
	}

	/**
	 * Returns Auto Teaser's settings and their default values.
	 *
	 * @since 0.1
	 *
	 * @return array
	 *
	 */
	function auto_teaser_get_default_settings() {
		$default_settings = array(
			"teaser_format"                => "",
			"teaser_split_style"           => "words",
			"teaser_split_value"           => 3,
			"teaser_custom_split_value"    => "",
			"teaser_custom_split_position" => "before",
			"replace_excerpt"              => "off",
			"allow_html"                   => "on",
			"allow_html_tags"              => "",
			"exclude_ids"                  => array(),
			"backup_teaser"                => "",
			"backup_teaser_custom_text"    => ""
		);

		return $default_settings;
	}

	/**
	 * Returns Auto Teaser's settings. If a setting has not yet been set,
	 * the default setting value will be returned.
	 *
	 * @since 0.1
	 *
	 * @return array
	 *
	 */
	function auto_teaser_get_settings() {
		$settings         = array();
		$default_settings = auto_teaser_get_default_settings();
		foreach($default_settings as $default_setting => $default_value) {
			$value = get_option("auto_teaser_" . $default_setting);
			if(!$value) {
				$value = $default_value;
			}
			if($default_setting == "exclude_ids") {
				$value = preg_replace("'[^1-9,]'", "", $value);
				if(!$value) {
					$value = array();
				}
				else {
					$value = explode(", ", $value);
				}
			}
			elseif($default_setting == "allow_html_tags") {
				if(!is_array($value)) {
					$value = array();
				}
			}
			$settings[$default_setting] = $value;
		}

		return (array)$settings;
	}

	/**
	 * Returns a single setting value.
	 *
	 * @param $setting
	 *
	 * @since 0.1
	 *
	 * @return bool|string
	 */
	function auto_teaser_get_setting($setting) {
		$settings = auto_teaser_get_settings();
		if(!isset($settings[$setting])) {
			return false;
		}

		return $settings[$setting];
	}

	/**
	 * Displays a single setting value.
	 *
	 * @param $setting
	 *
	 * @since 0.1
	 */
	function auto_teaser_the_setting($setting) {
		echo auto_teaser_get_setting($setting);
	}

	/**
	 * Checks/selects a HTML input if a certain value from the
	 * plugin's settings is set.
	 *
	 * @param        $setting_name
	 * @param        $setting_value
	 *
	 * @param string $mode
	 * @param string $relation
	 *
	 * @return string
	 * @since 0.1
	 */
	function auto_teaser_get_check_setting($setting_name, $setting_value, $mode = "checked", $relation = "==") {
		$output = " " . $mode . '="' . $mode . '"';
		if($relation == "==") {
			if(auto_teaser_get_setting($setting_name) != $setting_value) {
				$output = "";
			}
		}
		elseif($relation == "!=") {
			if(auto_teaser_get_setting($setting_name) == $setting_value) {
				$output = "";
			}
		}

		return $output;
	}

	/**
	 * Compares a setting with the current value and displays HTML attributes accordingly.
	 *
	 * @param        $setting_name
	 * @param        $setting_value
	 * @param string $mode
	 * @param string $relation
	 *
	 * @since 0.1
	 */
	function auto_teaser_check_setting($setting_name, $setting_value, $mode = "checked", $relation = "==") {
		echo auto_teaser_get_check_setting($setting_name, $setting_value, $mode, $relation);
	}

	/**
	 * Replaces all placeholders in a string with the real values.
	 *
	 * @param     $string
	 *
	 * @param int $post_id
	 *
	 * @internal param int $post_id
	 *
	 * @since    0.1
	 *
	 * @return mixed
	 */
	function auto_teaser_replace_placeholders($string, $post_id = 0) {
		/** Use default post ID if no post or 0 is given */
		if(!$post_id) {
			$post_id = (int)get_the_ID();
		}
		$post = get_post($post_id);
		if(!$post) {
			return false;
		}
		$string           = stripslashes($string);
		$categories       = wp_get_post_categories($post_id);
		$category_list    = "";
		$category_counter = 0;
		foreach($categories as $category) {
			$category_list .= get_cat_name($category);
			if(isset($categories[$category_counter + 1])) {
				$category_list .= ", ";
			}
			$category_counter++;
		}

		/** Replacements */
		$replacements = array(
			"%excerpt%"         => $post->post_excerpt,
			"%permalink%"       => get_permalink($post_id),
			"%author_id%"       => $post->post_author,
			"%author_name%"     => get_the_author_meta("display_name", $post->post_author),
			"%author_url%"      => get_author_posts_url($post->post_author),
			"%comments_number%" => $post->comment_count,
			"%categories%"      => $category_list,
			"%category_name%"   => get_cat_name($categories[0]),
			"%category_url%"    => get_category_link($categories[0])
		);
		foreach($replacements as $placeholder => $replacement) {
			if(strstr($string, $placeholder)) {
				$string = str_replace($placeholder, $replacement, $string);
			}
		}

		return $string;
	}

	/**
	 * Returns the generated teaser for a post.
	 *
	 * @param int   $post_id
	 * @param array $options
	 *
	 * @since 0.1
	 *
	 * @return bool|string
	 */
	function auto_teaser_get_auto_teaser($post_id = 0, array $options = array()) {
		/** Define default option values */
		$default_options = array(
			"use_teaser_format"         => true,
			"teaser_format"             => auto_teaser_get_setting("teaser_format"),
			"split_style"               => auto_teaser_get_setting("teaser_split_style"),
			"split_value"               => auto_teaser_get_setting("teaser_split_value"),
			"custom_split_value"        => auto_teaser_get_setting("teaser_custom_split_value"),
			"custom_split_position"     => auto_teaser_get_setting("teaser_custom_split_position"),
			"allow_html"                => auto_teaser_get_setting("allow_html"),
			"allow_html_tags"           => auto_teaser_get_setting("allow_html_tags"),
			"exclude_ids"               => auto_teaser_get_setting("exclude_ids"),
			"backup_teaser"             => auto_teaser_get_setting("backup_teaser"),
			"backup_teaser_custom_text" => auto_teaser_get_setting("backup_teaser_custom_text")
		);
		/** Use default value if setting not set */
		foreach($default_options as $option_name => $default_value) {
			if(isset($options[$option_name]) || empty($options[$option_name])) {
				$options[$option_name] = $default_value;
			}
		}
		if(!$post_id) {
			$post_id = (int)get_the_ID();
		}

		/** Apply filter if it exists */
		$options = apply_filters("auto_teaser_get_auto_teaser_options", $options, $post_id);

		/** Stop script if post is among the excluded post IDs */
		if(in_array($post_id, $options["exclude_ids"], false)) {
			return false;
		}

		/** Define basic values */
		$post          = get_post($post_id);
		$content       = $post->post_content;
		$teaser        = false;
		$teaser_failed = false;
		$backup_teaser = "";

		/** Define backup teaser */
		if($options["backup_teaser"] == "post_content") {
			$backup_teaser = $post->post_content;
		}
		elseif($options["backup_teaser"] == "post_excerpt") {
			$backup_teaser = $post->post_excerpt;
		}
		elseif($options["backup_teaser"] == "custom_text") {
			$backup_teaser = $options["backup_teaser_custom_text"];
		}

		/** Split paragraphs */
		if($options["split_style"] == "paragraphs") {
			if(strstr($content, "\n")) {
				$paragraphs       = preg_split("'\n'", $content);
				$glued_paragraphs = "";
				for($i = 0; $i <= $options["split_value"] - 1; $i++) {
					$glued_paragraphs .= $paragraphs[$i];
					if(!isset($paragraphs[$i][1])) {
						$options["split_value"]++;
					}
				}
				$teaser = $glued_paragraphs;
			}
			else {
				$teaser_failed = true;
			}
		}
		/** Split sentences */
		elseif($options["split_style"] == "sentences") {
			$sentences = preg_split("'(?<=[.?!;\r\n])\s+'", $content);
			for($i = 0; $i <= $options["split_value"] - 1; $i++) {
				if(isset($sentences[$i])) {
					$teaser .= $sentences[$i] . " ";
				}
				else {
					$teaser_failed = true;
				}
			}
		}
		/** Split words */
		elseif($options["split_style"] == "words") {
			$teaser = wp_trim_words($content, $options["split_value"], "");
		}
		/** Split characters */
		elseif($options["split_style"] == "characters") {
			$teaser = substr($content, 0, $options["split_value"]);
		}
		/** Split at custom value */
		elseif($options["split_style"] == "custom" && strstr($content, $options["custom_split_value"])) {
			$length = 0;
			/** Display the custom text after the split value if set */
			if($options["custom_split_position"] == "after") {
				$length = strlen($options["custom_split_value"]);
			}
			$teaser = substr($content, 0, strpos($content, $options["custom_split_value"]) + $length);
		}
		/** Use backup teaser */
		else {
			$teaser = $backup_teaser;
		}

		/** Display backup teaser if teaser generation has failed */
		if($teaser_failed) {
			$teaser = $backup_teaser;
		}

		/** Apply HTML settings */
		if($options["allow_html"] != "on") {
			$allowed_tags = "";
			/** Allow only set HTML tags */
			if($options["allow_html"] == "custom") {
				$html_tags = $options["allow_html_tags"];
				/** Glue HTML tags together */
				foreach((array)$html_tags as $html_tag) {
					$allowed_tags .= "<" . trim($html_tag) . ">";
				}
			}
			$teaser = strip_tags($teaser, $allowed_tags);
		}

		/** Apply teaser format if activated */
		if($options["use_teaser_format"]) {
			$replaced_format = auto_teaser_replace_placeholders($options["teaser_format"]);
			$teaser          = str_replace("%teaser%", $teaser, $replaced_format);
		}

		/** Apply filter if it exists */
		$teaser = apply_filters("auto_teaser_get_auto_teaser", $teaser, $post_id);

		/** Return our final teaser */

		return $teaser;
	}

	/**
	 * Displays the generated teaser for a post.
	 *
	 * @param int   $post_id
	 * @param array $options
	 *
	 * @since 0.1
	 */
	function auto_teaser_the_auto_teaser($post_id = 0, array $options = array()) {
		if(!$post_id) {
			$post_id = (int)get_the_ID();
		}
		$default_options = array(
			"container"       => "p",
			"container_id"    => "auto-teaser-%post_id%",
			"container_class" => ""
		);
		foreach($default_options as $option_name => $default_value) {
			if(isset($options[$option_name]) || empty($options[$option_name])) {
				$options[$option_name] = $default_value;
			}
			if(strstr($options[$option_name], "%post_id%")) {
				$options[$option_name] = str_replace("%post_id%", $post_id, $options[$option_name]);
			}
		}
		$container_start = "";
		$container_end   = "";
		if($options["container"]) {
			$container_start = "<" . $options["container"] . ' id="' . $options["container_id"] . '" class="' . $options["container_class"] . '">';
			$container_end   = "</" . $options["container"] . ">";
		}
		$teaser = auto_teaser_get_auto_teaser($post_id, $options);
		$teaser = $container_start . $teaser . $container_end;
		echo $teaser;
	}