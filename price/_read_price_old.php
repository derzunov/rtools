<?php
/*
CORS https://developer.mozilla.org/ru/docs/Web/HTTP/CORS
https://ru.stackoverflow.com/questions/1010195/Как-настроит-php-на-cors
*/

if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Headers: Content-Type, text/plain, application/json');
} else {
    // обработка ошибки или уведомление разработчикам
}

// #TODO нужен функционал сохранения цен price.php, даже если номенклатурной позиции сейчас нет в наличии
// в этом случае в наименовании ставится знак вопроса
// можно загружать из 2х файлов. во 2м то, что часто отсутсвует

$content = file_get_contents('price.txt'); // просто читаем файл с прайсом
$content2 = file_get_contents('price-add.txt'); // просто читаем файл с прайсом
echo $content . "\n" . $content2;

?>
