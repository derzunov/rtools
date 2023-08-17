<?php
require '../vendor/autoload.php'; // see composer doc

// create Smarty instance
$smarty = new Smarty;

$json = file_get_contents( './filters.json' );

$fillters = json_decode(
  $json,
  true,
  16,
  0
);

// НЕ УДАЛЯТЬ! Цикл создания рыбных файлов семантики для фильтров(разделов)
// foreach ( $fillters as $filter ) {
//   foreach( $filter[ 'filters' ] as $subfilter ) {
//     echo $subfilter[ 'furl' ];

//     //$filename = $filter[ 'furl' ] . '-' . $subfilter[ 'furl' ];
//     $filename = $subfilter[ 'furl' ];

//     $fd = fopen( "semantic/" . "$filename.html", 'w' ) or die( "Can\'t create/update $fileName db json" );
//     fwrite( $fd, $subfilter[ 'name' ] . "-" . $subfilter[ 'furl' ] );
//     fclose( $fd );
//   }
// }

$smarty->assign( 'json', $json );
$smarty->assign( 'fillters', $fillters );

$getParametersLength = count( $_GET );

$smarty->assign( 'getParametersLength', $getParametersLength );


// display it
$smarty->display('index.tpl');
