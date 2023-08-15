// makeMaterialsList, makeWorksList

import materials from './materials'


export const makeMaterialsList = ( materialNames ) => {

    // Объект материалов, исходя из переданного списка имён материалов
    // Все доступные имена материалов в materials-names.js
    // (Такой список позволит пользоваться подсказками в IDE)
    const materialsByNames = {}

    materialNames.forEach( ( materialName ) => {
        // console.log( 'materials[ oneSCode ]' )
        // console.log( materials[ oneSCode ] )
        materialsByNames[ materialName ] = materials[ materialName ]
    } )
    console.table( materialsByNames )
    return materialsByNames
}

export const makeWorksList = () => {}
