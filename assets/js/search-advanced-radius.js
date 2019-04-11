jQuery(document).ready(function ($) {
    var geocoder;
    var autoComplete;

    $(".iw-job-advanced_search .range-radius").asRange({
        tip: true,
        step: 1,
        value: parseInt(iwj_search_advanced_radius.radius),
        max: parseInt(iwj_search_advanced_radius.max_radius),
        min: parseInt(iwj_search_advanced_radius.min_radius),
    });

    $(".iw-job-advanced_search .range-radius").on('asRange::change', function (e) {
        var value = $(".iw-job-advanced_search .range-radius").asRange('get');
        $(".iw-job-advanced_search input[name='radius']").val(value);
    });


    function init() {
        geocoder = new google.maps.Geocoder();
        autoComplete = new google.maps.places.Autocomplete(
            $('.iw-job-advanced_search input[name="address"]').get(0), {
            types: []
        });

        google.maps.event.addListener(autoComplete, 'place_changed', function () {
            var place = autoComplete.getPlace();
            $(".iw-job-advanced_search input[name='current_lat']").val(place.geometry.location.lat());
            $(".iw-job-advanced_search input[name='current_lng']").val(place.geometry.location.lng());
        });

        $('.iw-job-advanced_search .btn-search').click(function (e) {
            e.preventDefault();
            var value = $(".iw-job-advanced_search input[name='address']").val();
            if(value === ''){
                $(".iw-job-advanced_search input[name='current_lat']").val('');
                $(".iw-job-advanced_search input[name='current_lng']").val('');
            }

            $('.iw-job-advanced_search').find('input').each(function () {
                var value = $(this).val();
                if(value == ''){
                    $(this).attr('disabled', true);
                }
            });

             $('.iw-job-advanced_search').submit();
        });
    }

    $('.iw-job-advanced_search .btn-pinpoint').on('click',function(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                $(".iw-job-advanced_search input[name='current_lat']").val(position.coords.latitude);
                $(".iw-job-advanced_search input[name='current_lng']").val(position.coords.longitude);
                geocoder.geocode({'location': pos}, function(results, status) {
                    if (status === 'OK') {
                        if(results[0]){
                            $(".iw-job-advanced_search input[name='address']").val(results[0].formatted_address);
                        }
                    }
                });
            }, function() {
                console.log("Browser doesn't support Geolocation");
            });
        } else {
            console.log("Browser doesn't support Geolocation");
        }
    });


    init();
});