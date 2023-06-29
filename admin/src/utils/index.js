import { default as ruToLat } from './ru-to-lat'
import { default as urlDumbProtect } from './url-dumb-protect'
import { default as random } from './random'
import { default as sleep } from './sleep'
import { default as parsePriceToHtml } from './parse-price-to-html'
import { calculateCampaignPeriodInDays } from './calculate-campaign-period'
import { filterActiveItems, filterItemsByType, isItemActive } from './filters'
import { default as tracer } from './tracer'
import { rgbToCmyk, hexToRgb } from './colors'

import {
    addRow,
    deleteRow,
    addCol,
    deleteCol,
} from './table-functions'

export {
    urlDumbProtect,
    calculateCampaignPeriodInDays,
    random,
    sleep,
    filterActiveItems,
    filterItemsByType,
    isItemActive,
    parsePriceToHtml,
    addRow,
    deleteRow,
    addCol,
    deleteCol,
    ruToLat,
    tracer,
    rgbToCmyk,
    hexToRgb,
}
export * from './files' // TODO: привести к такому виду все экспорты из index-а
export * from './one-s'
export { default as C2S } from './canvas2svg' // (https://github.com/derzunov/canvas2svg)
