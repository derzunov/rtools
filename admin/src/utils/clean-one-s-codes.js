export const cleanOneSCodes = ( codesString ) => {

    // Избавимся от букв
    codesString = codesString.replaceAll( /[a-zA-z]/g, '' )
    codesString = codesString.replaceAll( /[а-яА-Я]/g, '' )

    // Избавимся от всех разделителей и превратим их в пробелы
    codesString = codesString.replaceAll( '\t', ' ' )
    codesString = codesString.replaceAll( /(;\s*)/g, ' ' )
    codesString = codesString.replaceAll( /(:\s*)/g, ' ' )
    codesString = codesString.replaceAll( /(,\s*)/g, ' ' )
    codesString = codesString.replaceAll( /(\.\s*)/g, ' ' )

    // Заменим все неодиночные пробелы на одиночные
    codesString = codesString.replaceAll( /(\s\s+)/g, ' ' )

    // Каждый одиночный пробел заменим на ";"
    codesString = codesString.replaceAll( /(\s)/g, ';' )

    return codesString
}
