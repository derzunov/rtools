export default ( url ) => {
    // eslint-disable-next-line
    const urlRegExp = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/
    const groups = url.match( urlRegExp )
    if ( !groups ) return url
    // [0] => https://www.site.su/path/to/ [1] => www [2] => /path/to/
    return groups[ 2 ]
};
