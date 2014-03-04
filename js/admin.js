(function ($) {
	"use strict";
	$(function () {
	    $('#upload_file_image_button').click(function() {
	        wp.media.editor.send.attachment = function(props, attachment) {
	            $('.award_image').val(attachment.url);
	        }

	        wp.media.editor.open(this);

	        return false;
	    });
	});
}(jQuery));