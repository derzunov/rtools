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
    // /tools/ipm/?url=/YOUR_URL/&type=news&action=actual // actual news list by url
    // /tools/ipm/?url=/YOUR_URL/&type=news&action=all    // all news list by url
    function get( $data ) {

        $db = Db::getInstance();

        if ( !isset( $data[ 'url' ] ) ) {
            sendError( 'url is not passed' );
            die();
        }

        if ( !isset( $data[ 'action' ] ) ) {
            sendError( 'action is not passed' );
            die();
        }

        $currentUrlItems = $db->getItemsByURL( $data['url'] );

        // Filtering by type - get news only
        $currentUrlNews = array_filter( $currentUrlItems, function($item, $key) {
            return ($item[ 'type' ] == 'news');
        }, ARRAY_FILTER_USE_BOTH );

        // News GET request actions:
        switch ( $data[ 'action' ] ) {
            case 'actual':
                sendJsonResponse( getActualItems( $currentUrlNews ) );
                break;
            case 'all':
                sendJsonResponse( $currentUrlNews );
                break;
            default:
                sendError( 'the action is not recognized' );
        }

        return 0;
    }

    function post( $data ) {

        $db = Db::getInstance();
        // dbResponse - created element or error
        $dbResponse = $db->addItemForURL( $data['url'], $data );

        sendJsonResponse( $dbResponse );
        return 0;
    }

    function put( $data ) {
        $db = Db::getInstance();
        // dbResponse - updated element or error
        $dbResponse = $db->updateItemForURL( $data['url'], $data );

        sendJsonResponse( $dbResponse );
    }

    /*
    params:
        url
        id
    */
    function delete( $data ) {
        $db = Db::getInstance();

        // dbResponse - { result: "ok" } or { result: "error" }
        $dbResponse = $db->deleteItem( $data['url'], $data['id'] );
        sendJsonResponse( $dbResponse );
    }

    // Preflight requests ( headers in sendJsonResponse )
    function options( $data ) {
        $dbResponse = 0;
        sendJsonResponse( $dbResponse );
    }
 ?>
