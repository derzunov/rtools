<?php
  class Db {
    private static $instance = null;
    private $allDb = [];
    private $stages = [];
    public $groupsDb = [];
    public $changedPricesDb = [];

    private $dbUrl = __DIR__ .'/index.json';
    private $dbTestUrl = __DIR__ .'/index_test.json';

    private $changedPricesUrl = __DIR__ .'/changed.json';
    private $changedPricesTestUrl = __DIR__ .'/changed_test.json';

    private $groupsUrl = __DIR__ .'/groups.json';
    private $groupsTestUrl = __DIR__ .'/groups_test.json';

    private $jsonPricelistsDir = __DIR__ . "/price-lists/json/";
    private $htmlPricelistsDir = __DIR__ . "/price-lists/html/";

    private $allPriceListsIndexUrl = __DIR__ . "/price-lists/all.json";

    private $stagesUrl = __DIR__ . "/stages.json";

    private function __construct() {
      if ( strpos( $_SERVER[ 'HTTP_HOST' ], 'localhost' ) !== false )
      {
        $this->dbUrl = $this->dbTestUrl;
        $this->groupsUrl = $this->groupsTestUrl;
        $this->changedPricesUrl = $this->changedPricesTestUrl;
      }

      $json = file_get_contents( $this->dbUrl );
      $jsonParsed = json_decode(
                  $json,
                  true,
                  16,
                  0
      );

      $this->allDb = $jsonParsed;

      $stagesJson = file_get_contents( $this->stagesUrl );

      $stagesJsonParsed = json_decode(
                  $stagesJson,
                  true,
                  16,
                  0
      );

      $this->stages = $stagesJsonParsed;

      // Группы
      $groupsJson = file_get_contents( $this->groupsUrl );

      $this->groupsDb = json_decode(
          $groupsJson,
          true,
          16,
          0
      );


      // Позиции с изменившейся ценой из 1с
      $changedPricesJson = file_get_contents( $this->changedPricesUrl );
      $this->changedPricesDb = json_decode(
            $changedPricesJson,
            true,
            16,
            0
      );
    }

    public static function getInstance() {
      if (self::$instance == null)
      {
        self::$instance = new Db();
      }
      return self::$instance;
    }

    // Prices from 1c + old prices
    public function getWholeDb() {
      return $this->allDb;
    }

    public function getGroupsDb() {
      return $this->groupsDb;
    }

    // Все изменившиеся в цене 1с-позиции
    public function getChangedPricesDb() {
      return $this->changedPricesDb;
    }

    // Только те измененившиеся 1с-позиции, которые упомянуты в прайс-листах
    public function getRelatedChangedPrices() {
      $relatedChangedPrices = [];

      foreach( $this->getAllPriceListsIndex() as $priceListObject ) {
        // Если привязанных к прайсу 1с-кодов нет, то идем к следующему прайсу
        if ( !strlen( $priceListObject[ 'one_s_codes' ] ) ) {
          continue;
        }
        // Прикапываем каждый упомянутый в прайсе 1c-код
        foreach ( explode( ';', $priceListObject[ 'one_s_codes' ] ) as $relatedOneSCode ) {
          $relatedOneSCode = trim( $relatedOneSCode );

          // Есть ли вообще такой 1с-код в нашей базе цен 1с
          if ( isset( $this->changedPricesDb[ $relatedOneSCode ] ) ) {
            // Есть, прикапываем
            $relatedChangedPrices[ $relatedOneSCode ] = $this->changedPricesDb[ $relatedOneSCode ];
          }
        }
      }

      return $relatedChangedPrices;
    }

    public function getAllNotActualizedPriceLists() {
      $result = [];
      foreach ( $this->getAllPriceListsIndex() as $price) {
        if ( $price[ 'is_actualized' ] != true ) {
            $result[] = $price;
        }
    }
      return $result;
    }

    // Получение связанных с 1с-кодом неактуализированных прайс-листов
    public function getRelatedNotActualizedPriceListsByOneSCode( $oneSCode ) {

      $relatedNotActualizedPriceLists = [];

      // Бежим по всем прайс-листам
      foreach( $this->getAllPriceListsIndex() as $priceListObject ) {
        // Если привязанных к прайсу 1с-кодов нет, то идем к следующему прайсу
        if ( !strlen( $priceListObject[ 'one_s_codes' ] ) ) {
          continue;
        }
        // Иначе бежим по привязанным к прайс--листу 1с-кодам и ищем наш
        foreach ( explode( ';', $priceListObject[ 'one_s_codes' ] ) as $relatedOneSCode ) {
          $relatedOneSCode = trim( $relatedOneSCode );

          //  Нашли наш код, значит этот прайс лист привязан к нашему коду
          if ( $relatedOneSCode == $oneSCode ) {
            // Проверяем актуализирован ли этот прайс-лист,
            // Если нет, поплняем массив связанных с кодом неактуализированных прайс-листов
            if ( $priceListObject[ 'is_actualized' ] == false ) {
              array_push( $relatedNotActualizedPriceLists, $priceListObject );
            }
          }
        }
      }

      // Возвращаем список связанных с кодом неактуализированных прайс-листов
      return $relatedNotActualizedPriceLists;
    }

    public function getLastUpdateTimestamp() {
      return filectime( $this->dbUrl );
    }

    // Номенклатура из 1с обновилась
    public function updateDb( $newPriceObject ) {

      foreach ( $newPriceObject as $newPriceOneSId => $newPriceString ) {
        // Обновляем все новопришедшие позиции и дописываем к ним время обновления
        $this->allDb[ $newPriceOneSId ] = $newPriceString;
      }

      $this->saveDb( $this->allDb );

      return 0;
    }

    // Saving changes
    private function saveDb( $dbArray ) {
      // Unicode Ru fix & convert to json
      $dbJson = getCorrectRu( json_encode( $dbArray ) );

      $fd = fopen( $this->dbUrl, 'w' ) or die( 'Can not write to main db json' );
      fwrite( $fd, $dbJson );
      fclose( $fd );
    }

    // Get One S price by code
    public function getPriсeByCode( $oneSCode ) {
      $db = $this->getWholeDb();
      if ( !isset( $db[ $oneSCode ] ) ) {
        return null;
      }
      return $db[ $oneSCode ];
    }

    // Saving new/edited price list item
    public function savePriceList( $fileName, $priceListJson, $priceListHtml ) {

      $fd = fopen( $this->jsonPricelistsDir . "$fileName.json", 'w' ) or die( "Can\'t create/update $fileName db json" );
      fwrite( $fd, getCorrectRu( $priceListJson ) );
      fclose( $fd );

      $fd = fopen( $this->htmlPricelistsDir . "$fileName.html", 'w' ) or die( "Can\'t create/update $fileName db html" );
      fwrite( $fd, $priceListHtml );
      fclose( $fd );

      chmod( $this->jsonPricelistsDir . "$fileName.json", 0777 );
      chmod( $this->htmlPricelistsDir . "$fileName.html", 0777 );

      $this->updateAllPricelistsIndex();
    }

    private function getAllPriceListsNames() {
      // Сканируем папку с прайсами
      $allPriceListsFiles = scandir( $this->jsonPricelistsDir );
      // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
      // чтобы все элементы были по порядку
      return array_values( array_filter( $allPriceListsFiles, function( $fileName ) {
        return $fileName != '.' && $fileName != '..';
      } ) );
    }


    public function resetActualizedPricesByCode( $oneSCode ) {

      // Найти все прайс-листы для кода $oneSCode
      // Для каждого обнулить is_actualized
      // После этого обновить индекс

      $allPriceListsFiles = $this->getAllPriceListsNames();

      foreach ( $allPriceListsFiles as $priceFile ) {

        $priceListJson = file_get_contents( $this->jsonPricelistsDir . $priceFile );
        $priceListObject = json_decode(
                    $priceListJson,
                    true,
                    16,
                    0
        );

        // Если прайс привязан к этому коду, то обновляем  is_actualized
        // Содержит ли строка кодов искомый код
        if ( strpos( $priceListObject[ 'one_s_codes' ], $oneSCode ) !== false ) {

          $priceListObject[ 'is_actualized' ] = false;

          $priceListJson = json_encode( $priceListObject );
          $fd = fopen( $this->jsonPricelistsDir . $priceListObject[ 'file_name' ] . ".json", 'w' ) or die( "Can not create/update " . $priceListObject[ 'file_name' ] . " db json" );
          fwrite( $fd, getCorrectRu( $priceListJson ) );
          fclose( $fd );
        }
      }

      $this->updateAllPricelistsIndex();
    }

    // Пробежится по файлам прайслистов, создаст массив с именами и нужной инфой и сохранит в индексный json
    // Предполагается вызывать при каждом создании/измененении/удалении прайса
    // Индексный json будет использоваться для страницы со списком прайсов
    // на странице предполагается сортировка по группам / поиск / удаление
    private function updateAllPricelistsIndex() {
      $allPriceLists = [];
      $allPriceListsFiles = scandir( $this->jsonPricelistsDir );

      // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
      // чтобы все элементы были по порядку
      $allPriceListsFiles = array_values( array_filter( $allPriceListsFiles, function( $fileName ) {
        return $fileName != '.' && $fileName != '..';
      } ) );

      foreach ( $allPriceListsFiles as $file ) {
        $priceListJson = file_get_contents( $this->jsonPricelistsDir . $file );
        $priceListObject = json_decode(
                    $priceListJson,
                    true,
                    16,
                    0
        );

        $FinalPriceListsItem = [];
        $FinalPriceListsItem[ 'file_name' ] = $priceListObject[ 'file_name' ];
        $FinalPriceListsItem[ 'header' ] = $priceListObject[ 'header' ];
        $FinalPriceListsItem[ 'group' ] = $priceListObject[ 'group' ];
        $FinalPriceListsItem[ 'subgroup' ] = $priceListObject[ 'subgroup' ];
        $FinalPriceListsItem[ 'one_s_codes' ] = $priceListObject[ 'one_s_codes' ];
        $FinalPriceListsItem[ 'change_threshold' ] = $priceListObject[ 'change_threshold' ];
        $FinalPriceListsItem[ 'admin_comment' ] = $priceListObject[ 'admin_comment' ];

        $FinalPriceListsItem[ 'is_actualized' ] = $priceListObject[ 'is_actualized' ];
        $FinalPriceListsItem[ 'actualized_date' ] = $priceListObject[ 'actualized_date' ];

        $FinalPriceListsItem[ 'stage' ] = $priceListObject[ 'stage' ];

        array_push( $allPriceLists, $FinalPriceListsItem );
      }

      $allPriceListsJson = getCorrectRu( json_encode( $allPriceLists ) );

      //chmod( $this->jsonPricelistsDir . $this->allPriceListsIndexUrl, 0777 );
      $fd = fopen( $this->allPriceListsIndexUrl, 'w' ) or die( 'Can\'t update all price lists json' );
      fwrite( $fd, $allPriceListsJson );
      fclose( $fd );

      return $allPriceLists;
    }

    public function getAllPriceListsIndex() {
      $allPriceListsJson = file_get_contents( $this->allPriceListsIndexUrl );

      $allPriceLists = json_decode(
                  $allPriceListsJson,
                  true,
                  16,
                  0
      );

      return $allPriceLists;
    }

    public function getPriceList( $name ) {

      $priceListJson = file_get_contents( "$this->jsonPricelistsDir/$name.json" );

      $priceList = json_decode(
                  $priceListJson,
                  true,
                  16,
                  0
      );

      return $priceList;
    }

    public function getStages() {
      return $this->stages;
    }

    public function deletePriceByName( $name ) {

      // Удаляем файлы
      $isJsonDeleted = unlink( "$this->jsonPricelistsDir/$name.json" );
      $isHtmlDeleted = unlink( "$this->htmlPricelistsDir/$name.html" );

      // Обновляем индекс
      $this->updateAllPricelistsIndex();
      return ( $isJsonDeleted && $isHtmlDeleted );
    }

    // Saving changed prices ( 1s codes )
    public function saveChangedPricesDb( $dbArray ) {
      // Unicode Ru fix & convert to json
      $changedPricesJson = getCorrectRu( json_encode( $dbArray ) );

      $fd = fopen( $this->changedPricesUrl, 'w' ) or die( 'Can not write to changed prices db json' );
      fwrite( $fd, $changedPricesJson );
      fclose( $fd );
    }

    public function deleteChangedPriceByCode( $one_s_changed_code ) {

      $changedPricesDb = $this->getChangedPricesDb();
      // Удаляем из базы
      unset( $changedPricesDb[ $one_s_changed_code ] );
      echo( "Success: $one_s_changed_code deleted" );
      $this->saveChangedPricesDb( $changedPricesDb );
    }

    // Groups api

    // Saving changes
    public function saveGroupsDbFromJson( $json ) {
      // Unicode Ru fix & convert to json
      $groupsJson = getCorrectRu( ( $json ) );

      $fd = fopen( $this->groupsUrl, 'w' ) or die( 'Can\'t update db json by groupsUrl' );
      fwrite( $fd, $groupsJson );
      fclose( $fd );
    }

    // get stages
    public function clearChangedPricesDb() {
      return 0;
    // TODO: дописать очистку БД изменившихся позиций
    }
    
    public function getSatages() {
      return 0;
    // TODO: дописать работу со стадиями через наши
    }
  }
?>
