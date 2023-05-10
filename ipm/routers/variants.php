<?php

    function route( $method, $data ) {

        switch ( $method ) {
            case 'GET':
                get( $data );
                break;
            case 'POST':
                post( $data );
                break;
            case 'PUT':
                put( $data );
                break;
            case 'DELETE':
                delete( $data );
                break;
            case 'OPTIONS':
                options( $data );
                break;
        }
    }

    // TODO: Refactor all requests code like that (?url&type&action&[...data]):
    // GET /tools/ipm/?type=variants -> JSON [ "variant 1", "variant 2", ... ]
    function get() {

        $db = Db::getInstance();
        $variants = $db->getVariants();

        sendJsonResponse( $variants );
        return 0;
    }

    function post( $data ) {

        $db = Db::getInstance();
        // dbResponse - created element or error
        $dbResponse = $db->setVariants( $data[ 'variants' ] );
        header( 'Location: /tools/ipm/admin/dist/' );
        return 0;
    }

    // Preflight requests ( headers in sendJsonResponse )
    function options( $data ) {
        $dbResponse = 0;
        sendJsonResponse( $dbResponse );
    }
 ?>
