const CURRENT_DATE_TIMESTAMP = Date.now()

export const isItemActive = ( item ) => {
    const dateStartTimestamp = new Date( item.date_start || '' ).getTime()
    const dateEndTimestamp = new Date( item.date_end || '' ).getTime() + 24 * 60 * 60 * 1000
    return ( CURRENT_DATE_TIMESTAMP >= dateStartTimestamp &&
        CURRENT_DATE_TIMESTAMP <= dateEndTimestamp )
}

export const filterActiveItems = ( items ) => {
    return items?.filter( ( item ) => {
        return isItemActive( item )
    } )
}

export const filterItemsByType = ( items, type = 'news' ) => {
    return items?.filter( item => item[ 'type' ] === type )
}
