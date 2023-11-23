<?php

$jsonBackupsDir = './backups/';

$backup = $_GET[ "b" ];

unlink( $jsonBackupsDir . $backup . ".json" );

function getJsonsBackupsNames() {

    $jsonBackupsDir = './backups/';

    // Сканируем папку с фильтрами
    $allBackups = scandir( $jsonBackupsDir );
    // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
    // чтобы все элементы были по порядку
    return array_values( array_filter( $allBackups, function( $fileName ) {
        return $fileName != '.' && $fileName != '..';
    } ) );
}
$jsonsBackupsNames = getJsonsBackupsNames();

header( 'Content-Type: application/json' );
print_r( json_encode( $jsonsBackupsNames ) );
?>
