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
      padding: 10px;
  }

  .filters__item {
    position: relative;
    cursor: pointer;
    padding-left: 5px;
  }

  .filters__item:hover {
      background-color: rgb(243 244 246);
  }
  .filters__category-content label {
    cursor: pointer;
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
      margin-bottom: 5px;
  }
  .filters__category-content {

  }

  .category-arrow {
      display: inline-block;
      margin-bottom: -7px;
      margin-left: 2px;
      margin-right: 2px;
  }
  .filters__category_opened .category-arrow {
      transform:rotate(180deg);
  }
  .category-arrow svg {
      width: 19px;
  }

  .g-hidden {
      display: none;
  }

  #rc_subheader {
      font-family: "Open Sans", sans-serif;
      font-size: 18px;
      font-weight: bold;
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
          <img style="height: 24px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_filters.svg" alt="">
          <a style="display: inline-block; position: relative; top: 5px;" target="_blank" href="https://r-color.ru/tools/admin/#/semantic/table">
            <svg style="height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
            </svg>
          </a>
          <a style="display: inline-block; position: relative; top: 5px;" target="_blank"
                 href="/catalog/naklejki/?p=new">
            <svg style="height: 20px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" data-v-ea893728="">
              <path fill="currentColor" d="M352 480h320a32 32 0 1 1 0 64H352a32 32 0 0 1 0-64"></path>
              <path fill="currentColor" d="M480 672V352a32 32 0 1 1 64 0v320a32 32 0 0 1-64 0"></path>
              <path fill="currentColor"
                    d="M512 896a384 384 0 1 0 0-768 384 384 0 0 0 0 768m0 64a448 448 0 1 1 0-896 448 448 0 0 1 0 896"></path>
            </svg>
          </a>
        </p>
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
        <a href="/catalog/naklejki/?clear=true">
          <img style="height: 48px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_remove-filter.svg" alt="">
        </a>
        {/if}
      </ul>
    </div>
    <div style="width: 65%">
<!--      <p style="text-align: right;">-->
<!--        <a href="/catalog/naklejki/?p=new">-->
<!--          <span style="display: inline-block; width: 100px; height: 53px; background-image: url('/tools/catalog-admin/naklejki/filters/assets/srv_card-new.svg');" ></span>-->
<!--        </a>-->
<!--      </p>-->
      <br>

      {if $filteredGoods|@count}
<!--      <span>-->
<!--        <img style="height: 16px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_result.svg" alt="">-->
<!--      </span>-->
<!--      <a href="/catalog/naklejki/?p=new">-->
<!--        <img style="height: 48px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_card-new.svg" alt="">-->
<!--      </a>-->
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
        <p style="margin-bottom: 15px; padding-bottom: 0;">
          <img style="height: 16px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_no-result.svg" alt="">
        </p>
<!--        <a href="/catalog/naklejki/?p=new">-->
<!--          <img style="height: 48px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_card-new.svg" alt="">-->
<!--        </a>-->
      {else if !$isProductCard}
        {if !$isClear}
        <noindex>
          <p>
            Вводный текст, который не индексируется.
            Вводный текст, который не индексируется.
            Вводный текст, который не индексируется.
            Вводный текст, который не индексируется.
          </p>
        </noindex>
        {/if}
<!--        <p>-->
<!--          <a href="/catalog/naklejki/?p=new">-->
<!--            <img style="height: 48px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_card-new.svg" alt="">-->
<!--          </a>-->
<!--        </p>-->
      {/if}

      {* Блок "Вас также может заинтересовать" -------------------- *}
      {if !$isNew}
      <div style="margin-bottom: 15px;">
        <img style="height: 16px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_related.svg" alt="">
      </div>

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
      <span>
        <img style="height: 16px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_not-found.svg" alt="">
      </span>
      <a href="/catalog/naklejki/?p=new">
        <img style="height: 48px;" src="/tools/catalog-admin/naklejki/filters/assets/srv_card-new.svg" alt="">
      </a>
      {/if}
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
