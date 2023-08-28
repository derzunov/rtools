<html>
<head>


{if file_exists("meta/{$smarty.get.f}.html")}
  {include file="meta/{$smarty.get.f}.html"}
{else}
  <title>Наклейки</title>
{/if}

</head>
<body>
<style>
  .filter {}
  .filter__tooltip {
    display: none;
  }
  .filter:hover .filter__tooltip {
    display: inline-block;
  }
</style>
<h1>Наклейки</h1>
<div style="display: flex;">
  <div style="width: 35%">
    <ul>
        {foreach $filters as $filterName}
            <li>{$filterName.name|escape} ({$filterName.furl})</li>
            <ul>
              {foreach $filterName.filters as $filter}

                <li class="filter">
                  <label><input type="checkbox" value="{$filter.furl}">{$filter.name|escape}</label>
                  <div class="filter__tooltip">
                    <a class="js_go_to_filters" href="#" >Показать</a>
                  </div>
                </li>
              {/foreach}
            </ul>
        {foreachelse}
            <li>No filters found</li>
        {/foreach}
    </ul>
  </div>
  <div style="width: 65%">

    <div style="display: flex; justify-content: space-between;">
      {foreach $goods as $good}
        <div>
          <img width="250px" src="goods/{$good.jpg}" >
          <p>{$good.productName}</p>
        </div>
      {/foreach}
    </div>
    <br>
    <div style="display: flex; justify-content: space-between;">
      {foreach $filteredGoods as $good}
        <div>
          <img width="250px" src="goods/{$good.jpg}" >
          <p>{$good.productName}</p>
        </div>
      {/foreach}
    </div>

    {if file_exists("semantic/{$smarty.get.f}.html")}
      {include file="semantic/{$smarty.get.f}.html"}
    {/if}

  </div>

</div>

<script>
  let getParameters = ''

  const $applyFiltersBtns = document.querySelectorAll( '.js_go_to_filters' )
  $applyFiltersBtns.forEach( ( $btn ) => {
    $btn.addEventListener( 'click', ( event ) => {
      event.preventDefault()

      const $filtersChecked = document.querySelectorAll( '.filter input:checked' )
      getParameters = '?f='

      $filtersChecked.forEach( ( $filter ) => {
        getParameters += $filter.value + '|'
      } )
      // Удаляем | на самом конце строки
      getParameters = getParameters.slice( 0, -1 )
      console.log( getParameters )
      window.location.href = getParameters

    } )
  } )

</script>

</body>
</html>
