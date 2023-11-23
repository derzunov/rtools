<?php

$jsonFiltersDir = './filters/json/';
$mainFiltersDir = './filters/main/';
$metaFiltersDir = './filters/meta/';
$semanticFiltersDir = './filters/semantic/';

$time = time() * 1000;

$backupsUrl = './backups/' . $time . '.json';


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





function getMainHtmlsFiltersNames() {

    $mainFiltersDir = './filters/main/';

    // Сканируем папку с фильтрами
    $allFiltersFiles = scandir( $mainFiltersDir  );
    // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
    // чтобы все элементы были по порядку
    return array_values( array_filter( $allFiltersFiles, function( $fileName ) {
      return $fileName != '.' && $fileName != '..';
    } ) );
  }
$mainFiltersNames = getMainHtmlsFiltersNames();




function getMetaHtmlsFiltersNames() {

  $metaFiltersDir = './filters/meta/';

  // Сканируем папку с фильтрами
  $allFiltersFiles = scandir( $metaFiltersDir  );
  // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
  // чтобы все элементы были по порядку
  return array_values( array_filter( $allFiltersFiles, function( $fileName ) {
    return $fileName != '.' && $fileName != '..';
  } ) );
}
$metaFiltersNames = getMetaHtmlsFiltersNames();



function getSemanticHtmlsFiltersNames() {

  $semanticFiltersDir = './filters/semantic/';

  // Сканируем папку с фильтрами
  $allFiltersFiles = scandir( $semanticFiltersDir  );
  // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
  // чтобы все элементы были по порядку
  return array_values( array_filter( $allFiltersFiles, function( $fileName ) {
    return $fileName != '.' && $fileName != '..';
  } ) );
}
$semanticFiltersNames = getSemanticHtmlsFiltersNames();





$jsonsArray = array();
$mainHtmlArray = array();
$metaHtmlArray = array();
$semanticHtmlArray = array();

foreach ( $jsonsFiltersNames as $key => $filename ) {
    $jsonsArray[ $filename ] = json_decode( file_get_contents( $jsonFiltersDir . $filename ) );
}

foreach ( $mainFiltersNames as $key => $filename ) {
    $mainHtmlArray[ $filename ] = file_get_contents( $mainFiltersDir . $filename );
}

foreach ( $metaFiltersNames as $key => $filename ) {
    $metaHtmlArray[ $filename ] = file_get_contents( $metaFiltersDir . $filename );
}

foreach ( $semanticFiltersNames as $key => $filename ) {
    $semanticHtmlArray[ $filename ] = file_get_contents( $semanticFiltersDir . $filename );
}

$resultArray = array();
$resultArray[ 'comment' ] = $_GET[ 'comment' ];
$resultArray[ 'date' ] = $time;
$resultArray[ 'json' ] = $jsonsArray;
$resultArray[ 'main' ] = $mainHtmlArray;
$resultArray[ 'meta' ] = $metaHtmlArray;
$resultArray[ 'semantic' ] = $semanticHtmlArray;



$fd = fopen( $backupsUrl, 'w' ) or die( 'Can\'t update backups json' );
fwrite( $fd, json_encode( $resultArray ) );
fclose( $fd );


header( 'Content-Type: application/json' );
print_r( json_encode( $resultArray ) );
?>
