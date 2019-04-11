window.iwjmb = window.iwjmb || {};

jQuery( function ( $ ) {
	'use strict';

	var views = iwjmb.views = iwjmb.views || {},
		MediaField = views.MediaField,
		MediaItem = views.MediaItem,
		MediaList = views.MediaList,
		ImageField;

	ImageField = views.ImageField = MediaField.extend( {
		createList: function () {
			this.list = new MediaList( {
				controller: this.controller,
				itemView: MediaItem.extend( {
					className: 'iwjmb-image-item',
					template: wp.template( 'iwjmb-image-item' )
				} )
			} );
		}
	} );

	/**
	 * Initialize image fields
	 */
	function initImageField() {
		new ImageField( {input: this, el: $( this ).siblings( 'div.iwjmb-media-view' )} );
	}

	$( 'input.iwjmb-image_advanced' ).each( initImageField );
	$( '#wpbody' ).on( 'clone', 'input.iwjmb-image_advanced', initImageField )
} );
