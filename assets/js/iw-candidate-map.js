/**
 * Created by VodKa on 9/19/2017.
 */
(function ($) {
    "use strict";
// When the window has finished loading create our google map below
    $.fn.iwjSimpleMap = function (getdata) {
        $(this).each(function () {
            var self = $(this),
                    js_array_map = iwj_candidate_map.js_array_map,
                    icon = iwj_candidate_map.marker_icon,
                    lat = parseFloat(iwj_candidate_map.latitude),
                    long = parseFloat(iwj_candidate_map.longitude),
                    path_image_google = iwj_candidate_map.path_image_google
            var loc = new google.maps.LatLng(lat, long);
            var mapOptions = {
                scrollwheel: false,
                //disableDoubleClickZoom: true,
                draggable: true,
                // How zoomed in you want the map to start at (always required)
                zoom: parseInt(iwj_candidate_map.zoom),
                // The latitude and longitude to center the map (always required)
                center: loc,
                // How you would like to style the map.
                // This is where you would paste any style found on Snazzy Maps.
                styles:
                        (iwj_candidate_map.map_styles ? JSON.parse(iwj_candidate_map.map_styles) : [
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
            };
            var map = new google.maps.Map($(this).find('.map-view').get(0), mapOptions);
            var marker, i;
            if (iwj_candidate_map.auto_center === 'yes') {
                //set mutil location to center map
                var bounds = new google.maps.LatLngBounds();
                //end
            }
            //set mutil location to marker cluster
            var markers = [];
            var infobox = new InfoBubble({
                //map: map,
                content: ' ',
                // position: new google.maps.LatLng(-32.0, 149.0),
                shadowStyle: 1,
                maxWidth: 250,
                minWidth: 250,
                minHeight: 250,
                maxHeight: 900,
                padding: 0,
                backgroundColor: '#fff',
                borderRadius: 5,
                arrowSize: 25,
                borderWidth: 0,
                borderColor: 'transparent',
                disableAutoPan: false,
                hideCloseButton: false,
                arrowPosition: 50,
                backgroundClassName: 'infor-candidate-map',
                arrowStyle: 0,
                closeSrc: iwj_candidate_map.close_icon
            });
            //end
            for (i = 0; i < js_array_map.length; i++) {
                var infowindow = new google.maps.InfoWindow({maxWidth: 250});
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(js_array_map[i].lat, js_array_map[i].lng),
                    map: map,
                    icon: icon
                });
                if (iwj_candidate_map.auto_center === 'yes') {
                    //set mutil location to center map
                    bounds.extend(marker.position);
                    //end
                }
                //set mutil location to marker cluster
                markers.push(marker);
                //end
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var contentdata = '';
                        var content = '';
                        content += '<div class="candidate-item-with-map">';
                        content += '<div class="candidate-bg theme-bg">';
                        content += '</div>';
                        content += '<div class="candidate-info">';
                        if (js_array_map[i].image != '') {
                            content += '<div class="avatar" style="background-image: url(' + js_array_map[i].image + ')"></div>';
                        }
                        content += '<h3 class="title"><a class="theme-color" href="' + js_array_map[i].link + '">' + js_array_map[i].title + '</a></h3>';
                        if (js_array_map[i].headline != '') {
                            content += '<div class="headline">' + js_array_map[i].headline + '</div>';
                        }
                        if (js_array_map[i].last_login_1 != '' || js_array_map[i].last_login_2 != '') {
                            if (js_array_map[i].last_login_1 != '') {
                                content += '<div class="latest-activities"><label>' + js_array_map[i].text_activities + '</label> ' + js_array_map[i].last_login_1 + ' ' + js_array_map[i].text_ago + '</div>';
                            } else {
                                content += '<div class="latest-activities"><label>' + js_array_map[i].text_activities + '</label> ' + js_array_map[i].last_login_2 + '</div>';
                            }
                        } else {
                            content += '<div class="latest-activities"><label>' + js_array_map[i].text_activities + '</label> ' + js_array_map[i].date_registered + '</div>';
                        }
                        content += '</div>';
                        content += '</div>';
                        contentdata = $(content);
                        if (infobox) {
                            infobox.close();
                        }
                        infobox.setContent(contentdata.get(0));
                        infobox.open(map, marker);
                    }
                })(marker, i));
            }
            //set mutil location to marker cluster
            var markerCluster = new MarkerClusterer(map, markers, {imagePath: path_image_google});
            //end set mutil location to marker cluster
            if (iwj_candidate_map.auto_center === 'yes') {
                //set mutil location to center map
                map.fitBounds(bounds);
            }
        });
    };

    $(window).load(function () {
        $(".iwj-candidate-with-map").iwjSimpleMap();
    });

})(jQuery);

