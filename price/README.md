# Price
Identification of out-of-stock items

### Input
 - current-price.txt from 1c
### Output
 - db/index.json
 - _GET_ **/tools/price/** -> *text/plain*: 
```
...
Электроды;Электроды;00-00002035;шт;900;2,00;
?Шнурок для бейджа;Шнурок для бейджа;00165;шт;796;20,06;бейдж
...
```
 - "?" - out-of-stock item sign

 - "00-00002035" - 1C Id

