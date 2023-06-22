const saveObjectToJSONFile = ( object, name = 'object' ) => {
    const json = JSON.stringify( object )
    const blob = new Blob( [ json ], { type: 'application/json' } )
    const link = document.createElement( 'a' )
    link.href = URL.createObjectURL( blob )
    link.download = `${ name }.json`
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

export { saveObjectToJSONFile, saveSvgToFile }
