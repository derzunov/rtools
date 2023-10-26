<?php

// Vars initial
$catalog = 'undefined';
$filter = 'undefined';
$title = 'undefined';
$description = 'undefined';
$h1 = 'undefined';
$subheader = 'undefined';
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

if( isset( $_POST[ 'subheader' ] ) ) {
    $subheader = $_POST[ 'subheader' ];
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

// H1 + Subheader HTML
$fd = fopen( "$catalog/filters/main/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/main/$filter.html" );
fwrite( $fd, "<h1>$h1</h1> <h2>$subheader</h2>" );
fclose( $fd );

// Semantic HTML
$fd = fopen( "$catalog/filters/semantic/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/semantic/$filter.html" );
fwrite( $fd, "<h2>$h2</h2> <div>$html</div>" );
fclose( $fd );

// JSON
$all = array(
'catalog' => $catalog,
'filter' => $filter,
'title' => $title,
'description' => $description,
'h1' => $h1,
'subheader' => $subheader,
'h2' => $h2,
'html' => $html
);
$fd = fopen( "$catalog/filters/json/" . "$filter.json", 'w' ) or die( "Can\'t create/update $catalog/filters/json/$filter.json" );
fwrite( $fd, json_encode($all) );
fclose( $fd );

// echo "Catalog: $catalog <br> Filter: $filter <br> Title: $title <br> Description: $description <br> H1: $h1 <br> HTML: $html";
?>
