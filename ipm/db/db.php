<?php
  class Db {
    private static $instance = null;
    private $allCampaignsDb = [];
    private $variantsDb = [];

    private $dbUrl = __DIR__ .'/index.json';
    private $dbTestUrl = __DIR__ .'/index_test.json';

    private $variantsUrl = __DIR__ .'/variants.json';
    private $variantsTestUrl = __DIR__ .'/variants_test.json';

    private function __construct()
    {
      if ( strpos( $_SERVER[ 'HTTP_HOST' ], 'localhost' ) !== false )
      {
        $this->dbUrl = $this->dbTestUrl;
        $this->variantsUrl = $this->variantsTestUrl;
      }

      $json = file_get_contents( $this->dbUrl );
      $jsonParsed = json_decode(
                  $json,
                  true,
                  16,
                  0
      );
      $this->allCampaignsDb = $jsonParsed;

      $variantsJson = file_get_contents( $this->variantsUrl );
      $variantsJsonParsed = json_decode(
                  $variantsJson,
                  true,
                  16,
                  0
      );
      $this->allCampaignsDb = $jsonParsed;
      $this->variantsDb = $variantsJsonParsed;
    }

    public static function getInstance()
    {
      if (self::$instance == null)
      {
        self::$instance = new Db();
      }

      return self::$instance;
    }

    // All items for requested url
    // ! the url must be passed in params of the request :::: admin client/url -> {ur: 'requested/url'}
    public function getItemsByURL( $url )
    {
      if ( isset($this->allCampaignsDb[ $url ])) {
        return $this->allCampaignsDb[ $url] ;
      } else {
        return [];
      }
    }

    // All urls with all their items
    public function getWholeCampaignsDb()
    {
      return $this->allCampaignsDb;
    }

    // Post a new item
    // Returns: created element or an error
    public function addItemForURL( $url, $data )
    {
      $dataToWrite = $data;

      $dataToWrite[ 'id' ] = time() * 1000; // convert to ms
      if ( isset( $dataToWrite[ 'interacts' ] ) ) {
        $dataToWrite[ 'interacts' ] = intval($dataToWrite[ 'interacts' ], 10);
      }

      if ( isset( $dataToWrite[ 'variant' ] ) ) {
        $dataToWrite[ 'variant' ] = intval($dataToWrite['variant']); // string to int
      }

      unset( $dataToWrite[ 'url' ] ); // we don't need url property inside of news/cart/etc. item

      // TODO: check that $url is set in db. if none, create it
      // Paste the item:
      if (!isset( $this->allCampaignsDb[ $url ] )) {
        $this->allCampaignsDb[ $url ] = [];
      }
      array_unshift( $this->allCampaignsDb[ $url ], $dataToWrite );

      $newAllDbArray = $this->getWholeCampaignsDb();
      $this->saveCampaignsDb( $newAllDbArray );

      return $dataToWrite;
    }

    // Update the item
    // Returns: updated element or an error
    public function updateItemForURL( $url, $itemData )
    {
      $dataToWrite = $itemData;
      $dataToWrite[ 'id' ] = intval($dataToWrite['id']); // Приходит строковым из PUT, исправляем это

      if ( isset( $dataToWrite[ 'interacts' ] ) ) {
        $dataToWrite[ 'interacts' ] = intval($dataToWrite['interacts']); // string to int
      }

      if ( isset( $dataToWrite[ 'variant' ] ) ) {
        $dataToWrite[ 'variant' ] = intval($dataToWrite['variant']); // string to int
      }

      unset( $dataToWrite[ 'url' ] ); // we don't need url property inside of news/cart/etc. item

      $urlItemsArray = $this->allCampaignsDb[ $url ] ;

      foreach ( $urlItemsArray as &$item ) {
        if ( $item[ 'id' ] ==  $dataToWrite[ 'id' ] ) {
          $item = $dataToWrite;
          break;
        }
      }
      unset($item); // рвём ссылку на посл элемент

      $this->allCampaignsDb[$url] = $urlItemsArray;
      $this->saveCampaignsDb( $this->getWholeCampaignsDb() );

      return $dataToWrite;
    }

    // Delete the item
    public function deleteItem( $url, $id )
    {
      $items = $this->getItemsByURL( $url );
      $updatedItemsArray = array_filter( $items, function( $item, $key ) use ( $id ) {
        return ( $item[ 'id' ] != $id );
      }, ARRAY_FILTER_USE_BOTH );

      // array_values переиндексирует все ключи массива без пропусков с 0 до конца,
      // чтобы при кодировании в json мы получили именно массив, а не объект
      $this->allCampaignsDb[ $url ] = array_values( $updatedItemsArray );
      $this->saveCampaignsDb( $this->getWholeCampaignsDb() );

      $responseJsonStr = '{"result": "ok"}';
      $response = json_decode(
        $responseJsonStr,
        true,
        16,
        0
      );
      return $response;
    }

    // Get all urls
    public function getAllUrls()
    {
      return array_keys( $this->allCampaignsDb );
    }

    // TODO: Implement!:
    public function getUrlsByType()
    {
      return array_keys( $this->allCampaignsDb );
    }

    // Get variants list
    public function getVariants()
    {
      return $this->variantsDb;
    }

    // Set new variants list
    public function setVariants( $variantsArray )
    {
      // Maybe more actions with $variantsArray...variantsDb
      // Save
      $this->saveVariantsDb( $variantsArray );
      return $this->variantsDb;
    }

    // Saving changes
    private function saveCampaignsDb( $dbArray ) {
      // Unicode Ru fix & convert to json
      $dbJson = getCorrectRu( json_encode( $dbArray ) );

      $fd = fopen( $this->dbUrl, 'w' ) or die( 'Can\'t update db json' );
      fwrite( $fd, $dbJson );
      fclose( $fd );
    }

    private function saveVariantsDb( $dbArray ) {
      // Unicode Ru fix & convert to json
      $dbJson = getCorrectRu( json_encode( $dbArray ) );

      $fd = fopen( $this->variantsUrl, 'w' ) or die( 'Can\'t update db json' );
      fwrite( $fd, $dbJson );
      fclose( $fd );
    }
  }
?>
