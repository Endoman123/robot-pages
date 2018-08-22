(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function() {
		// Instantiates the variable that holds the media library frame.
		var meta_image_frame;
	
		// Runs when the image button is clicked.
		$('.button--meta-image').click(function(e) {
			// Prevents the default action from occuring.
			e.preventDefault();
			
			// If the frame already exists, re-open it.
			if ( meta_image_frame ) {
				meta_image_frame.open();
				return;
			}
	
			// Sets up the media library frame
			meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
				title: meta_image.title,
				button: { text:  meta_image.button },
				library: { type: 'image' }
			});
	
			// Runs when an image is selected.
			meta_image_frame.on('select', function(){
	
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
	
				// Sends the attachment URL to our custom image input field.
				$(`input#${this.id}`).val(media_attachment.url);
			});
	
			// Opens the media library frame.
			meta_image_frame.open();
		});
	});
})( jQuery );
