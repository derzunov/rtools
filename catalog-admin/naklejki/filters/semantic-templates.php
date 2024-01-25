<?php
  // returns HTML - H1#rc_h1 and Subheader - p#rc_subheader
  function getMainTemplate( $catalogName, $filtersNames ) {
    $filtersString = '';
    foreach( $filtersNames as $filterName  ) {
      $filtersString = $filtersString . ' ' . $filterName;
    }
    return "<h1 id='rc_h1'>$catalogName $filtersString</h1><p id='rc_subheader'>$filtersString $catalogName - обзор видов, способов применения и популярных трендов</p>";
  };

  // TODO: Immplement
  function getSemanticTemplate( $catalogName, $filters ) {
    return "";
  };

  // TODO: Immplement
  function getMetaTemplate( $catalogName, $filters ) {
    return "";
  };
?>
