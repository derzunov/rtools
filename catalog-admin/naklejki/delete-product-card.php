<?php

$productCardsDir = './tovary/';

$filename = $_GET[ 'fileName' ];

unlink( $productCardsDir . $filename . '.json' );
unlink( $productCardsDir . $filename . '.jpg' );
unlink( $productCardsDir . $filename . '.svg' );
unlink( $productCardsDir . $filename . '.html' );
unlink( $productCardsDir . $filename . '.php' );


header( 'Content-Type: application/json' );
print_r( json_encode( [$filename] ) );
?>
