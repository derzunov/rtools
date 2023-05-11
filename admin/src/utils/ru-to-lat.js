export default ( str ) => {
    let ru = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
        'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
        'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya',
        'ъ': 'ie', 'ь': '', 'й': 'i', ' ': '_',
        '.': '', '/': '', '(': '', ')': '', '[': '', ']': '',
    }

    return [ ...str ].map( letter => {
        let latLetter = ru[ letter.toLowerCase() ]

        if ( letter !== letter.toLowerCase() ) {
            latLetter = latLetter.charAt(0).toUpperCase() + latLetter.slice( 1 )
        } else if ( latLetter === undefined ) {
            latLetter = letter
        }

        return latLetter
    } ).join( '' ).replaceAll(/(_+)/g, '_').replaceAll(/\W+/g, '').toLowerCase()
}
