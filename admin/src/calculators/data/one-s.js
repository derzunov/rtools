import axios from 'axios'
import * as EDINICY_IZM  from '../constants/ed-izm'
import { roundUpNumber } from '../data/utils'

const IS_DEV = window.location.host.includes( 'localhost' ) || window.location.host.includes( '8080' )
const ROOT_HOST = IS_DEV ?
    'http://localhost' : // CORS: Dev-Сервер XAMPP живёт на чистом localhost без порта:8080
    'https://r-color.ru'

// Стягиваем объект бд 1с единожды при загрузке этого модуля
const oneSDbResponse = await axios.get( `${ ROOT_HOST }/tools/price/db/index.json` )
const oneSDb = oneSDbResponse.data

export const getOneSDb = () => {
    return oneSDb
}

export const getPriceByOneSCode = ( oneSCode ) => {
    // TODO: Преобразовывать цены к кв. метрам, пог. метрам, литрам, etc.
    if ( !oneSDb[ oneSCode ] ) return -1
    return oneSDb[ oneSCode ].split(';')[ 5 ].replace( ',', '.' )
}

export const getUnitsByOneSCode = ( oneSCode ) => {
    if ( !oneSDb[ oneSCode ] ) return -1
    return oneSDb[ oneSCode ].split(';')[ 3 ]
}

export const getPositionNameByOneSCode = ( oneSCode ) => {
    if ( !oneSDb[ oneSCode ] ) return 'Позиция отсутствует в 1с!'
    return oneSDb[ oneSCode ].split(';')[ 1 ]
}

// Получение размеров листа материала
export const getMaterialSheetSizesByOneSCode = ( oneSCode ) => {

    const oneSPositionString = getPositionNameByOneSCode( oneSCode ) // например, ПВХ 3050*2050 6мм белый;00-00002142;л;112;2909,70;
    let sizes = oneSPositionString.match(/(\d+)\*(\d+)/)
    if ( !sizes || sizes.length < 3 ) {
        // Не нашли размеры в строке позиции 1с
        // заполним нулями, чтобы было очевидно в калькуляторе, что что-то пошло не так
        // Или у позиции не предусмотрены размеры
        sizes = [ 0, 0, 0 ]
    }
    return { shirinaLista: sizes[ 1 ], vysotaLista: sizes[ 2 ] }
}

export const getFormattedOneSObject = ( oneSCode ) => {
    return _formatMaterialPrice( {
        one_s_title: getPositionNameByOneSCode( oneSCode ),
        one_s_sizes: getMaterialSheetSizesByOneSCode( oneSCode ),
        one_s_units: getUnitsByOneSCode( oneSCode ),
        one_s_price: getPriceByOneSCode( oneSCode ),
        edIzm: '', // Единицы измерения приведённые к стандарту (кв. метры, пог. метры, литры, etc.)
        cena: '',  // цены к кв. метрам, пог. метрам, литрам, etc.
    } )
}

// Private utils -----------------------------------------------------
const _formatMaterialPrice = ( oneSObject ) => {
    oneSObject = { ...oneSObject }
    
    // Какие единицы измерения указаны в 1с
    // Исходя из этого делаем преобразования
    switch ( oneSObject.one_s_units ) {
        
        // Листы нужно привести в кв. метры
        case EDINICY_IZM.LIST:
            oneSObject.edIzm = EDINICY_IZM.M2
            oneSObject.cena = _listyVMetry2(
                oneSObject.one_s_price,
                oneSObject.one_s_sizes.shirinaLista,
                oneSObject.one_s_sizes.vysotaLista
            )
            return oneSObject
        default:
            return oneSObject
    }
}

const _listyVMetry2 = ( cenaLista, shirinaLista, vysotaLista ) => {
    const ploshadListaMm2 = shirinaLista * vysotaLista
    const ploshadListaM2 = ploshadListaMm2 / ( 1000 * 1000 ) // 1000 * 1000 - Квадратный метр в кв. миллиметрах
    return roundUpNumber( cenaLista / ploshadListaM2 )
}
