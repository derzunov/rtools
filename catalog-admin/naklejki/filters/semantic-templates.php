<?php
  // Json содержащий все возможные фильтры
  $allFiltersJson = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/filters.json' );
  // Декодируем json в массив php
  $allFilters = json_decode(
    $allFiltersJson,
    true,
    16,
    0
  );

  // Формируем словарь фильтров по принципу:
  /*
  {
    'bumazhnye': 'Бумажные',
    'plyonka': 'Плёнка',
    'filterKey': 'Filter string value',
    ...
  }

  */
  $filtersDictionary = [];
  foreach( $allFilters as $filterCategory  ) {
    foreach( $filterCategory[ 'filters' ] as $filter  ) {
      $filtersDictionary[ $filter[ 'furl' ] ] = $filter[ 'name' ];
    }
  }

  // returns HTML - H1#rc_h1 and Subheader - p#rc_subheader
  $getMainTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    $filtersString = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }
    return "<h1 id='rc_h1'>$catalogName $filtersString</h1><p id='rc_subheader'>Что важно знать. <br> $catalogName $filtersString - обзор видов, способов применения и популярных трендов</p>";
  };

  // TODO: Implement
  $getSemanticTemplate = function ( $catalogName, $filters ) use ( $filtersDictionary ) {
    return "";
  };

  // TODO: Implement
  $getMetaTemplate = function ( $catalogName, $filters ) use ( $filtersDictionary ) {
    return "";
  };
?>
