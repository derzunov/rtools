<?php

  // описаны методы, вызов которых возвращает заполненный автосгенеренный  шаблон страницы, которая НЕ описана в админке
  // параметры методов ( каталог продукта, строка фильтров) 
  // методы getXyz() отличаются подключаемыми в них файлами шаблонов, например 01_auto_h1_subheader.html 
  // шаблоны могут быть формата HTML или TXT
  // "заполнятели" шаблонов - /tools/catalog-admin/naklejki/filters/auto.../01_auto....php


  // echo("12313123 semantic templates");

  // Json содержащий все возможные фильтры
  $allFiltersJson = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/filters.json' );
  // Декодируем json в массив php
  $allFilters = json_decode(
    $allFiltersJson,
    true,
    16,
    0
  );


  /*
    Формируем словарь фильтров по принципу:
    
    {
      'bumazhnye': 'Бумажные',
      'plyonka': 'Плёнка',
      'filterKey': 'Filter string value',
      ...
    }

	в filters.json перечислены значения фильтров, которые используются для формирования списка фильтров на экране, а так же для автогенережки
	чтобы автогенережка была более читабельной, для некоторых фильтров с предлогами требуется особая обрабока 
	для этого в фильтре добавляем поле template и в нем перед словами фильтра ставим управляющие символы, примерно как в wordstat.yandex, которые управляют работой semantic_through_phpmorphy.php
   
    {
      "id": "13579",
      "name": "На стекло",
      "template": "![На стекло]",
      "furl": "na-steklo"
    }

  */
  
  $filtersDictionary = [];
  foreach( $allFilters as $filterCategory  ) {
    foreach( $filterCategory[ 'filters' ] as $filter  ) {
		// 
		if ((isset($filter['template'])) ){
			// Если для фильтра в filters.json задано значение template берем его значение. 
			// Например: "template": "слова с управляющими !символами", 
			$filtersDictionary[ $filter[ 'furl' ] ] = $filter[ 'template' ];
		}
		else {
			// Работаем по основной логике фильтров 
			$filtersDictionary[ $filter[ 'furl' ] ] = $filter[ 'name' ];
		}
    }
  }

  // блок методов getXyz для листнгов и карточек //////////
  
  // шаблоны листингов в папке auto-semantic-list-templates 
  
  // list_title 
  $getTitleTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-list-templates/' ;
    $fileName = $filePath. "01_list_title.txt" ; // файл шаблона list_title
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
 
   // list_description 
  $getDescriptionTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-list-templates/' ;
    $fileName = $filePath. "01_list_description.txt" ; // файл шаблона list_description
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
 
  // list_h1_subheader
  $getMainTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-list-templates/' ;
    $fileName = $filePath. "01_list_h1_subheader.html" ; // файл шаблона list_h1_subheader
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
 
  // list_html
  $getSemanticTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {

    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-list-templates/' ;
    $fileName = $filePath. "01_list_html.html" ; // файл шаблона list_html
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };

  
  // шаблоны карточек в папке auto-semantic-card-templates

  // card_title
  $getCardTitleTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-card-templates/' ;
    $fileName = $filePath . "01_card_title.txt" ; // файл шаблона card_title
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
  
  // card_description 
  $getCardMetaDescriptionTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-card-templates/' ;
    $fileName = $filePath . "01_card_description.txt"; // файл шаблона card_description
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
  
  // card_h1

  $getH1Template = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-card-templates/' ;
    $fileName = $filePath . "01_card_h1.txt"; // файл шаблона h1
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
  
  // card_subheader  ?? зачем html?
   $getCardSubheaderTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-card-templates/' ;
    $fileName = $filePath . "01_card_subheader.html"; // файл шаблона card_subheader
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  };
  
     
   // card_html
   $getCardHtmlTemplate = function ( $catalogName, $filtersNames ) use ( $filtersDictionary ) {
    
    $filtersString = '';
    $filtersStringSemicolon = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . mb_strtolower( $filtersDictionary[ $filterName ] );
      $filtersStringSemicolon = $filtersStringSemicolon . ';' . mb_strtolower( $filtersDictionary[ $filterName ] );
    }

    $filePath = $_SERVER["DOCUMENT_ROOT"]  . '/tools/catalog-admin/naklejki/filters/auto-semantic-card-templates/' ;
    $fileName = $filePath . "01_card_html.html"; // файл шаблона card_html
    include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic_through_phpmorphy.php' );

    return $filledTemplate;
  }; 
 
  

?>
