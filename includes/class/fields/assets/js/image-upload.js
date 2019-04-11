window.iwjmb = window.iwjmb || {};

jQuery( function ( $ ) {
	'use strict';

	var views = iwjmb.views = iwjmb.views || {},
		ImageField = views.ImageField,
		ImageUploadField,
		UploadButton = views.UploadButton;

	ImageUploadField = views.ImageUploadField = ImageField.extend( {
		createAddButton: function () {
			this.addButton = new UploadButton( {controller: this.controller} );
		}
	} );

	/**
	 * Initialize fields
	 * @return void
	 */
	function init() {
		new ImageUploadField( {input: this, el: $( this ).siblings( 'div.iwjmb-media-view' )} );
	}

	$( ':input.iwjmb-image_upload, :input.iwjmb-plupload_image' ).each( init );
	$( '.iwjmb-input' )
		.on( 'clone', ':input.iwjmb-image_upload, :input.iwjmb-plupload_image', init )
} );
