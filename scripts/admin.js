jQuery(document).ready(
	function() {
		"use strict";

		/**
		 * Toggles a link and slides down the hidden content.
		 * @type {Array}
		 *
		 * @since 0.1
		 */
		var toggledContents, customTextDefaultHeight, customText, placeholdersShown;

		toggledContents = [];

		function toggleLink(defaultLink, toggledLink, toggledContent) {
			/** Get the elements with jQuery */
			var theDefaultLink = jQuery(defaultLink);
			var theToggledLink = jQuery(toggledLink);
			var theToggledContent = jQuery(toggledContent);
			var toggledContentId = theToggledContent.attr("id");

			if(jQuery.inArray(toggledContentId, toggledContents) === "-1") {
				theToggledContent.slideDown();
				theDefaultLink.hide();
				theToggledLink.show();
				toggledContents.push(toggledContentId);
			} else {
				theToggledContent.slideUp();
				theToggledLink.hide();
				theDefaultLink.show();
				toggledContents.splice(toggledContentId.indexOf(toggledContents));
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
				"teaser", "excerpt", "permalink", "author_id", "author_name", "author_url", "comments_number", "categories", "category_id", "category_name", "category_url"
			];
			jQuery(placeholders).each(
				function() {
					var example = jQuery("#placeholder-" + this.replace("_", "-") + "-example").text();
					teaser = teaser.replace("%" + this + "%", example);
				}
			);
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
		jQuery("#teaser-format").keydown(
			function() {
				setTimeout(
					function() {
						updateTeaserCharacterCount();
						updateTeaserFormatPreview();
					}, 50
				);
			}
		);

		/**
		 * Shows/hides preview.
		 *
		 * @since 0.1
		 */
		var previewToggled = false;
		jQuery("#show-teaser-format-preview, #hide-teaser-format-preview").click(
			function() {
				var preview, showLink, hideLink;

				preview = jQuery("#teaser-format-preview");
				showLink = jQuery("#show-teaser-format-preview");
				hideLink = jQuery("#hide-teaser-format-preview");

				if(previewToggled) {
					hideLink.hide();
					showLink.show();
					preview.slideUp();
					previewToggled = false;
				} else {
					hideLink.show();
					showLink.hide();
					preview.slideDown();
					previewToggled = true;
				}
				event.preventDefault();
			}
		);

		/**
		 * Shows/hides placeholder list.
		 *
		 * @since 0.1
		 */
		placeholdersShown = false;
		jQuery("#show-placeholders, #hide-placeholders").click(
			function() {
				var placeholders = jQuery("#teaser-format-placeholders");
				var showPlaceholders = jQuery("#show-placeholders");
				var hidePlaceholders = jQuery("#hide-placeholders");
				event.preventDefault();
				if(placeholdersShown) {
					placeholders.slideUp();
					showPlaceholders.show();
					hidePlaceholders.hide();
					placeholdersShown = false;
				} else {
					showPlaceholders.hide();
					hidePlaceholders.show();
					placeholders.slideDown();
					placeholdersShown = true;
				}
			}
		);

		/**
		 * Adds a clicked placeholder to the teaser format textarea.
		 *
		 * @since 0.1
		 */
		jQuery("#teaser-format-placeholders").find("code").click(
			function() {
				var teaserFormat = jQuery("#teaser-format");
				teaserFormat.attr("value", teaserFormat.val() + jQuery(this).text());
				updateTeaserCharacterCount();
				updateTeaserFormatPreview();
			}
		);

		/**
		 * Dynamically displays the split style.
		 *
		 * @since 0.1
		 */
		jQuery("#teaser-split-style").change(
			function() {
				var selected = jQuery(this).find("option:selected");
				var splitValue = jQuery("#teaser-split-value-container");
				var customSplitValue = jQuery("#teaser-custom-split-value-container");
				if(selected.attr("value") === "custom") {
					splitValue.hide();
					customSplitValue.slideDown();
				} else {
					customSplitValue.hide();
					splitValue.slideDown();
					jQuery("#teaser-split-style-label").text(selected.val()).hide().slideDown();
				}
			}
		);

		/**
		 * "Allow HTML" row.
		 *
		 * @since 0.1
		 */
		jQuery("#row-allow-html").find("input[type='radio']").click(
			function() {
				var tags = jQuery("#allow-html-tags");
				tags.attr("disabled", "disabled");
				if(jQuery(this).val() === "custom") {
					tags.removeAttr("disabled");
				}
			}
		);

		jQuery("#backup-teaser").change(
			function() {
				var customText = jQuery("#backup-teaser-custom-text-container");
				if(jQuery(this).find("option:selected").attr("value") === "custom_text") {
					customText.slideDown();
				} else {
					customText.slideUp();
				}
			}
		);

		/**
		 * Extends the textarea when in focus.
		 *
		 * @since 0.1
		 */
		customText = jQuery("#backup-teaser-custom-text");
		customTextDefaultHeight = customText.css("height");
		customText.focus(
			function() {
				jQuery(this).animate(
					{
						"height": "150px"
					}
				);
			}
		);
		/**
		 * Resets the textarea to default height when focus out.
		 *
		 * @since 0.1
		 */
		customText.focusout(
			function() {
				jQuery(this).animate(
					{
						"height": customTextDefaultHeight
					}
				);
			}
		);
	}
);