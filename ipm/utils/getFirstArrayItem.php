<?php 
  function getFirstArrayItem($array, $default = null)
  {
    foreach ($array as $item) {
        return $item;
    }
    return $default;
  }
?>