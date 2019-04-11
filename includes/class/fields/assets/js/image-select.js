jQuery( function ( $ ) {
	'use strict';

	$( 'body' ).on( 'change', '.iwjmb-image-select input', function () {
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected ) {
			$parent.addClass( 'iwjmb-active' );
			if ( type === 'radio' ) {
				$others.removeClass( 'iwjmb-active' );
			}
		} else {
			$parent.removeClass( 'iwjmb-active' );
		}
	} );
	$( '.iwjmb-image-select input' ).trigger( 'change' );
} );
