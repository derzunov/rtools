<?php

    function route( $method, $data ) {
        // Just Get have a meaning but let us provide all methods

        switch ( $method ) {
            case 'GET':
                get( $data );
                break;
            case 'POST':
                echo 'Router CART: method POST';
                echo json_encode( $data );
                break;
            case 'PUT':
                echo 'Router CART: method PUT';
                echo json_encode( $data );
                break;
            case 'DELETE':
                echo 'Router CART: method DELETE';
                echo json_encode( $data );
                break;
        }
    }

    function get( $data ) {
        $db = Db::getInstance();
        $currentUrlItems = $db->getItemsByURL( $data['url'] );

        // Filtering by type
        $currentUrlCartItems = array_filter( $currentUrlItems, function($item, $key) {
            return ($item[ 'type' ] == 'cart');
        }, ARRAY_FILTER_USE_BOTH );

        if ( isset( $data['all'] ) && $data['all'] === "1" ) {
            sendJsonResponse( $currentUrlCartItems );
        } else {
            sendJsonResponse( getActualItems($currentUrlCartItems) );
        }
        return 0;
    }
 ?>
