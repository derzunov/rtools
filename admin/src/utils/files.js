const saveObjectToJSONFile = ( object, name = 'object' ) => {
    const json = JSON.stringify( {
        ...object,
        php: `${ name }.php`,
        html: `${ name }.html`,
        jpg: `${ name }.jpg`,
    } )
    const blob = new Blob( [ json ], { type: 'application/json' } )
    const link = document.createElement( 'a' )
    link.href = URL.createObjectURL( blob )
    link.download = `${ name }.json`
    document.body.appendChild( link )
    link.click()
    document.body.removeChild( link )
}

const saveHTMLToFile = ( htmlString, name = 'document' ) => {
    const blob = new Blob( [ htmlString ], { type: 'text/html;charset=utf-8' } )
    const link = document.createElement( 'a' )
    link.href = URL.createObjectURL( blob )
    link.download = `${ name }.html`
    document.body.appendChild( link )
    link.click()
    document.body.removeChild( link )
}

const saveSvgToFile = ( svg, name = 'svg' ) => {
    const blob = new Blob( [ svg ], { type: 'image/svg+xml' } )
    const link = document.createElement( 'a' )
    link.href = URL.createObjectURL( blob )
    link.download = `${ name }.svg`
    document.body.appendChild( link )
    link.click()
    document.body.removeChild( link )
}

const readFileAsDataFromInput = ( input ) => {
    return new Promise(( resolve, reject ) => {
        if ( input.files && input.files[ 0 ] ) {
            const reader = new FileReader()
            reader.onload = function ( event ) {
                resolve( event.target.result )
            }
            reader.readAsDataURL( input.files[ 0 ] )
        } else {
            reject()
        }
    } )
}

const readFileAsTextFromInput = ( input ) => {
    return new Promise(( resolve, reject ) => {
        if ( input.files && input.files[ 0 ] ) {
            const reader = new FileReader()
            reader.onload = function ( event ) {
                resolve( event.target.result )
            }
            reader.readAsText( input.files[ 0 ] )
        } else {
            reject()
        }
    } )
}

const saveCanvasToJpeg = ( canvas, fileName = 'image' ) => {
    const dataURL = canvas.toDataURL( 'image/jpeg' )
    const link = document.createElement( 'a' )
    document.body.appendChild( link ) // Firefox requires the link to be in the body
    link.href = dataURL
    link.download = `${ fileName }.jpg`
    link.click()
    document.body.removeChild( link )
}

const loadImageBySrc =  ( src ) => {
    return new Promise(( resolve, reject ) => {
        let image = new Image()
        image.onload = () => resolve( image )
        image.onerror = reject
        image.src = src
    } )
}


export {
    saveObjectToJSONFile,
    saveHTMLToFile,
    saveSvgToFile,
    readFileAsDataFromInput,
    readFileAsTextFromInput,
    saveCanvasToJpeg,
    loadImageBySrc,
}
