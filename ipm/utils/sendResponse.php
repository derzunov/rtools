<?php

  function setHeaders() {
    header( 'Access-Control-Allow-Headers: *' );
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT' );

    header( 'Content-Type: application/json; charset=utf-8' );
  };

  function sendJsonResponse( $serializedData ) {
    setHeaders();
    $responseJson = json_encode( $serializedData );
    echo( getCorrectRu( $responseJson ) );
  };

  function sendError( $errorMessage ) {
    setHeaders();
    if ( !isset( $errorMessage ) ) {
      $errorMessage = 'Unknown error';
    }
    echo ( '{ "result": "error", "message": "' . $errorMessage . '" }' );
  };
?>
