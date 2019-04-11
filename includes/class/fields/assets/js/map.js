(function ( $ ) {
	'use strict';

	// Use function construction to store map & DOM elements separately for each instance
	var MapField = function ( $container ) {
		this.$container = $container;
	};

	// Use prototype for better performance
	MapField.prototype = {
		// Initialize everything
		init: function () {
			this.initDomElements();
			this.initMapElements();

			this.initMarkerPosition();
			this.addListeners();
			this.autocomplete();
		},

		// Initialize DOM elements
		initDomElements: function () {
			this.canvas = this.$container.find( '.iwjmb-map-canvas' )[0];
			this.$coordinate = this.$container.find( '.iwjmb-map-coordinate' );
			this.$address_components = this.$container.find( '.iwjmb-map-address_components' );
			this.$findButton = this.$container.find( '.iwjmb-map-goto-address-button' );
			this.addressField = this.$findButton.val();
		},

		// Initialize map elements
		initMapElements: function () {
			var defaultLoc = $( this.canvas ).data( 'default-loc' ),
				latLng;

			defaultLoc = defaultLoc ? defaultLoc.split( ',' ) : [53.346881, - 6.258860];
			latLng = new google.maps.LatLng( defaultLoc[0], defaultLoc[1] ); // Initial position for map
            var zoom = defaultLoc.length > 2 ? parseInt( defaultLoc[2], 10 ) : 14;

			this.map = new google.maps.Map( this.canvas, {
				center: latLng,
				zoom: zoom,
				streetViewControl: 0,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles:
                    (iwjmap.map_styles ? JSON.parse(iwjmap.map_styles) : [
                        {
                            "featureType": "administrative",
                            "elementType": "labels.text.fill",
                            "stylers": [
                                {
                                    "color": "#444444"
                                }
                            ]
                        },
                        {
                            "featureType": "landscape",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "color": "#f2f2f2"
                                }
                            ]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "road",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "saturation": -100
                                },
                                {
                                    "lightness": 45
                                }
                            ]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "simplified"
                                }
                            ]
                        },
                        {
                            "featureType": "road.arterial",
                            "elementType": "labels.icon",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "water",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "color": "#46bcec"
                                },
                                {
                                    "visibility": "on"
                                }
                            ]
                        }
                    ])
			} );
            var map_data = $(".iwjmb-map-field");
            if (map_data.length) {
                var maker_icon = map_data.data( "marker" );
            }
			this.marker = new google.maps.Marker( {position: latLng, map: this.map, icon: maker_icon, draggable: true} );
			this.geocoder = new google.maps.Geocoder();
		},

		// Initialize marker position
		initMarkerPosition: function () {
			var coord = this.$coordinate.val(),
				l,
				zoom;

			if ( coord ) {
				l = coord.split( ',' );
				this.marker.setPosition( new google.maps.LatLng( l[0], l[1] ) );

				zoom = l.length > 2 ? parseInt( l[2], 10 ) : 14;

				this.map.setCenter( this.marker.position );
				this.map.setZoom( zoom );
			}
			else if ( this.addressField ) {
				this.geocodeAddress();
			}
		},

		// Add event listeners for 'click' & 'drag'
		addListeners: function () {
			var that = this;
			google.maps.event.addListener( this.map, 'click', function ( event ) {
				that.marker.setPosition( event.latLng );
				that.updateCoordinate( event.latLng );
                that.geocoder.geocode({'location': event.latLng}, function(results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            that.$address_components.val(JSON.stringify(results[0].address_components));
                        } else {
                            console.log('No results found');
                        }
                    } else {
                        console.log('Geocoder failed due to: ' + status);
                    }
                });
			} );

			google.maps.event.addListener( this.map, 'zoom_changed', function ( event ) {
				that.updateCoordinate( that.marker.getPosition() );
			} );

			google.maps.event.addListener( this.marker, 'drag', function ( event ) {
				that.updateCoordinate( event.latLng );

                that.geocoder.geocode({'location': event.latLng}, function(results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            that.$address_components.val(JSON.stringify(results[0].address_components));
						} else {
                            console.log('No results found');
                        }
                    } else {
                        console.log('Geocoder failed due to: ' + status);
                    }
                });
			} );

			this.$findButton.on( 'click', function () {
				that.geocodeAddress();
				return false;
			} );

			/**
			 * Add a custom event that allows other scripts to refresh the maps when needed
			 * For example: when maps is in tabs or hidden div (this is known issue of Google Maps)
			 *
			 * @see https://developers.google.com/maps/documentation/javascript/reference
			 *      ('resize' Event)
			 */
			$( window ).on( 'iwjmb_map_refresh', function () {
				that.refresh();
			} );

			// Refresh on meta box hide and show
			$( document ).on( 'postbox-toggled', function () {
				that.refresh();
			} );
			// Refresh on sorting meta boxes
			$( '.meta-box-sortables' ).on( 'sortstop', function () {
				that.refresh();
			} );
		},

		refresh: function () {
			var zoom = this.map.getZoom(),
				center = this.map.getCenter();

			if ( this.map ) {
				google.maps.event.trigger( this.map, 'resize' );
				this.map.setZoom( zoom );
				this.map.setCenter( center );
			}
		},

		// Autocomplete address
		autocomplete: function () {
			var that = this;

			//normal search
            var searchBox = new google.maps.places.SearchBox($( '#' + this.addressField ).get(0));
            $( '#' + this.addressField ).keydown(function (e) {
                if (e.which == 13 && $('.pac-container:visible').length) return false;
            });

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
                    that.marker.setPosition(place.geometry.location);
                    that.marker.setTitle(place.name);
                    that.map.setCenter( place.geometry.location );
                    that.updateCoordinate( place.geometry.location );

                });
            });

            return;

            //auto complete search
            // No address field or more than 1 address fields, ignore
			if ( ! this.addressField || this.addressField.split( ',' ).length > 1 ) {
				return;
			}

			// If Meta Box Geo Location installed. Do not run auto complete.
			if ( $( '.iwjmb-geo-binding' ).length ) {
				$( '#' + this.addressField ).on( 'selected_address', function () {
					that.$findButton.trigger( 'click' );
				} );

				return false;
			}

			$( '#' + this.addressField ).autocomplete( {
				source: function ( request, response ) {
					that.geocoder.geocode( {
						'address': request.term
					}, function ( results ) {
						response( $.map( results, function ( item ) {
							return {
								label: item.formatted_address,
								value: item.formatted_address,
								latitude: item.geometry.location.lat(),
								longitude: item.geometry.location.lng()
							};
						} ) );
					} );
				},
				select: function ( event, ui ) {
					var latLng = new google.maps.LatLng( ui.item.latitude, ui.item.longitude );

					that.map.setCenter( latLng );
					that.marker.setPosition( latLng );
					that.updateCoordinate( latLng );
				}
			} );
		},

		// Update coordinate to input field
		updateCoordinate: function ( latLng ) {
			var zoom = this.map.getZoom();
			this.$coordinate.val( latLng.lat() + ',' + latLng.lng() + ',' + zoom );
		},

		// Find coordinates by address
		geocodeAddress: function () {
            var address,
				addressList = [],
				fieldList = this.addressField.split( ',' ),
				loop,
				that = this;

			for ( loop = 0; loop < fieldList.length; loop ++ ) {
				addressList[loop] = jQuery( '#' + fieldList[loop] ).val();
			}

			address = addressList.join( ',' ).replace( /\n/g, ',' ).replace( /,,/g, ',' );
			if ( address ) {
				this.geocoder.geocode( {'address': address}, function ( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						that.map.setCenter( results[0].geometry.location );
						that.marker.setPosition( results[0].geometry.location );
						that.updateCoordinate( results[0].geometry.location );
					}
				} );
			}
		}
	};

	$( function () {
		$( '.iwjmb-map-field' ).each( function () {
			var field = new MapField( $( this ) );
			field.init();

			$( this ).data( 'mapController', field );
		} );

		$( '.iwjmb-input' ).on( 'clone', function () {
			$( '.iwjmb-map-field' ).each( function () {
				var field = new MapField( $( this ) );
				field.init();

				$( this ).data( 'mapController', field );
			} );
		} );
	} );

})( jQuery );
