<?php
if ( isset( $locations ) && is_array( $locations ) && $locations) :
    $output= '';
    $locations_request = iwj_get_request_url_location();
    iwj_walk_tax_tree( $locations, 0, $locations_request, $iwj_location_limit, 'employers' );
endif; ?>
