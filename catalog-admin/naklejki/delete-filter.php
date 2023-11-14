<?php

$jsonFiltersDir = './filters/json/';

$filter = $_GET[ "f" ];

//echo( $jsonFiltersDir . $filter . ".json" );

unlink( $jsonFiltersDir . $filter . ".json" );

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

header( 'Content-Type: application/json' );
print_r( json_encode( $jsonsFiltersNames ) );
?>
