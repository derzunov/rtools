<html>
<head>
<title>Наклейки</title>
</head>
<body>
<div style="display: flex;">
  <div style="width: 35%">
    <ul>
        {foreach $fillters as $fillterName}
            <li>{$fillterName.name|escape} ({$fillterName.furl})</li>
            <ul>
              {foreach $fillterName.filters as $filter}
                <li><a href="?{$fillterName.furl}={$filter.furl}">{$filter.name|escape} ({$filter.furl})</a></li>
              {/foreach}
            </ul>
        {foreachelse}
            <li>No filters found</li>        
        {/foreach}
    </ul>
  </div>
  <div style="width: 65%">
    {foreach $smarty.get as $paramName => $paramValue}
      {include file="semantic/{$paramName}-{$paramValue}.html"}
    {/foreach}
  </div>

</div>

</body>
</html>
