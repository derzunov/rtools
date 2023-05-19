
// Configs:

const DEBUG = 'DEBUG'
const INFO = 'INFO'
const ERROR = 'ERROR'
const OFF = 'OFF'

const COLORS = {}
COLORS[ DEBUG ] = '#24fbff'
COLORS[ INFO ] = '#63de5d'
COLORS[ ERROR ] = '#ff1493'

const SYSTEM_PRIORITY_VALUES = {}
SYSTEM_PRIORITY_VALUES[ DEBUG ] = [ DEBUG, INFO, ERROR ]
SYSTEM_PRIORITY_VALUES[ INFO ] = [ INFO, ERROR ]
SYSTEM_PRIORITY_VALUES[ ERROR ] =  [ ERROR ]
SYSTEM_PRIORITY_VALUES[ OFF ] = []

const SYSTEM_PRIORITY_LEVEL = DEBUG // For Example...
// TODO: config from process.env or smth. like that for SYSTEM_PRIORITY_LEVEL


// Private methods:

const _trace = ( message, priority ) => {

    if ( !SYSTEM_PRIORITY_VALUES[ SYSTEM_PRIORITY_LEVEL ].includes( priority ) ) {
        return
    }

    const time = new Date().toISOString()
    const fullMessage = `${ time }: ${ priority }: ${ message }`
    console.log( '%c%s', `color: ${ COLORS[ priority ] };`, fullMessage )

    // TODO: Writing traces in files here :)
}

export default {
    log: ( message, priority ) => {
        _trace( message, priority )
    },
    debug: ( message ) => {
        _trace( message, DEBUG )
    },
    info: ( message ) => {
        _trace( message, INFO )
    },
    error: ( message ) => {
        _trace( message, ERROR )
    },
}

// TODO: Add priority filters
// About - https://www.digitalocean.com/community/tutorials/log4j-levels-example-order-priority-custom-filters#log4j-levels
