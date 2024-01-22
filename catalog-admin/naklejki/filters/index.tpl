<html>
<head>

<meta name="robots" content="noindex,nofollow" />

{* Meta подгружается в /catalog/naklejki/index.php - см под комментарием DE *}

</head>
<body>
<style>
  ul, li {
      list-style-type: none;
      margin-left: 0;
  }

  .filters {
      user-select: none;
      width: 240px;
      background-color: rgb(243 244 246);
      padding: 10px;
      border-radius: 5px;
  }

  .filters__item {
    position: relative;
  }
  .filters__item-tooltip {
    display: inline-block;
    position: absolute;
    height: 48px;
    width: 100px;
    top: -12px;
    left: 190px;
    background-image: url("/tools/catalog-admin/naklejki/filters/assets/srv_show.svg");
    cursor: pointer;
  }

  .filters__category {
      cursor: pointer;
  }
  .filters__category-content {
      padding-left: 5px;
  }
  .category-arrow {
      display: inline-block;
      margin-bottom: -7px;
      transition: all 0.2s ease-out;
  }
  .filters__category_opened .category-arrow {
      transform:rotate(180deg);
  }

  .g-hidden {
      display: none;
  }

</style>
<div class="tw-container">


{if $isClear}
    {if file_exists("{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/main/{$smarty.get.f}.html")}
      {include file="{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/main/{$smarty.get.f}.html"}
    {/if}
{elseif $isFiltersSet}
    {if file_exists("{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/main/{$smarty.get.f}.html")}
      {include file="{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/main/{$smarty.get.f}.html"}
    {/if}
{/if}

  <div style="display: flex;">
    <!-- Список фильтров -->
    <div style="width: 35%">
      <ul class="filters">
        {if !$isProductCard}
        <p>
            <a href="/catalog/naklejki/?p=new">Новая карточка</a>
        </p>

        <a href="/catalog/naklejki/?clear=true">Сбросить фильтры</a>
          {foreach $filters as $filterName}
              <li class="filters__category filters__category_opened js_filters_category">
                <span class="category-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </span>
                <b>{$filterName.name|escape}</b>
              </li>
              <ul class="filters__category-content">
                {foreach $filterName.filters as $filter}
                  <li class="filters__item js_filter">
                    <label>
                      <input style="margin-right: 5px; position: relative; bottom: -1px;" type="checkbox" value="{$filter.furl}">
                      {$filter.name|escape}
                      <span class="filters__item-tooltip js_go_to_filters js_tooltip g-hidden"></span>
                    </label>
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
      {if !$isProductCard}
        {if file_exists("{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/semantic/{$smarty.get.f}.html")}
            {include file="{$DOCUMENT_ROOT}/tools/catalog-admin/naklejki/filters/semantic/{$smarty.get.f}.html"}
        {/if}
      {/if}
    </div>
  </div>
</div>

<script>
  let getParameters = ''

  const $applyFiltersBtns = document.querySelectorAll( '.js_go_to_filters' )
  $applyFiltersBtns.forEach( ( $btn ) => {
    $btn.addEventListener( 'click', ( event ) => {
      event.preventDefault()

      const $filtersChecked = document.querySelectorAll( '.js_filter input:checked' )
      //getParameters = '?f='
      const filters = []

      $filtersChecked.forEach( ( $filter ) => {
        filters.push( $filter.value )
      } )

      if ( filters.length ) {
          getParameters = '?f=' + filters.sort().join( '__' )
          window.location.href = getParameters
      }
    } )
  } )

  //--------------------------------------------------------------------------------------------------------------------

  const $filtersItems = document.querySelectorAll( '.js_filter' )
  const $tooltips = document.querySelectorAll( '.js_tooltip' )
  const $categories = document.querySelectorAll( '.js_filters_category' )

  $filtersItems.forEach( ( $filter ) => {
      $filter.addEventListener( 'click', ( event ) => {
          console.log( event.target )
          console.log( event.target.nextSibling.nextSibling )

          $tooltips.forEach( ( $tooltip ) => {
              $tooltip.classList.add( 'g-hidden' )
          } )
          //
          // Тултип инпута, в который мы ткнули
          event.target.nextSibling.nextSibling.classList.remove('g-hidden')

      } )
  } )

  $categories.forEach( ( $category ) => {
      $category.addEventListener( 'click', ( event ) => {

          // Фильтры этой категории
          const $categoryContent = event.currentTarget.nextSibling.nextSibling

          if ( $categoryContent.classList.contains( 'g-hidden' ) ) {
              $categoryContent.classList.remove( 'g-hidden' )
              event.currentTarget.classList.add( 'filters__category_opened' )
          } else {
              $categoryContent.classList.add( 'g-hidden' )
              event.currentTarget.classList.remove( 'filters__category_opened' )
          }
      } )
  } )

</script>

</body>
</html>
