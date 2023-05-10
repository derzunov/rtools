<?php
  // Actual Item Logic (date ranges, priorities, etc.)
  function getActualItems( $items ) {
    // array_values переиндексирует все ключи массива без пропусков с 0 до конца
    return array_values(array_filter( $items, function( $item ) {
      $currentTime = time();
      return ( $currentTime >= strtotime( $item[ 'date_start' ] ) &&
              $currentTime <= strtotime( $item[ 'date_end' ] ) + 23 * 60 * 60 + 59 * 60 ); // 23:59 of end day
    } ));
  }
?>
