export const roundUpNumber = ( number ) => {
    let result = 0
    if ( number ) {
        if ( number <= 0.001 ) {
            result = number // вдруг такое случится - оставим цифру
        } else if ( number > 0.001 && number < 0.01 ) {
            result = Math.ceil( number * 10000 ) / 10000
        } else if ( number > 0.01 && number < 0.1 ) {
            result = Math.ceil( number * 1000 ) / 1000
        } else if ( number > 0.1 && number < 1 ) {
            result = Math.ceil( number * 100 ) / 100
        } else if ( number > 1 && number < 10 ) {
            result = Math.ceil( number * 10 ) / 10
        } else {
            result = Math.ceil( number )
        }
    }
    return result
}
