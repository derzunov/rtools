import axios from "axios";

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes('localhost') ?
    'http://localhost' :
    'https://r-color.ru'

export default async ( priceObject ) => {

    const tableCode = await createTable( priceObject )

    let priceHtmlString =
        `<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <div class="b-price">
            <h3 class="b-price__header">${ priceObject.header }</h3>
            <p>${ priceObject.toptext }</p>
            ${ tableCode }
            <p class="b-price__update-date" >Дата обновления цен на ${ priceObject.header } - ${ ( new Date( priceObject.actualized_date ) ).toLocaleDateString( 'ru-RU' ) }</p>
            <p>${ priceObject.bottomtext }</p>
            
        <div>
        <style>
          .b-price__update-date {
            color: #888;
            font-size: 0.8rem;
            text-align: right;
          }
          .b-price-table {
            border-color: rgb( 185, 190, 185 );
            margin-bottom: 20px;
          }
          .b-price-table .b-price-table__col,
          .b-price-table .b-price-table__thead-col {
            border-width: 1px;
            text-align: center;
            padding: 5px;
          }
          .b-price-table .b-price-table__thead {
            background-color: #eee;
          }
        </style>
        `
    return priceHtmlString
}

const createTable = async ( priceObject ) => {
    const tableObject = priceObject.table
    const table = document.createElement( 'table' )
    table.classList.add( 'b-price-table' )
    table.style.width = '100%'
    table.setAttribute( 'border', '1' )
    table.setAttribute( 'align', 'center' )

    // Заголовок таблицы -----------------------------------------
    const thead = document.createElement( 'thead' )
    thead.classList.add( 'b-price-table__thead' )

    for ( let i = 0; i < tableObject.thead.rows.length; i++ ) {
        const cols = tableObject.thead.rows[ i ].cols
        const tr = document.createElement( 'tr' )

        tr.classList.add( 'b-price-table__thead-row' )
        tr.classList.add( `js_thead_row_${ i }` )

        for ( let j = 0; j < cols.length; j++ ) {
            const td = document.createElement( 'td' )
            td.classList.add( 'b-price-table__thead-col' )
            td.classList.add( `js_thead_col_${ j }` )

            if ( cols[ j ].value === '+' && i === 1 ) {
                // TODO: Считать colSpan-ы у верхней строки и вычитать каждый,
                // который больше единицы из величины ${ j } в имени класса,
                // так как получается расхождение между j у нижней строки и верхней строки
                // Ну или найти другой способ
                thead.querySelector( `.js_thead_row_0 .js_thead_col_${ j }` ).rowSpan = 2
                continue
            }

            td.appendChild( document.createTextNode( cols[ j ].value ) )
            td.colSpan = cols[ j ].colspan
            td.rowSpan = cols[ j ].rowspan
            tr.appendChild( td )
        }

        thead.appendChild( tr )
    }
    // -----------------------------------------------------------

    // Тело таблицы ----------------------------------------------
    const tbody = document.createElement( 'tbody' )

    for ( let i = 0; i < tableObject.rows.length; i++ ) {
        const cols = tableObject.rows[ i ].cols
        const tr = document.createElement( 'tr' )
        tr.classList.add('b-price-table__row')

        for ( let j = 0; j < cols.length; j++ ) {
            const td = document.createElement( 'td' )
            td.classList.add('b-price-table__col')

            // Images _Bird_ language
            const imageCode = parsePossibleImageCode( cols[ j ].value )

            if ( imageCode ) {
                td.innerHTML = imageCode
                td.colSpan = cols[ j ].colspan
                td.rowSpan = cols[ j ].rowspan
                tr.appendChild( td )
                continue
            }

            // 1С _Bird_ language
            const oneSCode = parsePossibleOneSCode( cols[ j ].value )

            if ( oneSCode ) {
                // Бла бла, сходи на /tools/price/,
                // отыщи такой код ( 00-00002340 - такой, например ),
                // получи по нему цену, помножь на коэфициент наценки
                // и вставь её в ячейку
                const url = `${ BASE_URL }/tools/price/?code=${ oneSCode }`
                const response = await axios.get( url )
                if ( priceObject.markup_factor ) {
                    td.innerHTML = Math.ceil( ( parseFloat( response.data ) ) * parseFloat( priceObject.markup_factor ) )
                } else {
                    td.innerHTML = response.data
                }
                td.colSpan = cols[ j ].colspan
                td.rowSpan = cols[ j ].rowspan
                tr.appendChild( td )
            } else {
                td.appendChild( document.createTextNode( cols[ j ].value ) )
                td.colSpan = cols[ j ].colspan
                td.rowSpan = cols[ j ].rowspan
                tr.appendChild( td )
            }
        }
        tbody.appendChild( tr )
    }
    // -----------------------------------------------------------

    // Вставляем в таблицу сформированные head и body
    table.appendChild( thead )
    table.appendChild( tbody )

    return table.outerHTML
}

const parsePossibleImageCode = ( colValue ) => {
    // Rule: [[image url]]

    // Regex: (\[\[)((\w|\W)+)(\]\])
    //
    // Example (colValue):
    // [[https://r-color.ru/logo.png]]

    // Result (matchValue):
    // 0: Match:    [[https://r-color.ru/logo.png]]
    // 1: Group 1:  [[
    // 2: Group 2:  https://r-color.ru/logo.png     <--- !!!
    // 3: Group 3:  g
    // 4: Group 4:  ]]

    const imgRegex = /(\[\[)((\w|\W)+)(\]\])/g

    if ( colValue && typeof colValue === 'string' ) {
        const matchValue = imgRegex.exec( colValue )
        if ( matchValue ) {
            return `<img style="max-width: 200px;" src="${ matchValue[ 2 ] }" />`
        }
    }

    return null
}

const parsePossibleOneSCode = ( colValue ) => {

    const oneSRegex = /(\{\{)((\w|\W)+)(\}\})/g

    if ( colValue && typeof colValue === 'string' ) {
        const matchValue = oneSRegex.exec( colValue )
        if ( matchValue ) {
            return matchValue[2]
        }
    }

    return null
}
