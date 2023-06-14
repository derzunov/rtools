<?php

    function route( $method, $data ) {

        switch ( $method ) {
            case 'GET':
                get( $data );
                break;
            case 'POST':
                post( $data );
                break;
            case 'PUT':
                put( $data );
                break;
            case 'DELETE':
                delete( $data );
                break;
            case 'OPTIONS':
                options( $data );
                break;
        }
    }

    // Rest functions ----------------------------------------------------------------

    function get( $data ) {

        $db = Db::getInstance();


        if ( isset( $data[ 'code' ] ) ) {
            $oneSPriceItem = $db->getPriсeByCode( $data[ 'code' ] );
            if ( isset( explode( ';', $oneSPriceItem )[ 5 ] ) ) {
                $oneSPrice = explode( ';', $oneSPriceItem )[ 5 ];
                sendResponse( $oneSPrice );
                die();
            } else {
                sendResponse( 0 );
                die();
            }
        }

        if ( isset( $data[ 'action' ] ) ) {
            switch ( $data[ 'action' ] ) {
                case 'groups':
                    // TODO: populate=subgroups condition
                    sendJsonResponse( $db->getGroupsDb() );
                    break;
                case 'changed':
                    sendJsonResponse( $db->getChangedPricesDb() );
                    break;
                case 'all':
                    sendJsonResponse( $db->getAllPriceListsIndex() );
                    break;
                case 'position':
                    if ( isset( $data[ 'id' ] ) ) {
                        $oneSPriceItem = $db->getPriсeByCode( $data[ 'id' ] );
                        sendResponse( $oneSPriceItem );
                    } else {
                        sendError( 'position id is not passed' );
                    }
                    break;
                case 'file':
                    if ( isset( $data[ 'name' ] ) ) {
                        sendJsonResponse( $db->getPriceList( $data[ 'name' ] ) );
                    } else {
                        sendError( 'the name of file is not passed' );
                    }
                    break;
                default:
                    sendError( 'the action is not recognized' );
            }
            die();
        }

        // Отправляем текстовый ответ из базы (коды 1с)
        sendTextResponse( $db->getWholeDb() );
    }

    function post( $data ) {

        $db = Db::getInstance();

        if ( isset( $data[ 'file_name' ] ) &&
             isset( $data[ 'pricelist_json' ] ) &&
             isset( $data[ 'pricelist_html' ] ) ) {
                // Работаем с прайс-листами, создаваемыми из админки и встраиваемыми в страницы
                $db->savePriceList( $data[ 'file_name' ], $data[ 'pricelist_json' ], $data[ 'pricelist_html' ] );
                echo 'Price list saved';
        } else if ( !isset( $data[ 'action' ] ) ) {
            // Работаем с выгрузкой из 1с
            $uploadDir = './db/ones-txt/';
            $uploadFileDist = $uploadDir . basename( 'current-price.txt' );

            if ( move_uploaded_file( $_FILES[ 'one_s_price' ][ 'tmp_name' ], $uploadFileDist ) ) {

                // ------------------------------------------

                $_ONE_S_PRICE_FILE_PATH = $uploadDir . basename( 'current-price.txt' );

                $dbLastUpdateTimestamp = $db->getLastUpdateTimestamp();
                $oneSFileLastUpdateTimestamp = filectime( $_ONE_S_PRICE_FILE_PATH );

                // Если файл из 1с был обновлен позднее нашей БД, значит нужно обновить базу
                // TODO: !Нужно добавить люфт в 1 сутки, например ( то есть файл должен быть старше минимум на сутки )
                if ( $oneSFileLastUpdateTimestamp > $dbLastUpdateTimestamp ) {
                    $oneSPriceContent = file_get_contents( $_ONE_S_PRICE_FILE_PATH );

                    // Собираем массив из каждой строки файла из 1с
                    $oneSPiceArray = explode( PHP_EOL, $oneSPriceContent );
                    $oneSPriceObject = [];
                    $oldDb = $db->getWholeDb();

                    // Собираем объект, где ключом будет Id 1c (третий кусок строки через ';'), а значением сама строка
                    foreach ( $oneSPiceArray as $oneSPriceItem ) {

                        if ( isset( explode( ';', $oneSPriceItem )[ 2 ] ) ) {
                            $oneSId = explode( ';', $oneSPriceItem )[ 2 ];
                            $oneSPriceObject[ $oneSId ] = $oneSPriceItem;
                        }
                    }

                    // Проставляем признак отсутствия (знак "?"), для позиций, которых не оказалось в новопришедшем списке
                    foreach ( $oldDb as $oldPriceOneSId => $oldPriceString ) {
                        if ( !isset( $oneSPriceObject[ $oldPriceOneSId ] ) ) {
                            if ( $oldPriceString [ 0 ] != '?' ) {
                                $oneSPriceObject[ $oldPriceOneSId ] = '?' . $oldPriceString;
                            } else {
                                // Мы уже знаем, что товар отсутствовал, дополнительный знак вопроса не ставим
                                $oneSPriceObject[ $oldPriceOneSId ] = $oldPriceString;
                            }
                        }
                    }

                    // ------------------------------------------------------------------------------------
                    // Прикопаем изменения (изменилась цена, добавились новые позиции)

                    $oldPricesObject = $db->getWholeDb();
                    $newPricesObject = $oneSPriceObject;

                    updateChangedPrices( $oldPricesObject, $newPricesObject );

                    // ------------------------------------------------------------------------------------

                    
                    $db->updateDb( $oneSPriceObject );
                }

                // TODO: вернуть: 
                header( 'Location: /tools/admin/' );
                die();
            } else {
                echo 'Ошибка перемещения загруженного файла';
            }
        }

        // В остальных случаях обрабатываем тут экшны ----------------------------------
        // TODO: Навести порядок с роутингом отдельно групп/отдельно 1с/отдельно самих прайсов
        if ( isset( $data[ 'action' ] ) ) {

            switch ( $data[ 'action' ] ) {
                case 'groups' && isset( $data[ 'groups_populated' ] ):
                    $db->saveGroupsDbFromJson( $data[ 'groups_populated' ] );
                    echo 'The groups db sucessfuly updated';
                    break;
                default:
                    sendError( 'the action is not recognized' );
                }
                die();
            }


        return 0;
    }

    function put( $data ) {
        return 0;
    }


    /*
    params in $data:
        file_name
        one_s_changed_code
    */
    function delete( $data ) {
        $db = Db::getInstance();

        if ( isset( $data[ 'file_name' ] ) ) {
            $db->deletePriceByName( $data[ 'file_name' ] );
        }
        
        if ( isset( $data[ 'one_s_changed_code' ] ) ) {
            $db->deleteChangedPriceByCode( $data[ 'one_s_changed_code' ] );
        }
    }

    // Preflight requests (headers in sendJsonResponse)
    function options( $data ) {
        $dbResponse = [];
        sendJsonResponse( $dbResponse );
    }

    // ------------------------------------------------------------------------

    // Private functions: -----------------------------------------------------

    function updateChangedPrices( $oldPricesObject, $newPricesObject ) {
        $db = Db::getInstance();
        $updatedChangedPrices = $db->getChangedPricesDb(); // Сюда только добавляем изменившиеся в цене (или наличии) позиции. 
        // Перезаписываем значения полей ТОЛЬКО при обнаружении изменений

        // ------------------------------------------------------------------------------------
        // Найти позиции с изменившейся ценой
        // Посчитать размер изменившейся цены в процентах
        // Сохранить предыдущую и текущую цены

        // * Проверить, привязанные прайс-листы текущего объекта обновляемой позиции (если он есть) на предмет их текущей актуализации. и если все они числятся актуализированными, а наши прошлое значение текущей цены и значение цены в db
        // Собрать объект позиции: // Положить этот объект в объект базы ( $updatedChangedPrices ). Ключ - код 1с
        // "00-00002244": {
        //     "one_s_code": "00-00002244",
        //     "value": "Бумага citi 115 г/м 1.25x100 (matt);Бумага citi 115 г/м 1.25x100 (matt);00-00002244;м2;055;30,18;\r",
        //     "percents": 25,
        //     "old_price": 111,
        //     "current_price": 222,
        //     "increased": true,
        //     "date": 1684933559122
        //     "
        // }

        foreach ( $newPricesObject as $oneSCode => $newPriceItemString ) {

            if ( isset( explode( ';', $newPriceItemString )[ 2 ] ) ) {

                
                if ( !isset( $oldPricesObject[ $oneSCode ] ) ) {
                    // Товар новый. Добавился в этой номенклатуре. Помечаем.
                    $newPriceItemString = 'NEW: ' . $newPriceItemString;
                    // Что-то потом делаем, а может и не делаем :)
                } else {
                    // Товар уже числится в нашей БД цен (index.json). Работаем дальше
                    
                    $oldPrice = explode( ';', $oldPricesObject[ $oneSCode ] )[ 5 ];
                    $newPrice = explode( ';', $newPriceItemString )[ 5 ];
                    
                    // Цена изменилась
                    // ИЛИ
                    // Товара нет на складе (Признак - знак '?'):
                    // FYI: Мы предварительно подготавливаем новый объект $newPricesObject (до вызова этой функции), 
                    // записывая в него позиции, которые уже имеются в нашей бд, но отсутствуют в загружаемой номенклатуре 
                    // и проставляем знак '?' в начале строки-значения
                    if ( $oldPrice != $newPrice || $newPriceItemString[ 0 ] == '?' ) {

                        $oldDate = 0;

                        if ( !isset( $updatedChangedPrices[ $oneSCode ] ) ) {
                            // В нашей БД изменений нет записи о прошлых изменениях цены этой позиции
                            $updatedChangedPrices[ $oneSCode ] = [];
                            // Значит и старой даты у нас нет и нужно ее получить 
                            // из даты последнего обновления нашей !!!базы цен!!!              
                            $oldDate = $db->getLastUpdateTimestamp() * 1000;
                        } else {

                            // * Следующий шаг, хранить историю изменений, а не только было/стало (но скорее всего не нужно)

                            // В нашей БД изменений уже есть запись о прошлом изменении цены этой позиции
                            // Поэтому старой ценой будет не запись из текущей номенклатуры ( бд цен )
                            // А будет та самая старая цена из уже имеющейся записи в бд изменений
                            $oldPrice = $updatedChangedPrices[ $oneSCode ][ 'old_price' ];
                            // То же самое и со старой датой
                            $oldDate = $updatedChangedPrices[ $oneSCode ] [ 'old_date' ];
                        }

                        $percents = intval( ( $newPrice * 100 / $oldPrice ) - 100, 10 );

                        $updatedChangedPrices[ $oneSCode ] [ 'one_s_code' ] = $oneSCode;
                        $updatedChangedPrices[ $oneSCode ] [ 'value' ] = $newPriceItemString;
                        $updatedChangedPrices[ $oneSCode ] [ 'old_price' ] = $oldPrice;
                        $updatedChangedPrices[ $oneSCode ] [ 'current_price' ] = $newPrice;
                        $updatedChangedPrices[ $oneSCode ] [ 'percents' ] = $percents;
                        $updatedChangedPrices[ $oneSCode ] [ 'increased' ] = $percents > 0 ? true : false;
                        $updatedChangedPrices[ $oneSCode ] [ 'date' ] = time() * 1000;
                        $updatedChangedPrices[ $oneSCode ] [ 'old_date' ] = $oldDate;

                        // TODO: Обнуляем все is_actualized для всех прайс-листов связанных с изменившимся 1с-кодом
                        $db->resetActualizedPricesByCode( $oneSCode );
                    }
                }
            }
        }

        // Сохраняем новую БД изменений
        $db->saveChangedPricesDb( $updatedChangedPrices );
    }
 ?>
