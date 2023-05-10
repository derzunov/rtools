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

    // GET:
    // /tools/ipm/?type=urls&action=all                   // all urls
    // /tools/ipm/?type=urls&action=with-news             // urls that have news only
    // /tools/ipm/?type=urls&action=with-goods            // urls that have goods only
    // /tools/ipm/?type=urls&action=all&populate=true     // urls with nested items (news, goods, etc)

    function get( $data ) {

        $db = Db::getInstance();

        if ( !isset( $data[ 'action' ] ) ) {
            sendError( 'action is not passed' );
            die();
        }

        if ( !isset( $data[ 'populate' ] ) ) {
            $data[ 'populate' ] = false;
        }

        // News GET request actions:
        switch ( $data[ 'action' ] ) {
            case 'all':
                if ( $data[ 'populate' ] ) {
                    sendJsonResponse( $db->getWholeCampaignsDb() );
                    break;
                }
                sendJsonResponse( $db->getAllUrls() );
                break;

            case 'with-news':
                sendJsonResponse( $db->getUrlsByType('news') );
                break;
            default:
                sendError( 'the action is not recognized' );
        }

        return 0;
    }

    function post( $data ) {
        return 0;
    }

    function put( $data ) {
        return 0;
    }

    function delete( $data ) {
        return 0;
    }

    // Preflight requests ( headers in sendJsonResponse )
    function options( $data ) {
        $dbResponse = 0;
        sendJsonResponse( $dbResponse );
    }
 ?>
