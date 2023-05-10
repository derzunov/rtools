<?php
  include_once 'db/db.php';
  include_once 'utils/index.php';

  function getRequestData($method) {

    // GET and POST are simple:
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;

    // PUT, DELETE:
    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));

    foreach($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }
    return $data;
  }

  // GET POST PUT DELETE
  $method = $_SERVER['REQUEST_METHOD'];

  // Request data
  $data = getRequestData($method);

  if ( isset( $data['type'] ) ) {
    $router = $data['type'];
  } else {
    $router = 'price';
  }


  // TODO: Set up structure with possible types and check it up before call route functions
  include_once 'routers/' . $router . '.php';

  route($method, $data);
?>
