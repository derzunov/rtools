// Позиции из 1с или кастомные позиции
// getFormattedOneSObject('XXX') - Получение объекта 1с c данными из выгрузки

// Создаём объект материалов. Для каждого материала получаем информацию из 1с
// и дополняем её своими полями (наименованием, которое будет использоваться в интерфейсе калькулятора и прочими)

// ??? При добавлении нового материала необходимо указать его имя (ключ) в объекте материалов в файле имен материалов (material-names)

import {
    materialNames,
} from './'

import { getFormattedOneSObject } from './one-s'


const materials = {
    [ materialNames.PVH_10MM_BELYJ ]: {
        ...getFormattedOneSObject( '00-00002155' ),
        title: 'ПВХ белый',
        description: 'Описание для ПВХ 10мм белый',
    },
    [ materialNames.PVH_3MM_CHYORNYJ ]: {
        ...getFormattedOneSObject( '00-00002198' ),
        title: 'ПВХ чёрный',
        description: 'Описание для ПВХ 3мм чёрный',
    },
    [ materialNames.FANERA_BERYOZA_3MM_CHYORNYJ ] : {
        ...getFormattedOneSObject( '00-00002263' ),
        title: 'Фанера из березы',
        description: 'Фанера берёза 3мм',
    },
    [ materialNames.ORGSTEKLO_1_5MM_PROZRACHNYJ ] : {
        ...getFormattedOneSObject( '00-00002258' ),
        title: 'Прозрачное оргстекло 1,5мм',
        description: 'Описание для прозрачного оргстекла 1,5мм',
    },
    [ materialNames.ORGSTEKLO_2MM_PROZRACHNYJ ] : {
        ...getFormattedOneSObject( '00-00002248' ),
        title: 'Прозрачное оргстекло 2мм',
        description: 'Описание для прозрачного оргстекла 2мм',
    },
}

export default materials
