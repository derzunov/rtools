<?php
  include_once 'db/db.php';
  include_once 'utils/index.php';
  include_once 'service/index.php';

  // GET POST PUT DELETE
  $method = $_SERVER['REQUEST_METHOD'];

  // Request data
  $data = getRequestData($method);

  // STRUCTURE: ========================================================
  // url - Start point for db search
      // Должен быть передан в явном виде, а не автоматом вычитан из запроса,
      // тк, админу с любого урла может понадобиться список для любого URL
  // type: news, cart, greetings, redirect (with timer for example), etc.
  // id (timestamp when was created)
  // title
  // description
  // link (for redirect for example)
  // img
  // date start
  // date end
    // Use only start date if no end date is set
  // ~ Dat-file

  // Service information: ==============================================
  // ?page ( for Admin page pagination )
  // ?limit ( for Admin page pagination - items count on page )
  // ?all (0 or 1)
  // etc. .......

  if ( isset( $data['type'] ) ) {
    $router = $data['type'];
  } else {
    $router = 'news';
  }


  // TODO: Set up structure with possible types and check it up before call route functions
  include_once 'routers/' . $router . '.php';

  route($method, $data);
?>
