<html>
<head>


{if file_exists("meta/{$smarty.get.filters}.html")}
  {include file="meta/{$smarty.get.filters}.html"}
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
<div style="display: flex;">
  <div style="width: 35%">  
    <ul>
        {foreach $fillters as $fillterName}
            <li>{$fillterName.name|escape} ({$fillterName.furl})</li>
            <ul>
              {foreach $fillterName.filters as $filter}
                
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

    {if file_exists("semantic/{$smarty.get.filters}.html")}
      {include file="semantic/{$smarty.get.filters}.html"}
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
      getParameters = '?filters='

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
