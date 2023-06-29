import axios from 'axios'
// Стягиваем объект бд 1с единожды при загрузке этого модуля
const oneSdbResponse = await axios.get( 'http://localhost/tools/price/db/index.json' )
const oneSDb = oneSdbResponse.data

const getOneSDb = () => {
    return oneSDb
}

const getPriceByOneSCode = ( oneSCode ) => {
    if ( !oneSDb[ oneSCode ] ) return -1
    return oneSDb[ oneSCode ].split(';')[ 5 ].replace( ',', '.' )
}

const getUnitsByOneSCode = ( oneSCode ) => {
    if ( !oneSDb[ oneSCode ] ) return -1
    return oneSDb[ oneSCode ].split(';')[ 3 ]
}

const getPositionNameByOneSCode = ( oneSCode ) => {
    if ( !oneSDb[ oneSCode ] ) return 'Позиция отсутствует в 1с!'
    return oneSDb[ oneSCode ].split(';')[ 1 ]
}

// Получение размеров листа материала
const getMaterialSheetSizesByOneSCode = ( oneSCode ) => {

    const oneSPositionString = getPositionNameByOneSCode( oneSCode ) // например, ПВХ 3050*2050 6мм белый;00-00002142;л;112;2909,70;
    let sizes = oneSPositionString.match(/(\d+)\*(\d+)/)
    if ( !sizes || sizes.length < 3 ) {
        // Не нашли размеры в строке позиции 1с
        // заполним нулями, чтобы было очевидно в калькуляторе, что что-то пошло не так
        sizes = [ 0, 0, 0 ]
    }
    return { materialSheetWidth: sizes[ 1 ], materialSheetHeight: sizes[ 2 ] }
}

export {
    getOneSDb,
    getPriceByOneSCode,
    getUnitsByOneSCode,
    getPositionNameByOneSCode,
    getMaterialSheetSizesByOneSCode,
}
