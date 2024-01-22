<?

    $DebugAndTest = 0;

    $fio="Халтурина";

    $ProductName        = 'Наклейки';    // Название продукта = ключевая фраза, продвигаемая на странице (для SEO + og:title) !!!
    $ProductBrand       = 'РЕСПУБЛИКА ЦВЕТА Киров'; // БРЕНД пишем в конце строки !!

    $PageTitle          = 'Наклейки. Купить наклейки самоклеящиеся в Кирове. Заказ наклеек.';

    $PageH1             = '111 Наклейки';  // текст в H1 и Description должен кодержать ключевую фразу = ProductName (PageTitle)

    $PageDescription    = '111 Наклейки самоклеящиеся в Кирове. Заказ изготовления наклеек из бумаги, пленки в РЕСПУБЛИКЕ ЦВЕТА по низкой цене, скидки.';


    $firstWinButton     = 'Заказать наклейки';
    $firstWinTitle      = 'Заказать наклейки';
    $firstImage         = '';

    $lastH2             = 'Заказать наклейки в Кирове';
    $lastGrey           = '';
    $lastWinButton      = 'Оставить заявку';
    $lastWinTitle       = 'Заявка на наклейки';
    $lastImage          = '';
    // цена продукта - показывается в снипете
    $fixPriceProduct    = '';  // фиксированная цена или ниже диапазон цен (минимальная цена указывается только в паре с максимальной)
    $lowPriceProduct    = '1';
    $highPriceProduct   = '1000';


    // DE -----------------------------------------------------------------------------------------------------

    // Подгружаем верную инфу из json исходя из GET-параметра ?f=blabla
    if( isset( $_GET['f'] ) && file_exists( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/json/' . $_GET['f'] . '.json' ) ) {
        $json = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/json/' . $_GET['f'] . '.json' );
    } else {
        $json = file_get_contents( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/json/.json' );
    }


    $rootInfo = json_decode(
        $json,
        true,
        16,
        0
    );

    $PageH1  = $rootInfo[ 'h1' ];
    $PageDescription = $rootInfo[ 'subheader' ];
    $PageTitle = $rootInfo[ 'title' ];

    // Прописывать пустоту в переменные $PageH1 и $PageDescription, если ?f или ?p не пусты или ?clear == true, значит мы находимся не в корне каталога,
    // или вернулись в корень после сброса филтров или из карточки товаров,
    // Значиит красную полосу не надо показывать

    // При пустых $PageH1 и $PageDescription Красная полоса не отображается
    if( ( $_GET['f'] != NULL ) || ( $_GET['p'] != NULL ) || ( $_GET['clear'] == 'true' ) ) {
        $PageH1  = NULL;
        $PageDescription = NULL;
    }

    //------------------------------------------------------------------------------------

    // порядок показа блоков задается порядком в массиве fileList с именами файлов блоков
    if (empty($fileList)) {
        $fileList       = array(
                         "-type",     // категории ссылки
                         "-SYMPTOMS",     // Симптомы позволяют существовать текущей ситуации
                         "-CAUSES",       // Причины вызывают текущую ситуацию и поддерживают симптомы.
                         "-OUTCOMES",     // Результаты — конкретные цели или желаемые состояния, которые должны прийти на смену симптомам.
                         "-EFFECTS",      // Эффекты долговременные следствия достижения конкретного результата.
                         "-RESOURCES",    // Ресурсы устраняют причины, контекст существования симптома, поддерживают существование желаемого результата.


                         "filters",      // фильтры
                         "-price",        // цена
                         "-gallery",      // галлерея
                         "-video",        // видео


                         "-case",         // кейс
                         "-product",      // продукт
                         "-attributes",   // свойства продукта

                         "-technology",   // технология производства продукта
                         "-brief",        // бриф
                         "-steps",        // этапы производства продукта
                         "-review",       // Отзывы.
                         "-kv",           // критерии выбора
                         "-faq",          // ответы на возражения
                         "-profit",       // выгоды
                         "-additions",    // дополнительные продукты

                         "-s_business",  // для бизнеса

                         "-LINKS",       // ссылки на подчиненные страницы



                         // мобильная версия

                         "+price",        // цена
                         "+gallery",      // галлерея

      );
    }

    require($_SERVER["DOCUMENT_ROOT"]."/product.php"); // тело страницы с микроразметкой
?>
