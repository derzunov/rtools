<html>
<head>
<title>Info</title>
</head>
<body>

<ul>
    {foreach $fillters as $fillter}
        <li>{$fillter.name|escape} ({$fillter.id})</li>
        <ul>
          {foreach $fillter.filters as $fillter}
            <li>{$fillter.name|escape} ({$fillter.id})</li>
          {/foreach}
        </ul>
    {foreachelse}
        <li>No filters found</li>        
    {/foreach}
</ul>

</body>
</html>
