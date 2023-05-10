<?php

  function setHeaders( $contentType ) {
    header( 'Access-Control-Allow-Headers: *' );
    header( 'Access-Control-Allow-Origin: *, http://localhost:8080/, http://localhost:8080' );
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT' );

    header( "Content-Type: $contentType; charset=utf-8" );
  };

  function sendResponse( $textHtml ) {
    setHeaders( 'text/html' );
    echo( $textHtml );
  }

  function sendTextResponse( $serializedData ) {
    setHeaders( 'text/plain' );

    foreach ( $serializedData as $item ) {
      echo $item, PHP_EOL;
    }
  };

  function sendJsonResponse( $serializedData ) {
    setHeaders( 'application/json' );
    $responseJson = json_encode( $serializedData );
    echo( getCorrectRu( $responseJson ) );
  };

  function sendError( $errorMessage ) {
    setHeaders( 'application/json' );
    if ( !isset( $errorMessage ) ) {
      $errorMessage = 'Unknown error';
    }
    echo ( '{ "result": "error", "message": "' . $errorMessage . '" }' );
  };
?>
