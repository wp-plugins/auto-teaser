jQuery(document).ready(function() {
	/**
	 * Toggles a link and slides down the hidden content.
	 * @type {Array}
	 *
	 * @since 0.1
	 */
	var toggled_contents = [];

	function toggleLink(default_link, toggled_link, toggled_content) {
		/** Get the elements with jQuery */
		default_link = jQuery(default_link);
		toggled_link = jQuery(toggled_link);
		toggled_content = jQuery(toggled_content);
		var toggled_content_id = toggled_content.attr("id");

		if(jQuery.inArray(toggled_content_id, toggled_contents) == "-1") {
			toggled_content.slideDown();
			default_link.hide();
			toggled_link.show();
			toggled_contents.push(toggled_content_id);
		}
		else {
			toggled_content.slideUp();
			toggled_link.hide();
			default_link.show();
			toggled_contents.splice(toggled_content_id.indexOf(toggled_contents));
		}
	}

	/**
	 * Updates the teaser format preview.
	 *
	 * @since 0.1
	 */
	function updateTeaserFormatPreview() {
		var teaser = jQuery("#teaser-format").val();
		var placeholders = [
			"teaser",
			"excerpt",
			"permalink",
			"author_id",
			"author_name",
			"author_url",
			"comments_number",
			"categories",
			"category_id",
			"category_name",
			"category_url"
		];
		jQuery(placeholders).each(function() {
			var example = jQuery("#placeholder-" + this.replace("_", "-") + "-example").text();
			teaser = teaser.replace("%" + this + "%", example);
		});
		jQuery("#teaser-format-preview").html(teaser);
	}

	/**
	 * Updates the teaser character counter.
	 *
	 * @since 0.1
	 */
	function updateTeaserCharacterCount() {
		var teaser = jQuery("#teaser-format");
		jQuery("#teaser-character-count").text(teaser.val().length);
	}

	updateTeaserCharacterCount();
	updateTeaserFormatPreview();

	/**
	 * Updates the teaser character counter whenever a new character is entered.
	 *
	 * @since 0.1
	 */
	jQuery("#teaser-format").keydown(function() {
		setTimeout(function() {
			updateTeaserCharacterCount();
			updateTeaserFormatPreview();
		}, 50);
	});

	/**
	 * Shows/hides preview.
	 *
	 * @since 0.1
	 */
	jQuery("#show-teaser-format-preview, #hide-teaser-format-preview").click(function() {
		event.preventDefault();
		toggleLink("#show-teaser-format-preview", "#hide-teaser-format-preview", "#teaser-format-preview");
	});

	/**
	 * Shows/hides placeholder list.
	 *
	 * @since 0.1
	 */
	jQuery("#show-placeholders, #hide-placeholders").click(function() {
		event.preventDefault();
		toggleLink("#show-placeholders", "#hide-placeholders", "#teaser-format-placeholders");
	});

	/**
	 * Adds a clicked placeholder to the teaser format textarea.
	 *
	 * @since 0.1
	 */
	jQuery("#teaser-format-placeholders").find("code").click(function() {
		var teaser_format = jQuery("#teaser-format");
		teaser_format.attr("value", teaser_format.val() + jQuery(this).text());
		updateTeaserCharacterCount();
		updateTeaserFormatPreview();
	});

	/**
	 * Dynamically displays the split style.
	 *
	 * @since 0.1
	 */
	jQuery("#teaser-split-style").change(function() {
		var selected = jQuery(this).find("option:selected");
		var split_value = jQuery("#teaser-split-value-container");
		var custom_split_value = jQuery("#teaser-custom-split-value-container");
		if(selected.attr("value") == "custom") {
			split_value.hide();
			custom_split_value.slideDown();
		}
		else {
			custom_split_value.hide();
			split_value.slideDown();
			jQuery("#teaser-split-style-label").text(selected.val()).hide().slideDown();
		}
	});

	/**
	 * "Allow HTML" row.
	 *
	 * @since 0.1
	 */
	jQuery("#row-allow-html").find("input[type='radio']").click(function() {
		var tags = jQuery("#allow-html-tags");
		tags.attr("disabled", "disabled");
		if(jQuery(this).val() == "custom") {
			tags.removeAttr("disabled");
		}
	});

	jQuery("#backup-teaser").change(function() {
		var custom_text = jQuery("#backup-teaser-custom-text-container");
		if(jQuery(this).find("option:selected").attr("value") == "custom_text") {
			custom_text.slideDown();
		}
		else {
			custom_text.slideUp();
		}
	});

	/**
	 * Extends the textarea when in focus.
	 *
	 * @since 0.1
	 */
	var backup_teaser_custom_text = jQuery("#backup-teaser-custom-text");
	var backup_teaser_custom_text_default_height = backup_teaser_custom_text.css("height");
	backup_teaser_custom_text.focus(function() {
		jQuery(this).animate({
			"height": "150px"
		});
	});
	/**
	 * Resets the textarea to default height when focus out.
	 *
	 * @since 0.1
	 */
	backup_teaser_custom_text.focusout(function() {
		jQuery(this).animate({
			"height": backup_teaser_custom_text_default_height
		});
	});
});