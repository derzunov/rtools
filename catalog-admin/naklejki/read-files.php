<?php

$jsonFiltersDir = './filters/json/';

function getJsonsFiltersNames() {

    $jsonFiltersDir = './filters/json/';

    // Сканируем папку с фильтрами
    $allPriceListsFiles = scandir( $jsonFiltersDir );
    // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
    // чтобы все элементы были по порядку
    return array_values( array_filter( $allPriceListsFiles, function( $fileName ) {
      return $fileName != '.' && $fileName != '..';
    } ) );
  }
$jsonsFiltersNames = getJsonsFiltersNames();

$jsonsArray = array();

foreach ( $jsonsFiltersNames as $key => $filename ) {
    array_push( $jsonsArray, json_decode( file_get_contents( $jsonFiltersDir . $filename ) ) );
}
header( 'Content-Type: application/json' );
print_r( json_encode( $jsonsArray ) );
?>
