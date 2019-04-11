jQuery( function ( $ ) {
	'use strict';

	/**
	 * Turn select field into beautiful dropdown with select2 library
	 * This function is called when document ready and when clone button is clicked (to update the new cloned field)
	 *
	 * @return void
	 */
	function update() {
		var $this = $( this ),
			options = $this.data( 'options' );
		if(typeof options === 'undefined' || !options){
            options = {}
		}
		if(options && (options.minimumResultsForSearch && (options.minimumResultsForSearch == -1 || options.minimumResultsForSearch == 'Infinity')) || options.multiple){
            options.dropdownCssClass = 'iwj-select-2-wsearch';
		}
        $this.siblings( '.select2-container' ).remove();
		$this.show().select2( options );

		iwjmbSelect.bindEvents( $this );
	}

	$( ':input.iwjmb-select_advanced' ).each( update );
	$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-select_advanced', update );
} );
