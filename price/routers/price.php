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
                case 'subgroups':
                    sendJsonResponse( $db->getSubgroupsDb() );
                    break;
                case 'changed':
                    sendJsonResponse( $db->getСhangedPricesDb() );
                    break;
                case 'all':
                    sendJsonResponse( $db->getAllPriceLists() );
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


                $_ONE_S_PRICE_FILE_NAME = './db/ones-txt/current-price.txt';

                $dbLastUpdateTimestamp = $db->getLastUpdateTimestamp();
                $oneSFileLastUpdateTimestamp = filectime( $_ONE_S_PRICE_FILE_NAME );

                // Если файл из 1с был обновлен позднее нашей БД, значит нужно обновить базу
                if ( $oneSFileLastUpdateTimestamp > $dbLastUpdateTimestamp ) {
                    $oneSPriceContent = file_get_contents( $_ONE_S_PRICE_FILE_NAME );

                    // Собираем массив из каждой строки файла из 1с
                    $oneSPiceArray = explode( PHP_EOL, $oneSPriceContent );
                    $oneSPriceObject = [];

                    // Собираем объект, где ключом будет Id 1c (третий кусок строки через ';'), а значением сама строка
                    foreach ( $oneSPiceArray as $oneSPriceItem ) {

                        if ( isset( explode( ';', $oneSPriceItem )[ 2 ] ) ) {
                            $oneSId = explode( ';', $oneSPriceItem )[ 2 ];
                            $oneSPriceObject[ $oneSId ] = $oneSPriceItem;
                        }
                    }

                    $db->updateDb( $oneSPriceObject );
                }

                // ------------------------------------------
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
    */
    function delete( $data ) {
        $db = Db::getInstance();

        if ( isset( $data[ 'file_name' ] ) ) {
            $db->deletePriceByName( $data[ 'file_name' ] );
        }
    }

    // Preflight requests (headers in sendJsonResponse)
    function options( $data ) {
        $dbResponse = [];
        sendJsonResponse( $dbResponse );
    }

    // ------------------------------------------------------------------------
 ?>
