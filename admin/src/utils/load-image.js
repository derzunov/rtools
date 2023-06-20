export default ( src ) => {
    return new Promise(( resolve, reject ) => {
        let image = new Image()
        image.onload = () => resolve( image )
        image.onerror = reject
        image.src = src
    } )
}
