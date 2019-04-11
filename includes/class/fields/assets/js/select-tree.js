jQuery( function ( $ ) {
	'use strict';

	function update() {
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.iwjmb-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.iwjmb-input' )
		.on( 'change', '.iwjmb-select-tree select', update )
		.on( 'clone', '.iwjmb-select-tree select', update );
} );
