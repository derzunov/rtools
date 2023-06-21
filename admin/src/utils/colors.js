const rgbToCmyk = ( r, g, b ) => {
    let c, m, y, k
    r = r / 255
    g = g / 255
    b = b / 255
    const max = Math.max( r, g, b )
    k = 1 - max
    if ( k === 1 ) {
        c = 0
        m = 0
        y = 0
    } else {
        c = ( 1 - r - k ) / ( 1 - k )
        m = ( 1 - g - k ) / ( 1 - k )
        y = ( 1 - b - k ) / ( 1 - k )
    }
    //return { c: c, m: m, y: y, k: k }
    return `cmyk(${ Math.round( c * 100 ) }%, ${ Math.round( m * 100 ) }%, ${ Math.round( y * 100 ) }%, ${ Math.round( k * 100 ) }%)`
}

const hexToRgb = ( hex ) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( hex )
    return result ? {
        r: parseInt( result[ 1 ], 16 ),
        g: parseInt( result[ 2 ], 16 ),
        b: parseInt( result[ 3 ], 16 ),
    } : null
}

export { rgbToCmyk, hexToRgb }
