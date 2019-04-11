(function ( $ ) {
	'use strict';

	// Use function construction to store map & DOM elements separately for each instance
	var MapAddressField = function ( $container ) {
		this.$container = $container;
        this.$addressField = this.$container.find('input[type="text"]');
        this.$address_components = this.$container.find('input[type="hidden"]');

        var searchBox = new google.maps.places.SearchBox(this.$addressField.get(0));
        $(this.$addressField ).keydown(function (e) {
            if (e.which == 13 && $('.pac-container:visible').length) return false;
        });

        var that = this;
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function(e) {

            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // For each place, get the icon, name and location.
            places.forEach(function(place) {

                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                that.$address_components.val(JSON.stringify(place.address_components));
            });
        });
	};


	$( function () {
		$( '.iwjmb-map-address-field' ).each( function () {
			var field = new MapAddressField( $( this ) );
		} );

		$( '.iwjmb-input' ).on( 'clone', function () {
			$( '.iwjmb-map-address-field' ).each( function () {
				var field = new MapAddressField( $( this ) );
			} );
		} );
	} );

})( jQuery );
