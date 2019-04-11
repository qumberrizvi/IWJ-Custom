/**
 * Created by VodKa on 9/19/2017.
 */
(function ($) {
    "use strict";
// When the window has finished loading create our google map below
    $.fn.iwjSimpleMap = function (getdata) {
        $(this).each(function () {
            var self = $(this),
                    js_array_map = iwj_findjob_map.js_array_map,
                    icon = iwj_findjob_map.marker_icon,
                    lat = parseFloat(iwj_findjob_map.latitude),
                    long = parseFloat(iwj_findjob_map.longitude),
                    path_image_google = iwj_findjob_map.path_image_google,
                    check_search = iwj_findjob_map.check_search;
            var loc = new google.maps.LatLng(lat, long);
            var mapOptions = {
                scrollwheel: false,
                //disableDoubleClickZoom: true,
                draggable: true,
                // How zoomed in you want the map to start at (always required)
                zoom: parseInt(iwj_findjob_map.zoom),
                // The latitude and longitude to center the map (always required)
                center: loc,
                // How you would like to style the map.
                // This is where you would paste any style found on Snazzy Maps.
                styles:
                        (iwj_findjob_map.map_styles ? JSON.parse(iwj_findjob_map.map_styles) : [
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
            if (iwj_findjob_map.auto_center === 'yes') {
                //set mutil location to center map
                var bounds = new google.maps.LatLngBounds();
            }
            //end
            //set mutil location to marker cluster
            var markers = [];
            var saved_list = [];
            //console.log(path_image_google);
            if (getdata) { // if have get data in ajax
                js_array_map = getdata;
                check_search = '1';
            }
            var infobox = new InfoBubble({
                //map: map,
                content: ' ',
                // position: new google.maps.LatLng(-32.0, 149.0),
                shadowStyle: 1,
                maxWidth: 280,
                minWidth: 260,
                minHeight: 230,
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
                closeSrc: iwj_findjob_map.close_icon
            });
            //end
            for (i = 0; i < js_array_map.length; i++) {
                var infowindow = new google.maps.InfoWindow({maxWidth: 250});
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(js_array_map[i].lat, js_array_map[i].lng),
                    map: map,
                    icon: icon,
                });
                if (iwj_findjob_map.auto_center === 'yes') {
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
                        var is_saved = '';
                        if (jQuery.inArray(js_array_map[i].id, saved_list) != '-1') {
                            is_saved = 'saved ';
                        }
                        content += '<div class="job-item">';
                        content += '<div class="job-title"><a href="' + js_array_map[i].link + '">' + js_array_map[i].title + '</a></div>';
                        if (js_array_map[i].company_name != '') {
                            content += '<div class="company"><i class="fa fa-suitcase"></i><a href="' + js_array_map[i].company_link + '">' + js_array_map[i].company_name + '</a></div>';
                        }
                        if (js_array_map[i].salary != '') {
                            content += '<div class="sallary"><i class="iwj-icon-money"></i>' + js_array_map[i].salary + '</div>';
                        }
                        if (js_array_map[i].location != '') {
                            content += '<div class="address"><i class="fa fa-map-marker"></i>' + js_array_map[i].location + '</div>';
                        }

                        content += '<div class="job-type">';
                        if (js_array_map[i].link_type != '' && js_array_map[i].color != '') {
                            content += '<a class="type-name" href="' + js_array_map[i].link_type + '" data-color="' + js_array_map[i].color + '" style="color: ' + js_array_map[i].color + ';">' + js_array_map[i].type + '</a>';
                        }

                        if (js_array_map[i].check_login === '1') {
                            content += '<a href="#" class="iwj-save-job ' + is_saved + js_array_map[i].savejobclass + '" data-id="' + js_array_map[i].id + '" data-in-list="true"><i class="fa fa-heart"></i></a></div>';
                        } else {
                            content += '<button class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>';
                        }
                        content += '</div>';
                        content += '</div>';
                        contentdata = $(content);
                        // add function save job & change color
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
                                url: iwj.ajax_url,
                                type: 'POST',
                                data: data,
                                dataType: 'json',
                                beforeSend: function () {
                                    if (in_list) {
                                        ori_class = self.find('i').attr('class');
                                        self.find('i').attr('class', 'fa fa-spinner fa-spin');
                                    } else {
                                        iwj_button_loader(self, 'add');
                                    }
                                },
                                success: function (result) {
                                    if (result) {
                                        iwj_button_loader(self, 'remove');
                                        if (result.success == true) {
                                            var index = saved_list.indexOf(id);
                                            if (self.hasClass('saved')) {
                                                self.removeClass('saved');
                                                if (index > -1) {
                                                    saved_list.splice(index, 1);
                                                }
                                            } else {
                                                self.addClass('saved');
                                                if (index < 1) {
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
                        // end function save job & change color
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
            if (iwj_findjob_map.auto_center === 'yes') {
                //set mutil location to center map
                map.fitBounds(bounds);
            }
        });
    };

    $.fn.iwjSimpleMapNone = function (lat, long) {
        $(this).each(function () {
            var latdata = lat;
            var lngdata = long;
            var self = $(this),
                    js_array_map = '',
                    icon = iwj_findjob_map.marker_icon;
            var loc = new google.maps.LatLng(latdata, lngdata);
            var mapOptions = {
                scrollwheel: false,
                //disableDoubleClickZoom: true,
                draggable: true,
                // How zoomed in you want the map to start at (always required)
                zoom: parseInt(iwj_findjob_map.zoom),
                // The latitude and longitude to center the map (always required)
                center: loc,
                // How you would like to style the map.
                // This is where you would paste any style found on Snazzy Maps.
                styles:
                        (iwj_findjob_map.map_styles ? JSON.parse(iwj_findjob_map.map_styles) : [
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

            for (i = 0; i < js_array_map.length; i++) {
                var infowindow = new google.maps.InfoWindow({maxWidth: 250});

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(js_array_map[i].lat, js_array_map[i].lng),
                    map: map,
                    icon: icon,
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var contentdata = '';
                        var content = '';

                        content += '<div class="job-item">';
                        content += '<div class="job-title"><a href="' + js_array_map[i].link + '">' + js_array_map[i].title + '</a></div>';
                        content += '<div class="sallary"><i class="iwj-icon-money"></i>' + js_array_map[i].salary + '</div>';
                        content += '<div class="address"><i class="fa fa-map-marker"></i>' + js_array_map[i].location + '</div>';
                        if (js_array_map[i].link_type != '' && js_array_map[i].color != '') {
                            content += '<div class="job-type part-time"><a class="type-name" href="' + js_array_map[i].link_type + '" data-color="' + js_array_map[i].color + '" style="color: ' + js_array_map[i].color + ';">' + js_array_map[i].type + '</a>';
                        }

                        if (js_array_map[i].check_login === '1') {
                            content += '<a href="#" class="iwj-save-job ' + js_array_map[i].savejobclass + '" data-id="' + js_array_map[i].id + '" data-in-list="true"><i class="fa fa-heart"></i></a></div>';
                        } else {
                            content += '<button class="save-job iwj-save-job" data-toggle="modal" data-target="#iwj-login-popup"><i class="fa fa-heart"></i></button>';
                        }
                        content += '</div>';

                        contentdata = content;
                        infowindow.setContent(contentdata);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }

        });
    };

    $(window).load(function () {
        $(".map-contain-find-job").iwjSimpleMap();
    });

    $('.keywords-trending').click(function (e) {
        e.preventDefault();
        var data = '';
        var self = $(this);
        var keywords = self.data('keywords');
        var opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 14 // The line thickness
            , radius: 42 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
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
        var target = document.getElementById('spin-map');
        var data = 'action=iwj_findjob_map&_ajax_nonce=' + iwj.security + '&keyword=' + keywords;
        $.ajax({
            url: iwj.ajax_url,
            type: 'GET',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                var spinner = new Spinner(opts).spin(target);

            },
            success: function (result) {
                if (result) {
                    $(".map-contain-find-job").iwjSimpleMap(result);
                }
                $(".spinner").delay(2000).queue(function () {
                    $(this).remove();
                });
            }
        });

    });


    $('.iwj-login-form-maps').submit(function (e) {
        e.preventDefault();
        var self = $(this);
        var button = self.find('.iwj-login-btn');
        var data = self.serialize();
        var opts = {
            lines: 13 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 14 // The line thickness
            , radius: 42 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
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
        var target = document.getElementById('spin-map');
        data = 'action=iwj_findjob_map&_ajax_nonce=' + iwj.security + '&' + data;
        $.ajax({
            url: iwj.ajax_url,
            type: 'GET',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                var spinner = new Spinner(opts).spin(target);

            },
            success: function (result) {
                if (result.none_data != '0') {
                    $(".map-contain-find-job").iwjSimpleMap(result);
                } else {
                    var geocoder, lat, long;
                    geocoder = new google.maps.Geocoder();

                    geocoder.geocode({'address': result.location}, function (results, status) {
                        // and this is function which processes response
                        if (status == google.maps.GeocoderStatus.OK) {
                            lat = results[0].geometry.location.lat();
                            long = results[0].geometry.location.lng();

                            $(".map-contain-find-job").iwjSimpleMapNone(lat, long);
                        } else {
                            alert("Geocode was not successful for the following reason: " + status);
                        }
                    });

                }
                $(".spinner").delay(2000).queue(function () {
                    $(this).remove();
                });
                //var spinner = new Spinner(opts).spin(target).stop();
            }
        });

    });

})(jQuery);

