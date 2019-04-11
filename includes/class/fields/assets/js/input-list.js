jQuery( function ( $ ) {
	function update() {
		var $this = $( this ),
			$children = $this.closest( 'li' ).children( 'ul' );

		if ( $this.is( ':checked' ) ) {
			$children.removeClass( 'hidden' );
		} else {
			$children
				.addClass( 'hidden' )
				.find( 'input' )
				.removeAttr( 'checked' );
		}
	}

	$( '.iwjmb-input' )
		.on( 'change', '.iwjmb-input-list.collapse :checkbox', update )
		.on( 'clone', '.iwjmb-input-list.collapse :checkbox', update );
	$( '.iwjmb-input-list.collapse :checkbox' ).each( update );
} );
