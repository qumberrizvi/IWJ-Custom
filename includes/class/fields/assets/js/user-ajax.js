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
			options.ajax = {
				url: rmb_user_ajax.ajax_url,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						action: rmb_user_ajax.action,
						_ajax_nonce: rmb_user_ajax.security,
						q: params.term,
						role: $this.data('role')
					};
				},
				processResults: function (data) {
					// parse the results into the format expected by Select2.
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data
					return {
						results: data
					};
				},
				cache: true
			};
		$this.siblings( '.select2-container' ).remove();
		$this.show().select2( options );
	}

	$( ':input.iwjmb-user_ajax' ).each( update );
	$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-user_ajax', update );
} );
