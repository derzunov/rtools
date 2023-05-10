<?php
  function getRequestData( $method ) {

    // GET and POST are simple:
    if ( $method === 'GET' ) return $_GET;
    if ( $method === 'POST' ) return $_POST;

    // PUT, DELETE:
    $data = array();
    $exploded = explode( '&', file_get_contents('php://input' ));

    foreach( $exploded as $pair ) {
        $item = explode( '=', $pair );
        if ( count( $item ) == 2 ) {
            $data[ urldecode( $item[ 0 ] ) ] = urldecode( $item[ 1 ] );
        }
    }
    return $data;
  }
?>
