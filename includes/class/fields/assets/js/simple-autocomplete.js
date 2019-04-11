jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function updateSimpleAutocomplete( e ) {
		var $this = $( this );

		// If the function is called on cloning, then change the field name and clear all results
		// @see clone.js
		if ( e.hasOwnProperty( 'type' ) && 'clone' == e.type ) {
			// Clear all results
            $this.val( '' );
		}

        $this.autocomplete( {/*removeClass( 'ui-autocomplete-input' )*/
			minLength: 0,
			source: $this.data( 'options' ),
		} );
	}

	$( '.iwjmb-simple_autocomplete-wrapper input[type="text"]' ).each( updateSimpleAutocomplete );
	$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-simple-autocomplete', updateSimpleAutocomplete );
} );
