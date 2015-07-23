<?php
	/**
	 * The plugin's settings page displayed in the admin interface under
	 * "Settings" > "Auto Teaser settings".
	 *
	 * @since 0.1
	 */
	function auto_teaser_settings_page() {
		$updated = false;
		/** Check if form has been sent */
		if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "save_settings")) {
			$settings = auto_teaser_get_default_settings();
			foreach($settings as $setting => $default_value) {
				/** Update setting if set */
				if(isset($_POST[$setting])) {
					update_option("auto_teaser_" . $setting, $_POST[$setting]);
					$updated = true;
				}
			}
		}
		?>
		<div class="wrap">
			<h2><?php _e("Auto Teaser settings", "auto_teaser"); ?></h2>
			<?php
				/** Display success message */
				if($updated) {
					echo '<div id="message" class="updated"><p><strong>' . __("Settings saved.", "auto_teaser") . '</strong></p></div>';
				}
			?>
			<form action="" method="post">
				<table class="form-table" id="auto-teaser-settings">
					<tr id="row-teaser-format">
						<th>
							<label for="teaser-format">
								<i class="fa fa-align-left"></i>
								<?php _e("Teaser format", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<textarea placeholder="<?php _e("Enter teaser format...", "auto_teaser"); ?>" name="teaser_format" id="teaser-format"><?php echo stripslashes(esc_attr(auto_teaser_get_setting("teaser_format"))); ?></textarea>
							<div id="teaser-character-counter"><?php echo sprintf(__("%s characters", "auto_teaser"), '<span id="teaser-character-count">0</span>'); ?></div>
							<p class="description">
								<?php _e("Defines the way the teaser is being displayed. <strong>HTML allowed</strong>.", "auto_teaser"); ?>
							</p>
							<p>
								<a href="#" id="show-teaser-format-preview"><?php _e("Show preview", "auto_teaser"); ?></a>
								<a href="#" id="hide-teaser-format-preview" hidden="hidden"><?php _e("Hide preview", "auto_teaser"); ?></a> &vert; <a href="#" id="show-placeholders"><?php _e("Show placeholders", "auto_teaser"); ?></a>
								<a href="#" id="hide-placeholders" hidden="hidden"><?php _e("Hide placeholders", "auto_teaser"); ?></a>
							</p>
							<div id="teaser-format-preview" hidden="hidden"></div>
							<div id="teaser-format-placeholders" hidden="hidden">
								<table>
									<?php
										$placeholder_examples = array(
											"teaser"          => "",
											"excerpt"         => "",
											"permalink"       => get_bloginfo("url") . "/my-category/my-post-permalink/",
											"author_id"       => 4,
											"author_name"     => __("Administrator", "auto_teaser"),
											"author_url"      => "",
											"comments_number" => 15,
											"categories"      => array(
												__("Travel reports", "auto_teaser"),
												__("Blog", "auto_teaser"),
												__("All posts", "auto_teaser")
											),
											"category_id"     => "",
											"category_name"   => "",
											"category_url"    => ""
										);
										$random_posts         = new WP_Query(
											array(
												"post_type" => "any",
												"orderby"   => "rand",
												"showposts" => 99
											)
										);
										foreach($random_posts->posts as $random_post) {
											if($random_post->post_excerpt) {
												$placeholder_examples["excerpt"] = $random_post->post_excerpt;
												$placeholder_examples["teaser"]  = $random_post->post_excerpt;
											}
											if($random_post->post_author) {
												$placeholder_examples["author_id"]   = $random_post->post_author;
												$placeholder_examples["author_name"] = get_the_author_meta("display_name", $random_post->post_author);
												$placeholder_examples["author_url"]  = get_author_posts_url($random_post->post_author);
											}
											if(count($random_post->post_category)) {
												$placeholder_examples["category_id"]   = $random_post->post_category;
												$placeholder_examples["category_name"] = get_cat_name($random_post->post_category);
												$placeholder_examples["category_url"]  = get_category_link($random_post->post_category);
												if(count($random_post->post_category) > 1) {
													foreach($random_post->post_category as $category_id) {
														$placeholder_examples["categories"][] = get_cat_name($category_id);
													}
												}
											}
											if($random_post->comment_count) {
												$placeholder_examples["comments_number"] = $random_post->comment_count;
											}
										}
										$placeholder_examples["category_id"] = array_rand($placeholder_examples["category_id"]);
									?>
									<tr>
										<th>
											<code>%teaser%</code>
											<span id="placeholder-teaser-example" class="placeholder-example"><?php echo $placeholder_examples["teaser"]; ?></span>
										</th>
										<td><?php _e("The generated teaser.", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%excerpt%</code>
											<span id="placeholder-excerpt-example" class="placeholder-example"><?php echo $placeholder_examples["excerpt"]; ?></span>
										</th>
										<td><?php _e("Post's excerpt.", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%permalink%</code>
											<span id="placeholder-permalink-example" class="placeholder-example"><?php echo $placeholder_examples["permalink"]; ?></span>
										</th>
										<td><?php _e("Permalink (URL) to the current post.", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%author_{id|name|url}%</code>
											<span id="placeholder-author-id-example" class="placeholder-example"><?php echo $placeholder_examples["author_id"]; ?></span>
											<span id="placeholder-author-name-example" class="placeholder-example"><?php echo $placeholder_examples["author_name"]; ?></span>
											<span id="placeholder-author-url-example" class="placeholder-example"><?php echo $placeholder_examples["author_url"]; ?></span>
										</th>
										<td><?php _e("Author of the post", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%comments_number%</code>
											<span id="placeholder-comments-number-example" class="placeholder-example"><?php echo $placeholder_examples["comments_number"]; ?></span>
										</th>
										<td><?php _e("The total number of comments.", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%categories%</code>
											<span id="placeholder-categories-example" class="placeholder-example"><?php echo implode(", ", $placeholder_examples["categories"]); ?></span>
										</th>
										<td><?php _e("Post's categories separated with commas.", "auto_teaser"); ?></td>
									</tr>
									<tr>
										<th>
											<code>%category_{id|name|url}%</code>
								<span id="placeholder-category-id-example" class="placeholder-example">
									<?php echo $placeholder_examples["category_id"]; ?>
								</span>
											<span id="placeholder-category-name-example" class="placeholder-example"><?php echo $placeholder_examples["category_name"]; ?></span>
											<span id="placeholder-category-url-example" class="placeholder-example"><?php echo $placeholder_examples["category_url"]; ?></span>
										</th>
										<td><?php _e("First found category post is saved in.", "auto_teaser"); ?></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr id="row-teaser-split-style">
						<th>
							<label for="teaser-split-style">
								<i class="fa fa-scissors"></i>
								<?php _e("Teaser split style", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<select name="teaser_split_style" id="teaser-split-style">
								<?php
									$styles = array(
										"paragraphs" => __("Paragraphs", "auto_teaser"),
										"sentences"  => __("Sentences", "auto_teaser"),
										"words"      => __("Words", "auto_teaser"),
										"characters" => __("Characters", "auto_teaser"),
										"custom"     => __("Custom", "auto_teaser")
									);
									foreach($styles as $value => $label) {
										$selected = "";
										if(auto_teaser_get_setting("teaser_split_style") == $value) {
											$selected = " selected";
										}
										echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
									}
								?>
							</select>
							<p class="description"><?php _e("How should Auto Teaser split the teaser?", "auto_teaser"); ?></p>
						<span id="teaser-custom-split-value-container"<?php auto_teaser_check_setting("teaser_split_style", "custom", "hidden", "!="); ?>>
							<?php
								$selected_before = auto_teaser_get_check_setting("teaser_custom_split_position", "before", "selected");
								$selected_after  = auto_teaser_get_check_setting("teaser_custom_split_position", "after", "selected");
								$select          = '<select name="teaser_custom_split_position"><option value="before" ' . $selected_before . '>' . __("before", "auto_teaser") . '</option><option value="after" ' . $selected_after . '>' . __("after", "auto_teaser") . '</option></select>';
							?>
							<label for="teaser-custom-split-value"><?php echo sprintf(__("Split %s finding", "auto_teaser"), $select); ?></label>
							<input type="text" name="teaser_custom_split_value" id="teaser-custom-split-value" value="<?php auto_teaser_the_setting("teaser_custom_split_value"); ?>"/>
						</span>
						<span id="teaser-split-value-container"<?php auto_teaser_check_setting("teaser_split_style", "custom", "hidden"); ?>>
							<label for="teaser-split-value"><?php _e("Show", "auto_teaser"); ?></label>
							<input type="text" name="teaser_split_value" maxlength="3" id="teaser-split-value" value="<?php auto_teaser_the_setting("teaser_split_value"); ?>"/>
							<label for="teaser-split-value" id="teaser-split-style-label"><?php auto_teaser_the_setting("teaser_split_style") ?></label>
						</span>
						</td>
					</tr>
					<tr id="row-replace-excerpt">
						<th>
							<label for="replace-excerpt-yes">
								<i class="fa fa-clipboard"></i>
								<?php _e("Replace default excerpt", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<input type="radio" name="replace_excerpt" value="on" id="replace-excerpt-yes"<?php auto_teaser_check_setting("replace_excerpt", "on"); ?> />
							<label for="replace-excerpt-yes"><?php _e("Yes", "auto_teaser"); ?></label>
							<input type="radio" name="replace_excerpt" value="off" id="replace-excerpt-no"<?php auto_teaser_check_setting("replace_excerpt", "off"); ?> />
							<label for="replace-excerpt-no"><?php _e("No", "auto_teaser"); ?></label>
							<p class="description"><?php _e("Displays the auto teaser wherever WordPress' native <code>the_excerpt()</code> or <code>get_the_excerpt()</code> is being called.", "auto_teaser"); ?></p>
						</td>
					</tr>
					<tr id="row-allow-html">
						<th>
							<label for="allow-html-yes">
								<i class="fa fa-code"></i>
								<?php _e("Allow HTML", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<input type="radio" name="allow_html" id="allow-html-yes" value="on"<?php auto_teaser_check_setting("allow_html", "on"); ?> />
							<label for="allow-html-yes"><?php _e("Yes", "auto_teaser"); ?></label>
							<input type="radio" name="allow_html" id="allow-html-no" value="off""<?php auto_teaser_check_setting("allow_html", "off"); ?>
							/>
							<label for="allow-html-no"><?php _e("No", "auto_teaser"); ?></label>
							<input type="radio" name="allow_html" id="allow-html-custom" value="custom""<?php auto_teaser_check_setting("allow_html", "custom"); ?>
							/>
							<label for="allow-html-custom"><?php _e("Only allowed tags", "auto_teaser"); ?>: </label>
							<label for="allow-html-tags" hidden="hidden"></label>
							<input type="text" id="allow-html-tags" name="allow_html_tags"<?php auto_teaser_check_setting("allow_html", "custom", "disabled", "!=");
							?> value="<?php echo normalize_whitespace(implode(", ", auto_teaser_get_setting("allow_html_tags"))); ?>" placeholder="<?php _e("E.g.: p, div, br", "auto_teaser"); ?>"/>
							<p class="description"><?php _e("Defines whether HTML is allowed inside the teaser or not. Separate allowed tags with commas.", "auto_teaser"); ?></p>
						</td>
					</tr>
					<tr id="row-exclude-ids">
						<th>
							<label for="exclude-ids">
								<i class="fa fa-times"></i>
								<?php _e("Exclude post IDs", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<input type="text" name="exclude_ids" id="exclude-ids" placeholder="<?php _e("E.g.: 13, 84, 194", "auto_teaser"); ?>" value="<?php echo implode(", ", auto_teaser_get_setting("exclude_ids")); ?>"/>
							<p class="description"><?php _e("Post IDs Auto Teaser will ignore. Separate multiple IDs with commas.", "auto_teaser"); ?></p>
						</td>
					</tr>
					<tr id="row-backup-teaser">
						<th>
							<label for="backup-teaser">
								<i class="fa fa-floppy-o"></i>
								<?php _e("Backup teaser", "auto_teaser"); ?>
							</label>
						</th>
						<td>
							<label for="backup-teaser"><?php _e("Show", "auto_teaser"); ?> </label>
							<select name="backup_teaser" id="backup-teaser">
								<option value="post_content"<?php auto_teaser_check_setting("backup_teaser", "post_content", "selected"); ?>><?php _e("full post content", "auto_teaser"); ?></option>
								<option value="post_excerpt"<?php auto_teaser_check_setting("backup_teaser", "post_excerpt", "selected"); ?>><?php _e("post excerpt", "auto_teaser"); ?></option>
								<option value="custom_text"<?php auto_teaser_check_setting("backup_teaser", "custom_text", "selected"); ?>><?php _e("custom text", "auto_teaser"); ?></option>
								<option value="blank"<?php auto_teaser_check_setting("backup_teaser", "blank", "selected"); ?>><?php _e("nothing", "auto_teaser"); ?></option>
							</select>
							<label for="backup-teaser-custom-text" hidden="hidden"></label>
							<p id="backup-teaser-custom-text-container" hidden="hidden">
								<textarea name="backup_teaser_custom_text" id="backup-teaser-custom-text" placeholder="<?php _e("Enter your custom text...", "auto_teaser"); ?>"><?php auto_teaser_the_setting("backup_teaser_custom_text"); ?></textarea>
							</p>
							<p class="description">
								<?php _e("What to display in case the auto teaser could not be generated.", "auto_teaser"); ?>
							</p>
						</td>
					</tr>
				</table>
				<input type="submit" class="button button-primary" value="<?php _e("Save changes", "auto_teaser"); ?>"/>
				<?php wp_nonce_field("save_settings", "nonce"); ?>
			</form>
			<br/>
			<br/>
			<div id="report-bug">
				<small><?php echo sprintf(__('Found an error? Help making Auto Teaser better by <a href="%s" title="Click here to report a bug" target="_blank">quickly reporting the bug</a>.', "auto_teaser"), "http://www.wordpress.org/support/plugin/auto-teaser#postform"); ?></small>
			</div>
		</div>
		<?php
	}