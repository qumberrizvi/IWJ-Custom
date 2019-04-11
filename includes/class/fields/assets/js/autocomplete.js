jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function updateAutocomplete( e ) {
		var $this = $( this ),
			$search = $this.siblings( '.iwjmb-autocomplete-search' ),
			$result = $this.siblings( '.iwjmb-autocomplete-results' ),
			name = $this.attr( 'name' );

		// If the function is called on cloning, then change the field name and clear all results
		// @see clone.js
		if ( e.hasOwnProperty( 'type' ) && 'clone' == e.type ) {
			// Clear all results
			$result.html( '' );
		}

		$search.removeClass( 'ui-autocomplete-input' ).autocomplete( {
			minLength: 0,
			source: $this.data( 'options' ),
			select: function ( event, ui ) {
				$result.append(
					'<div class="iwjmb-autocomplete-result">' +
					'<div class="label">' + ( typeof ui.item.excerpt !== 'undefined' ? ui.item.excerpt : ui.item.label ) + '</div>' +
					'<div class="actions">' + IWJMB_Autocomplete.delete + '</div>' +
					'<input type="hidden" class="iwjmb-autocomplete-value" name="' + name + '" value="' + ui.item.value + '">' +
					'</div>'
				);

				// Reinitialize value
				$search.val( '' );

				return false;
			}
		} );
	}

	$( '.iwjmb-autocomplete-wrapper input[type="hidden"]' ).each( updateAutocomplete );
	$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-autocomplete', updateAutocomplete );

	// Handle remove action
	$( document ).on( 'click', '.iwjmb-autocomplete-result .actions', function () {
		// remove result
		$( this ).parent().remove();
	} );
} );
