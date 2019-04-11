(function($){
	$(window).load(function(){
		var infowindow;
  		var map;
  		var markers=[];
  		var markerCluster;
        var geocoder;
        var autoComplete;
        var current_lat;
        var current_lng;
        var spinner_opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 14 // The line thickness
            , radius: 42 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#444' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 60 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '50%' // Top position relative to parent
            , left: '50%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'absolute' // Element positioning
        };
        var saved_list = [];

        $(".range-radius").asRange({
            tip: true,
            step: 1,
            value: parseInt(iwj_search_map.default_radius),
            max: parseInt(iwj_search_map.max_radius),
            min: parseInt(iwj_search_map.min_radius),
        });

        function initMap() {
            geocoder = new google.maps.Geocoder();
            autoComplete = new google.maps.places.Autocomplete(   
                document.getElementById('location'), {
                types: []
            });
			var lat = parseFloat( iwj_search_map.lat );
			var lng = parseFloat( iwj_search_map.lng );
			var zoom = parseFloat( iwj_search_map.zoom );
		    map = new google.maps.Map( document.getElementById( 'iw_search_map' ), {
                zoom: zoom,
//                gestureHandling: 'greedy',
//                scrollwheel: false,
		    	center: { lat: lat, lng: lng },
		    	styles:
                ( iwj_search_map.map_styles ? JSON.parse( iwj_search_map.map_styles ) : [
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
		    });

            infowindow = new InfoBubble({
                //map: map,
                content: ' ',
                shadowStyle: 1,
                maxWidth: 280,
                minWidth: 260,
                minHeight: 'auto',
                maxHeight: 900,
                padding: 30,
                backgroundColor: '#fff',
                borderRadius: 5,
                arrowSize: 25,
                borderWidth: 1,
                borderColor: 'transparent',
                disableAutoPan: false,
                hideCloseButton: false,
                arrowPosition: 50,
                backgroundClassName: 'infor-search-map',
                arrowStyle: 0,
                //autoPan: true,
                closeSrc: iwj_search_map.close_icon
            });

            google.maps.event.addListener(autoComplete, 'place_changed', function() {
                var place = autoComplete.getPlace();
                if (place.geometry) {
                    latLng = place.geometry.location;
                    $('#iwj_curent_lat').val(latLng.lat());
                    $('#iwj_curent_lng').val(latLng.lng());
                    map.panTo(place.geometry.location);
                    map.setZoom(12  );
                } 
            });
            var result = $('.data-array').data('array');
            ajaxSuccess(result, map);

		}
        

        $('.btn-search').click(function(){
            ajaxfilter();           
        });

        $('form.search-map').submit(function (e) {
            e.preventDefault();
            ajaxfilter();
        });

        $('.hide-advance').click(function(){
            that = $(this);
            $('form.search-map').toggle(400);
            that.toggleClass('active');
            if(that.hasClass('active')){
                that.html(iwj_search_map.show_advance_text);
            }else{
                that.html(iwj_search_map.hide_advance_text)
            }
        });

        $('.section-result').on('mouseenter', '.grid-content', function(){
            var that = $(this);
            var id = that.data('id');
            for ( var i = 0; i< markers.length; i++) {
                if (id === markers[i].ID) {
                    markers[i].setIcon(iwj_search_map.marker_icon_hover);
                    break;
                }
            }
        });
        $( '.section-result' ).on( 'mouseleave', '.grid-content', function(){
            var that = $(this);
            var id = that.data( 'id' );
            for ( var i = 0; i < markers.length; i++ ) {
                if ( id === markers[i].ID ) {
                    markers[i].setIcon( iwj_search_map.marker_icon );
                    break;
                }
           }
        });

        $('.iwj-search-left-side').on('click', '.iwjob-ajax-map-pagination li a', function(e) {
            e.preventDefault();
            $('.iwjob-ajax-map-pagination li').removeClass('active');
            $(this).closest('li').addClass('active');

            var paged = $(this).closest('li').data('paged');

            var pag = $(this).closest('.w-pagination');

            pag.find('input[name="page_number"]').val(paged);

            ajaxfilter(paged);

        });

        $('.btn-pinpoint').on('click',function(){
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    $('#iwj_curent_lat').val(position.coords.latitude);
                    $('#iwj_curent_lng').val(position.coords.longitude);
                    // infoWindow.setPosition(pos);
                    // infoWindow.setContent('Location found.');
                    // infoWindow.open(map);
                    geocoder.geocode({'location': pos}, function(results, status) {
                        if (status === 'OK') {
                            if(results[0]){
                                // reverse_location = results[0].address_components[4].short_name + ', ' + results[0].address_components[5].short_name;
                                $('input#location').val(results[0].formatted_address);
                            }
                        }
                    });
                    map.setCenter(pos);
                    map.setZoom(13);
                }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
            // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
        });

		function ajaxfilter(paged){
            if(!paged){
                paged = 1;
            }
            var spinner;
            var data = $('form.search-map').serialize();
            var target      = $( '#section-filter' );
            current_lat = ( target.find( 'input[name="current_lat"]' ).val() ) ? target.find( 'input[name="current_lat"]' ).val() :  ''
            current_lng = ( target.find( 'input[name="current_lng"]' ).val() ) ? target.find( 'input[name="current_lng"]' ).val() :  '';
            var radius      = $( '.range-radius' ).asRange('get');
            data = 'action=get_locations_data&_ajax_nonce=' + iwj.security + '&' + data + '&current_lat=' + current_lat + '&current_lng=' + current_lng + '&radius=' + radius + '&paged=' + paged;
            var s = document.getElementById('iw_search_map');
            $.ajax({
                type: 'POST',
                dataType:'json',
                url: iwj.ajax_url,
                data: data,
                beforeSend: function(){
                    var spinner = new Spinner( spinner_opts ).spin( s );
                },
                success: function(resp){
                    $( ".spinner" ).delay( 1000 ).queue( function() {
                        $(this).remove();
                    });
                    ajaxSuccess( resp, map );
                }
            });
        }
		function ajaxSuccess(data, map){
            if(data.status == 1 && typeof data.status !== 'undefined'){
                $('.section-result .iwajax-load').html(data.html);
                var result = data.data;

                 if(typeof markerCluster !== 'undefined'){
                     markerCluster.clearMarkers();
                    for ( var i= 0 ; i < markers.length; i++ ) {
                        markers[i].setMap(null);
                        markerCluster.removeMarker( markers[i] );
                    }
                    markers = []; 
                 }

                for( var i = 0; i < result.length; i++ ){
                    marker = new google.maps.Marker({
                        position: result[i].location,
                        icon: iwj_search_map.marker_icon
                    });

                    marker.ID = result[i].ID;
                    
                    open_info( marker, result[i] );
                    marker.setMap(map);
                    markers.push( marker );
                }


                if($('.iwj-grid').length){
                    $('.iwj-grid .job-item').matchHeight({
                        byRow: true,
                        property: 'height',
                        target: null,
                        remove: false
                    });
                    $('.iwj-grid .job-item').data('setmatchHeight', true);
                }

            
                markerCluster = new MarkerClusterer(map, markers,
                    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                AutoCenter();
            } else {
                $('.section-result .iwj-jobs').html(data.html);
                for ( var i= 0 ; i < markers.length; i++ ) {
                    markers[i].setMap(null);
                    markerCluster.removeMarker( markers[i] );
                }
                markers = [];
                $('.section-result .w-pagination').hide();
            }
			
		}// end function ajaxsuccess

        function open_info(marker, result){
            google.maps.event.addListener( marker, 'click',( function( marker ) {
                return function () {
                var contentdata = '';
                var content = '';
                var is_saved = '';
                if (jQuery.inArray(result.ID, saved_list) != '-1'){
					is_saved = 'saved';
				}
                content += '<div class="job-item">';
                content += '<div class="job-title"><a href="'+result.link+'">'+result.title+'</a></div>';
                if( result.company_name != '' ) {
                    content += '<div class="company"><i class="fa fa-suitcase"></i><a href="' + result.company_link + '">' + result.company_name + '</a></div>';
                }
                if(result.salary!= '') {
                    content += '<div class="sallary"><i class="iwj-icon-money"></i>' +result.salary + '</div>';
                }
                if(result.address != '') {
                    content += '<div class="address"><i class="fa fa-map-marker"></i>' + result.address + '</div>';
                }

                content += '<div class="job-type">';
                if(result.link_type != '' && result.color != ''){
                    content += '<a class="type-name" href="'+result.link_type+'" data-color="'+result.color+'" style="color: '+result.color+';">'+result.type_name+'</a>';
                }
                if(result.user_login == '1'){
                    content += '<a href="#" class="iwj-save-job '+is_saved+' '+result.savejobclass+'" data-id="'+result.ID+'" data-in-list="true"><i class="fa fa-heart"></i></a></div>';
                }else{
                    content += '<button class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>';
                }
                content += '</div>';

                content += '</div>';

                contentdata = $(content);
                contentdata.on('click', '.iwj-save-job', function (e) {
                    e.preventDefault();
                    var self = $(this);
                    var id = $(this).data('id');
                    var in_list = $(this).data('in-list');
                    var ori_class = '';
                    if (self.hasClass('saved')) {
                        var data = 'action=iwj_undo_save_job&_ajax_nonce=' + iwj.security + '&id=' + id;
                    } else {
                        var data = 'action=iwj_save_job&_ajax_nonce=' + iwj.security + '&id=' + id;
                    }
                    $.ajax({
                        url       : iwj.ajax_url,
                        type      : 'POST',
                        data      : data,
                        dataType  : 'json',
                        beforeSend: function () {
                            if (in_list) {
                                ori_class = self.find('i').attr('class');
                                self.find('i').attr('class', 'fa fa-spinner fa-spin');
                            } else {
                                iwj_button_loader(self, 'add');
                            }
                        },
                        success   : function (result) {
                            if (result) {
                                iwj_button_loader(self, 'remove');
                                if (result.success == true) {
                                	var index = saved_list.indexOf(id);
                                    if (self.hasClass('saved')) {
                                        self.removeClass('saved');
										if (index > -1){
											saved_list.splice(index, 1);
										}
                                    } else {
                                        self.addClass('saved');
                                        if (index < 1){
											saved_list.push(id);
										}
                                    }

                                    if (!in_list) {
                                        self.html(result.message);
                                    }
                                }
                                if (in_list) {
                                    self.find('i').attr('class', ori_class);
                                }
                            }
                        }
                    });
                });
                contentdata.on('mouseenter', 'a[data-color]', function () {
                    var color = $(this).data('color');
                    if (color) {
                        var ori_background = $(this).css('background-color');
                        $(this).data('ori-background-color', ori_background);
                        $(this).css({'background-color': color});
                    }
                });
                contentdata.on('mouseout', 'a[data-color]', function () {
                    var color = $(this).data('ori-background-color');
                    $(this).css({'background-color': color});
                });
                infowindow.setContent( contentdata.get(0) );
                infowindow.open(map, marker); 
                // map.setCenter(marker.getPosition()); // set center map marker
                }                
            })(marker));
        }

		function AutoCenter() {
			//  Create a new viewpoint bound
			var bounds = new google.maps.LatLngBounds();
			//  Go through each...
			$.each( markers, function (index, marker) {
                bounds.extend( marker.position );
			});

            // Don't zoom in too far on only one marker
            if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
                var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
                var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
                bounds.extend(extendPoint1);
                bounds.extend(extendPoint2);
            }

            //  Fit these bounds to the map
            map.fitBounds(bounds);
        }
       
		initMap();

	})

	
})(jQuery);

jQuery(document).ready(function($){
    $(window).on("load resize", function () {
        if ($('#wpadminbar').length) {
            var h_wpadminbar = $('#wpadminbar').outerHeight()
        }
        var h_header = $('.header.header-default').outerHeight(),
            h_window = window.innerHeight,
            h_wpadminbar_n = 0;
        if (h_wpadminbar) {
            h_wpadminbar_n = h_wpadminbar;
        }
        var h_map = (h_window - h_wpadminbar_n - h_header);
        $('.contents-main-search-map').css({'padding-top': h_header});
        $('.page-search-map .iw_search_map').css({'height': h_map});
    });
});

