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

$smarty->assign( 'json', $json );
$smarty->assign( 'fillters', $fillters );


// display it
$smarty->display('index.tpl');
