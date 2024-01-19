<?php

    header( 'Access-Control-Allow-Headers: *' );
    header( 'Access-Control-Allow-Origin: * ' );
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT' );

    header( 'Content-Type: application/json; charset=utf-8' );


    // Пишем json-файл
    $fd = fopen( "./tovary/" . $_POST['fileName'] . ".json", 'w' ) or die( "Can\'t create/update json in /tovary/" );
    fwrite( $fd, $_POST['json'] );
    fclose( $fd );

    // Пишем картинку
    $imageData = $_POST[ 'imageData' ];
    $imageData = str_replace( 'data:image/jpeg;base64,', '', $imageData );
    $imageData = str_replace( ' ', '+', $imageData );
    $decodedData = base64_decode( $imageData );
    $fd = fopen( "./tovary/" . $_POST['fileName'] . ".jpg", 'w' ) or die( "Can\'t create/update jpeg in /tovary/" );
    fwrite( $fd, $decodedData );
    fclose( $fd );

    // Отвечаем в формате JSON (Для наглядности в Network->Preview в DevTools)
    // fileName --------------------------
    print_r('"fileName":');

    print_r('"');
    print_r($_POST['fileName']);
    print_r('",');

    // html --------------------------
    print_r('"html":');

    print_r('"');
    print_r($_POST['html']);
    print_r('"');
?>
