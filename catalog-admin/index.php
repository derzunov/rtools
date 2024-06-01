<?php

// Vars initial
$catalog = 'undefined';
$filter = 'undefined';
$title = 'undefined';
$description = 'undefined';
$h1 = 'undefined';
$subheader = 'undefined';
$comment = 'undefined';
$html = 'undefined';
$temporary = 'undefined';


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

if( isset( $_POST[ 'comment' ] ) ) {
    $comment = $_POST[ 'comment' ];
}

if( isset( $_POST[ 'html' ] ) ) {
    $html = $_POST[ 'html' ];
}

if( isset( $_POST[ 'temporary' ] ) ) {
    $temporary = $_POST[ 'temporary' ];
}

// Meta
$fd = fopen( "$catalog/filters/meta/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/meta/$filter.html" );
fwrite( $fd, "<title>$title</title> <meta name=\"description\" content=\"$description\">" );
fclose( $fd );

// H1 + Subheader HTML
$fd = fopen( "$catalog/filters/main/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/main/$filter.html" );
fwrite( $fd, "<h1 id=\"rc_h1\">$h1</h1> <p id=\"rc_subheader\">$subheader</p>" );
fclose( $fd );

// Semantic HTML
$fd = fopen( "$catalog/filters/semantic/" . "$filter.html", 'w' ) or die( "Can\'t create/update $catalog/filters/semantic/$filter.html" );
fwrite( $fd, "<div id=\"rc_html\">$html</div><br><div>$temporary</div>" );
fclose( $fd );

// JSON
$all = array(
    'catalog' => $catalog,
    'filter' => $filter,
    'title' => $title,
    'description' => $description,
    'h1' => $h1,
    'subheader' => $subheader,
    'comment' => $comment,
    'html' => $html,
    'temporary' => $temporary
);
$fd = fopen( "$catalog/filters/json/" . "$filter.json", 'w' ) or die( "Can\'t create/update $catalog/filters/json/$filter.json" );
fwrite( $fd, json_encode($all) );
fclose( $fd );

// echo "Catalog: $catalog <br> Filter: $filter <br> Title: $title <br> Description: $description <br> H1: $h1 <br> HTML: $html";
?>
