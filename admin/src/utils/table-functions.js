import {random} from "@/utils/index";

const addRow = ( table, rowIndex ) => {

    // Формируем массив ячеек новой строки
    const cols = []

    // table.thead.rows[ 1 ] - Вторая строка шапки таблицы
    // По их количеству формируется количество столбцов всей таблицы,
    // кроме первой строки шапки таблицы
    for ( let i = 0; i < table.thead.rows[ 1 ].cols.length; i++ ) {
        cols.push( {
            value: '',
            colspan: 1,
            rowspan: 1,
        } )
    }

    // Вставляем новую строку
    table.rows.splice( rowIndex + 1, 0, {
        id: Date.now() + random( 100 ),
        cols,
    } )
}

const deleteRow = ( table, rowIndex ) => {
    table.rows.splice( rowIndex, 1 )
}

const addCol = ( table, row, rowIndex, colIndex ) => {
    if ( rowIndex > 0 ) {
        table.rows.forEach( ( rowItem ) => {
            // Если это не верхняя строка шапки таблицы, добавляем столбцы для всех строк таблицы
            rowItem.cols.splice( colIndex + 1, 0, { value: '', colspan: 1, rowspan: 1 } )
        } )
        table.thead.rows[ rowIndex ].cols.splice( colIndex + 1, 0, { value: '', colspan: 1, rowspan: 1 } )
    } else { // Верхняя строка шапки таблицы добавляет ячейки независимо ото всех
        table.thead.rows[ rowIndex ].cols.splice( colIndex + 1, 0, { value: '', colspan: 1, rowspan: 1 } )
    }
}

const deleteCol = ( table, row, rowIndex, colIndex ) => {
    if ( rowIndex > 0 ) { // Если это не верхняя строка шапки таблицы
        table.rows.forEach( ( rowItem ) => {
            // тело таблицы
            rowItem.cols.splice( colIndex, 1 )
        } )
        // шапка таблицы
        table.thead.rows[ rowIndex ].cols.splice( colIndex, 1 )
    } else { // Верхняя строка шапки таблицы добавляет ячейки независимо ото всех
        table.thead.rows[ rowIndex ].cols.splice( colIndex, 1 )
    }
}

export {
    addRow,
    deleteRow,
    addCol,
    deleteCol,
}
