jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function updateTagable( e ) {
		var $this = $( this );
		// If the function is called on cloning, then change the field name and clear all results
		// @see clone.js
		if ( e.hasOwnProperty( 'type' ) && 'clone' == e.type ) {
			// Clear all results
            $this.val( '' );
		}

		$this.tokenfield({
            autocomplete: {
                source: $this.data('options'),
                delay: 100
            },
			//showAutocompleteOnFocus: true
        });

        $this.on('tokenfield:createtoken', function (event) {
			if (iwjmb_tagable_exclude_skills.length > 0 && $.inArray(event.attrs.value.toLowerCase(),iwjmb_tagable_exclude_skills) >= 0){
				event.preventDefault();
			}
            var existingTokens = $(this).tokenfield('getTokens');
            $.each(existingTokens, function(index, token) {
                if (token.value.toLowerCase() === event.attrs.value.toLowerCase())
                    event.preventDefault();
            });
        });

       //using typeahead
       /* var engine = new Bloodhound({
            local: [{value: 'red'}, {value: 'blue'}, {value: 'green'} , {value: 'yellow'}, {value: 'violet'}, {value: 'brown'}, {value: 'purple'}, {value: 'black'}, {value: 'white'}],
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        engine.initialize();

	   $this.tokenfield({
			typeahead: [null, { source: engine.ttAdapter() }]
	   });*/

	}

	$( '.iwjmb-tagable-wrapper input[type="text"]' ).each( updateTagable );
	//$( '.iwjmb-input' ).on( 'clone', ':input.iwjmb-tagable', updateTagable );
} );
