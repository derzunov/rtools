<?php
  class Db {
    private static $instance = null;
    private $allDb = [];
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

    public function getChangedPricesDb() {
      return $this->changedPricesDb;
    }

    public function getLastUpdateTimestamp() {
      return filectime( $this->dbUrl );
    }

    // Номенклатура из 1с обновилась
    public function updateDb( $newPriceObject ) {

      foreach ( $newPriceObject as $newPriceOneSId => $newPriceString ) {
        // Обновляем все новопришедшие позиции
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
        $FinalPriceListsItem[ 'id' ] = $priceListObject[ 'id' ];
        $FinalPriceListsItem[ 'change_threshold' ] = $priceListObject[ 'change_threshold' ];

        array_push( $allPriceLists, $FinalPriceListsItem );
      }

      $allPriceListsJson = getCorrectRu( json_encode( $allPriceLists ) );

      //chmod( $this->jsonPricelistsDir . $this->allPriceListsIndexUrl, 0777 );
      $fd = fopen( $this->allPriceListsIndexUrl, 'w' ) or die( 'Can\'t update all price lists json' );
      fwrite( $fd, $allPriceListsJson );
      fclose( $fd );

      return $allPriceLists;
    }

    public function getAllPriceLists() {
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
  }
?>
