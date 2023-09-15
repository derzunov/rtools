<?php

require './vendor/autoload.php'; // see composer doc

// create Smarty instance
$smarty = new Smarty;

$json = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/filters.json' );

$filters = json_decode(
  $json,
  true,
  16,
  0
);

// НЕ УДАЛЯТЬ! Цикл создания рыбных файлов семантики для фильтров(разделов)
// foreach ( $filters as $filter ) {
//   foreach( $filter[ 'filters' ] as $subfilter ) {
//     echo $subfilter[ 'furl' ];

//     //$filename = $filter[ 'furl' ] . '-' . $subfilter[ 'furl' ];
//     $filename = $subfilter[ 'furl' ];

//     $fd = fopen( "semantic/" . "$filename.html", 'w' ) or die( "Can\'t create/update $fileName db json" );
//     fwrite( $fd, '<b>Семантический</b> <i>HTML</i> для ' . $subfilter[ 'name' ] . "-" . $subfilter[ 'furl' ] );
//     fclose( $fd );
//   }
// }

$smarty->assign( 'json', $json );
$smarty->assign( 'filters', $filters );

$getParametersLength = count( $_GET );

$smarty->assign( 'getParametersLength', $getParametersLength );

// Ищем совпадения по товарам согласно фильтрам

$goods = [];
$filteredGoods = [];



// Собираем все файлы с расширением json из папки
foreach ( glob( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/tovary/*.json' ) as $file ) {
  $object = json_decode( file_get_contents( $file ) );
  $good = [
    'jpg' => $object->jpg,
    'productName' => $object->productName,
    'html' => $object->html,
    'name' => $object->name
  ];
  array_push( $goods, $good );

  // Фильтруем по заданным фильтрам
  foreach( explode( '__', $_GET[ 'f' ] ) as $getFilter ) {
    if ( in_array( $getFilter, $object->filters ) ) {
      array_push( $filteredGoods, $good );
      break; // Чтобы одинаковые позиции при совпадении в запросе по разным фильтрам не повторялись
    }
  }
}

$smarty->assign( 'goods', $goods );
$smarty->assign( 'filteredGoods', $filteredGoods );

// display it
$smarty->display( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/index.tpl' );
