<?php

	if (strpos($_SERVER["DOCUMENT_ROOT"], "xampp")){
		// локально - ставили через composer
		require_once($_SERVER["DOCUMENT_ROOT"] . '/vendor/cijic/phpmorphy/libs/phpmorphy/src/common.php');
		$dir = $_SERVER["DOCUMENT_ROOT"] . '/vendor/cijic/phpmorphy/libs/phpmorphy/dicts'; // путь к каталогу со словарями
	}
	else {
		// на сервере - отлажено, не трогаем 
		require_once($_SERVER["DOCUMENT_ROOT"] . '/akw/src/common.php');
		$dir = $_SERVER["DOCUMENT_ROOT"] . '/akw/dicts'; // путь к каталогу со словарями
	}	

	// Укажите, для какого языка будем использовать словарь.
	// Язык указывается как ISO3166 код страны и ISO639 код языка, 
	// разделенные символом подчеркивания (ru_RU, uk_UA, en_EN, de_DE и т.п.)

	$lang = 'ru_RU';

	// Укажите опции
	// Список поддерживаемых опций см. ниже
	$opts = array(
		'storage' => PHPMORPHY_STORAGE_FILE
	);

	// создаем экземпляр класса phpMorphy
	// обратите внимание: все функции phpMorphy являются throwable т.е. 
	// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
	try {
    $morphy = new phpMorphy($dir, $lang, $opts);
	    //echo $morphy->getEncoding(); //кодировка словаря
	    //var_dump ($morphy);
	    //http://phpmorphy.sourceforge.net/dokuwiki/manual
    
	}
	catch (phpMorphy_Exception $e) {
	    die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
	}

?>
