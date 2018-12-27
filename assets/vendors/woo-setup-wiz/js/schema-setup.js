jQuery(document).ready(function($) {

	// Hide/Show Organization or Person fields 
	$(".organization_or_person").hide();
	$(".organization-logo").hide();
	
	var inputValue = $(".schema-wizard-radio:checked").attr("value");
	
	if ( inputValue == 'person' ) {
		$('.organization_or_person label').text('Person Name');
    	$('.organization_or_person').show();
		$('.organization-logo').hide();
	} 
	else {
		$('.organization_or_person').show();
		$('.organization-logo').show();
		$('.organization_or_person label').text('Organization Name');
	}
		
	$(".schema-wizard-radio").change(function(){
        var inputValue = $(this).attr("value");
        if ($(this).val() == 'person') {
			$('.organization_or_person label').text('Person Name');
    		$('.organization_or_person').show();
			$('.organization-logo').hide();
    	}
   		else {
   			$('.organization_or_person').show();
			$('.organization-logo').show();
			$('.organization_or_person label').text('Organziation Name');
   		}
	});
	
	// Tooltips
	$('.schema-wp-help-tip').tooltip({
		content: function() {
			return $(this).prop('title');
		},
		tooltipClass: 'schema-wp-ui-tooltip',
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit',
		},
		hide: {
			duration: 200,
		},
		show: {
			duration: 200,
		},
	});
	
	// Date picker
	var schema_wp_datepicker = $( '.schema_wp_datepicker' );
	if ( schema_wp_datepicker.length > 0 ) {
		var dateFormat = 'mm/dd/yy';
		schema_wp_datepicker.datepicker( {
			dateFormat: dateFormat
		} );
	}
	
	
	
	/**
	 * Settings screen JS
	 */
	var Schema_WP_Settings = {

		init : function() {
			this.general();
		},

		general : function() {

			var schema_wp_color_picker = $('.schema-wp-color-picker');

			if( schema_wp_color_picker.length ) {
				schema_wp_color_picker.wpColorPicker();
			}

			// Settings Upload field JS
			if ( typeof wp === "undefined" || '1' !== schema_wp_vars.new_media_ui ) {
				//Old Thickbox uploader
				var schema_wp_settings_upload_button = $( '.schema_wp_settings_upload_button' );
				if ( schema_wp_settings_upload_button.length > 0 ) {
					window.formfield = '';

					$( document.body ).on('click', schema_wp_settings_upload_button, function(e) {
						e.preventDefault();
						window.formfield = $(this).parent().prev();
						window.tbframe_interval = setInterval(function() {
							jQuery('#TB_iframeContent').contents().find('.savesend .button').val(schema_wp_vars.use_this_file).end().find('#insert-gallery, .wp-post-thumbnail').hide();
						}, 2000);
						tb_show( schema_wp_vars.add_new_download, 'media-upload.php?TB_iframe=true' );
					});

					window.schema_wp_send_to_editor = window.send_to_editor;
					window.send_to_editor = function (html) {
						if (window.formfield) {
							imgurl = $('a', '<div>' + html + '</div>').attr('href');
							window.formfield.val(imgurl);
							window.clearInterval(window.tbframe_interval);
							tb_remove();
						} else {
							window.schema_wp_send_to_editor(html);
						}
						window.send_to_editor = window.schema_wp_send_to_editor;
						window.formfield = '';
						window.imagefield = false;
					};
				}
			} else {
				// WP 3.5+ uploader
				var file_frame;
				window.formfield = '';

				$( document.body ).on('click', '.schema_wp_settings_upload_button', function(e) {

					e.preventDefault();

					var button = $(this);

					window.formfield = $(this).parent().prev();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
						file_frame.open();
						return;
					}
					
				/*
					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						frame: 'post',
						state: 'insert',
						title: button.data( 'uploader_title' ),
						button: {
							text: button.data( 'uploader_button_text' )
						},
						multiple: false
					});
				*/
					
					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						frame: 'post',
						title: 'Choose Image',
						multiple: false,
						library: {
							type: 'image'
						},
						button: {
							text: 'Use Image'
						}
					});

					file_frame.on( 'menu:render:default', function( view ) {
						// Store our views in an object.
						var views = {};

						// Unset default menu items
						view.unset( 'library-separator' );
						view.unset( 'gallery' );
						view.unset( 'featured-image' );
						view.unset( 'embed' );

						// Initialize the views in our view object.
						view.set( views );
					} );
		
					// When an image is selected, run a callback.
					file_frame.on( 'insert', function() {

						var selection = file_frame.state().get('selection');
						selection.each( function( attachment, index ) {
							attachment = attachment.toJSON();
							window.formfield.val(attachment.url);
							
							/* image prevoew */
							var img = $('<img />');
							img.attr('src', attachment.url);
							// replace previous image with new one if selected
							$('#preview_image').empty().append( img );

							// show preview div when image exists
							if ( $('#preview_image img') ) {
								$('#preview_image').show();
							}
			
						});
						
					});

					// Finally, open the modal
					file_frame.open();
				});


				// WP 3.5+ uploader
				var file_frame;
				window.formfield = '';
			}

		},
		
	}
	Schema_WP_Settings.init();

});
