<?php

// Vars initial
$catalog = 'undefined';
$filter = 'undefined';
$title = 'undefined';
$description = 'undefined';
$h1 = 'undefined';
$h2 = 'undefined';
$html = 'undefined';


// Fill vars from $_POST
if( isset( $_POST[ 'catalog' ] ) ) {
    $catalog = $_POST[ 'catalog' ];
}

if( isset( $_POST[ 'filter' ] ) ) {
    $filter = $_POST[ 'filter' ];
}

if( isset( $_POST[ 'title' ] ) ) {
    $title = $_POST[ 'title' ];
}

if( isset( $_POST[ 'description' ] ) ) {
    $description = $_POST[ 'description' ];
}

if( isset( $_POST[ 'h1' ] ) ) {
    $h1 = $_POST[ 'h1' ];
}

if( isset( $_POST[ 'h2' ] ) ) {
    $h2 = $_POST[ 'h2' ];
}

if( isset( $_POST[ 'html' ] ) ) {
    $html = $_POST[ 'html' ];
}

// Meta
$fd = fopen( "$catalog/filters/meta/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/meta/$filter.html" );
fwrite( $fd, "<title>$title</title> <meta name=\"description\" content=\"$description\">" );
fclose( $fd );

// Semantic HTML
$fd = fopen( "$catalog/filters/semantic/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/semantic/$filter.html" );
fwrite( $fd, "<h1>$h1</h1> <h2>$h2</h2> <div>$html</div>" );
fclose( $fd );


// echo "Catalog: $catalog <br> Filter: $filter <br> Title: $title <br> Description: $description <br> H1: $h1 <br> HTML: $html";
?>
