<html>
<head>

<meta name="robots" content="noindex,nofollow" />


{if file_exists("filters/meta/{$smarty.get.f}.html")}
  {include file="filters/meta/{$smarty.get.f}.html"}
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
<div class="tw-container">
  {if file_exists("{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/semantic/{$smarty.get.f}.html") && !isProductCard}
      {include file="{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/semantic/{$smarty.get.f}.html"}
  {/if}
  <div style="display: flex;">
    <!-- Список фильтров -->
    <div style="width: 35%">
      <ul>
        {if !$isProductCard}
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
              <li>No filters found !</li>
          {/foreach}
        {/if}
      </ul>
    </div>
    <div style="width: 65%">
      <br>

      {if $filteredGoods|@count}
      <h5>Результат:</h5>
      <div style="display: flex; justify-content: space-between;">
        {foreach $filteredGoods as $good}
          <div>
            <a href="/catalog/naklejki/?p={$good.name}">
              <img width="250px" src="/tools/catalog-admin/naklejki/tovary/{$good.jpg}">
              <p>{$good.productName}</p>
            </a>
          </div>
        {/foreach}
      </div>

      {else if $isFiltersSet}
        <h5>Нет результатов по выбранным фильтрам</h5>
      {else if !$isProductCard}
        <h5>Выберите фильтр</h5>
      {/if}

      <h5>Вас также может заинтересовать:</h5>

      <div style="display: flex; justify-content: space-between;">
        {foreach $goods as $good}
          <div>
            <a href="/catalog/naklejki/?p={$good.name}">
              <img width="250px" src="/tools/catalog-admin/naklejki/tovary/{$good.jpg}" >
              <p>{$good.productName}</p>
            </a>
          </div>
        {/foreach}
      </div>
      <br>
    </div>
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
      const filters = []

      $filtersChecked.forEach( ( $filter ) => {
        filters.push( $filter.value )
      } )

      getParameters = '?f=' + filters.sort().join( '__' )

      window.location.href = getParameters
    } )
  } )
</script>

</body>
</html>
