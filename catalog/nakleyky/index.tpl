<html>
<head>
<title>Наклейки</title>
</head>
<body>

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

</body>
</html>
