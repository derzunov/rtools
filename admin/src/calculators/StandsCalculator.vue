<!-- При использовании этого калькулятора в другом проекте на vue не забыть забрать ещё и папку utils/ -->
<template>
  <h4>
    Конструктор стендов
  </h4>

  <div ref="calculatorContainer">

    <h4>{{ productName }}</h4>
    <p>{{ productDescription }}</p>
    <div class="row stands-constructor">
      <div class="col-md-6 stands-constructor__controls">

        <div class="input-group mb-3">
          <span class="input-group-text">Upload state</span>
          <input class="form-control" type="file" accept="application/json"  @change="onLoadState"/>
        </div>

        <div class="input-group mb-3">
          <span class="input-group-text">Изображение для фона</span>
          <input class="form-control" type="file" accept="image/jpeg, image/gif, image/png"  @change="onBackgroundImageChange"/>
        </div>

        <div class="input-group mb-3">
          <span class="input-group-text">Изображение для стенда</span>
          <input class="form-control" type="file" accept="image/jpeg, image/gif, image/png"  @change="onStandImageChange"/>
        </div>

        <div class="input-group mb-3">
          <span class="input-group-text">Цвет стенда</span>
          <input v-model="state.standColor" type="color" class="form-control" style="height: 40px;"/>
          <span class="input-group-text" style="width: 250px;" id="stand_cmyk">
            {{ (() => {
            const rgb = hexToRgb( state.standColor )
            return rgbToCmyk( rgb.r, rgb.g, rgb.b ) })() }}
          </span>
        </div>

        <div class="input-group mb-3">
          <span class="input-group-text">
            <input v-model="state.isHead" class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
            &nbsp;С шапкой
          </span>

          <span class="input-group-text">Текст шапки</span>
          <input :disabled="!state.isHead" type="string" v-model="state.headText" class="form-control" id="stand_height">
        </div>

        <div v-if="state.isHead" class="input-group mb-3">
          <select class="form-control" v-model="state.fontFamily" style="min-width: 117px;">
            <option value="sans-serif">Без засечек</option>
            <option value="serif">С засечками</option>
          </select>

          <span class="input-group-text">
            <input v-model="state.isItalic" class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
            &nbsp;&nbsp;Курсив
          </span>

          <span class="input-group-text">
            <input v-model="state.isBold" class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
            &nbsp;Жирный
          </span>

          <span class="input-group-text">Высота (мм):</span>
          <input type="number" v-model="state.fontSize" class="form-control">
        </div>

        <div class="input-group mb-3">
          <span class="input-group-text">Ширина</span>
          <input type="number" v-model="state.standWidth" step="50" min="200" max="2000" class="form-control" id="stand_width">
          <span class="input-group-text">Высота</span>
          <input type="number" v-model="state.standHeight" step="50" min="300" max="2000" class="form-control" id="stand_height">
        </div>

        <h5 v-if="state.pockets.length">Карманы</h5>
        <div class="input-group mb-3" v-for="( pocket, index ) in state.pockets" :key="index">
          <span class="input-group-text">Размер</span>
          <select class="form-control" v-model="pocket.size">
            <option v-for="( sizeObject, size ) in sizes" :key="size">{{ size }}</option>
          </select>
          <span class="input-group-text">x</span>
          <input type="number" step="10" min="0" v-model="pocket.x" class="form-control">
          <span class="input-group-text">y</span>
          <input type="number" step="10" min="0" v-model="pocket.y" class="form-control">
        </div>

        <div class="input-group mb-3">
          <button
              @click="addPocket( 10, Math.round( state.standHeight / 1.5 ) )"
              class="btn btn-primary" >
            Добавить карман
          </button>
        </div>

        <div class="input-group mb-3">
          <button
              @click="() => {
                const fileName = `stand_${ state.standWidth }x${ state.standHeight }_${ ruToLat( productName ) }`
                saveObjectToJSONFile( state, fileName  )
                saveSvgToFile( state.svg, fileName  )
                saveHTMLToFile( calculatorContainer.innerHTML, fileName )
              }"
              class="btn btn-primary" >
            Сохранить состояние
          </button>
        </div>

        <hr>
        <h5>Материалы</h5>

        <!-- Основа -->
        <div class="input-group mb-3">
          <label class="input-group-text" for="inputGroupSelect01">Основа</label>

          <select class="form-select" v-model="state.materialOsnovy" >
            <option v-for="( materialObject, materialName ) in materialyOsnovy" :value="materialName" :key="materialName">{{ materialObject.title }}</option>
          </select>
        </div>
        <div>
          <p>{{ materialyOsnovy[ state.materialOsnovy ].description }}</p>
        </div>

        <!-- Карманы -->
        <div class="input-group mb-3">
          <label class="input-group-text" for="inputGroupSelect01">Карманы</label>

          <select class="form-select" v-model="state.materialKarmanov" >
            <option v-for="( materialObject, materialName ) in materialyKarmanov" :value="materialName" :key="materialName">{{ materialObject.title }}</option>
          </select>
        </div>
        <div>
          <p>{{ materialyKarmanov[ state.materialKarmanov ].description }}</p>
        </div>

        <hr>
        <div class="input-group mb-3">
          <button
              @click="() => {
                calculate( state )
              }"
              class="btn btn-primary" >
            Рассчитать
          </button>
        </div>

      </div>


      <div class="col-md-6 stands-constructor__canvas">
        <canvas @click="saveCanvasToJpeg( $event.target, 'stand_preview' )" ref="canvas" style="cursor: pointer;"></canvas>

        <h5>SVG:</h5>
        <div v-html="state.svg"></div>

      </div>
      <div class="col-md-12">
        <div v-if="!!clientPrice" class="mb-3">
          <h4>Цена для клиента</h4>
          <p>{{ clientPrice }}₽</p>
        </div>

        <div v-if="!!clientPrice" class="mb-3">
          <h4>Отчёт для менеджера</h4>
          <div v-if="!!managersReportHtml" v-html="managersReportHtml"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted } from 'vue'
import {
  loadImageBySrc,
  rgbToCmyk,
  hexToRgb,
  saveCanvasToJpeg,
  saveObjectToJSONFile,
  saveSvgToFile,
  saveHTMLToFile,
  readFileAsDataFromInput,
  readFileAsTextFromInput,
  ruToLat,
  C2S, // (https://github.com/derzunov/canvas2svg)

} from '@/utils'

import {
  materialNames,
  makeMaterialsList,
  roundUpNumber,
} from './data/'

import sizes from './constants/sizes'
import backgroundImage from '@/assets/wall.jpg'

export default {
  name: "StandsComponent",
  props: {
    // Что касается карточки товара (так и оставим нереактивными пропсами)
    productName: String,
    productDescription: String,

    // Что касается конструктора (значения запишем в реактивный state)
    background: String,
    standWidth: Number,
    standHeight: Number,
    pocketsCountInRow: Number,
    pocketsRowsCount: Number,
    pocketSize: String, // A3, A4, A5
    isHead: Boolean,
    headText: String,
    isItalic: Boolean,
    isBold: Boolean,
    fontSize: Number,
    fontFamily: String,
    orientation: String, // v - вертикальная, h - горизонтальная
    backgroundImage: String,
    standColor: String,
    standImage: String,
    svg: String,
    pockets: Array,

    materialOsnovy: String,
    materialKarmanov: String,
  },
  setup( props ) {

    const IS_DEV = window.location.host.includes( 'localhost' )
    const ROOT_HOST = IS_DEV ?
        'http://localhost' :
        'https://r-color.ru'
    console.log( ROOT_HOST )
    // const PRICES_PATH = ROOT_HOST + '/tools/price/'
    const canvas = ref( null )
    const calculatorContainer = ref( null )

    const state = reactive( {
      // Конструктор стендов
      standWidth: props.standWidth || 1000,
      standHeight: props.standHeight || 1000,
      pocketsCountInRow: props.pocketsCountInRow || 3,
      pocketsRowsCount: props.pocketsRowsCount || 2,
      pocketSize: props.pocketSize || 'A3',
      backgroundImage: props.backgroundImage || backgroundImage,
      standColor: props.standColor || '#ffffff',
      standImage: props.standImage || '',
      isHead: props.isHead || true,
      headText: props.headText || 'Информация',
      isItalic: props.isItalic || false,
      isBold: props.isBold || false,
      fontSize: props.fontSize || 80, // mm
      fontFamily: props.fontFamily || 'sans-serif',
      orientation: props.orientation || 'v', // v - вертикальная, h - горизонтальная
      svg: props.svg || '',
      pockets: props.pockets || [],

      // Материалы
      probka: { x: 100, y: 100, width: 297, height: 420 }, //  или null, если пробковая вставка не нужна
      materialOsnovy: props.materialOsnovy || materialNames.FANERA_BERYOZA_3MM_CHYORNYJ,
      materialKarmanov: props.materialKarmanov || materialNames.ORGSTEKLO_1_5MM_PROZRACHNYJ,
    } )

    const clientPrice = ref( 0 )
    const managersReportHtml = ref( '' )

    const draw = async ( canvas, params ) => {
      const CANVAS_PADDING = 50 // px

      const canvasWidth = canvas.getBoundingClientRect().width
      canvas.width = canvasWidth // Проставляем атрибут явно (так надо)
      const canvasHeight = canvas.height = canvasWidth // Квадратная канва

      const CANVAS_STAND_SPACE = canvasWidth - CANVAS_PADDING * 2 // px - допустимое место на канве для отрисовки стенда

      // Масштаб (px vs. mm)
      let scale = 1
      if ( params.standWidth > params.standHeight ) {
        scale = CANVAS_STAND_SPACE / params.standWidth // px/mm
      } else {
        scale = CANVAS_STAND_SPACE / params.standHeight
      }

      const context = canvas.getContext( '2d' )
      // контекст для SVG (https://github.com/derzunov/canvas2svg)
      const svgContext = new C2S( canvasWidth, canvasHeight )

      const drawBackground = async ( params, context ) => {
        context.fillStyle = 'rgba( 245, 245, 220, 1 )'
        context.fillRect( 0, 0, canvasWidth, canvasHeight )

        // Рисуем стандартную картинку фона
        const image = await loadImageBySrc( params.backgroundImage )
        context.drawImage( image, 0, 0, canvasWidth, canvasHeight )
      }

      const drawStand = async ( params, context ) => {
        const standX = ( canvasWidth - params.standWidth * scale ) / 2
        const standY = ( canvasHeight - params.standHeight * scale ) / 2

        // Тень
        context.shadowColor = '#888'
        context.shadowBlur = 15

        // Закрашиваем стенд
        context.fillStyle = params.standColor
        context.fillRect( standX, standY, params.standWidth * scale, params.standHeight * scale )

        // Обводим стенд
        context.strokeStyle = "#0000ff"
        context.lineWidth   = 1
        context.rect( standX, standY, params.standWidth * scale, params.standHeight * scale )
        context.stroke()
        // Сбрасываем тень и цвет
        context.shadowColor = 'none'
        context.shadowBlur = 0

        // Если передана картинка стенда, отображаем её
        if ( params.standImage ) {
          const image = await loadImageBySrc( params.standImage )
          context.drawImage( image, standX, standY, params.standWidth * scale, params.standHeight * scale )
        }
      }

      const drawHead = ( params, context ) => {
        // Нужно ли вообще рисовать шапку
        if ( !params.isHead ) return

        const HEAD_HEIGHT = 150 // mm
        const headX = ( canvasWidth - params.standWidth * scale ) / 2
        const headY = ( canvasHeight - params.standHeight * scale ) / 2 - HEAD_HEIGHT * scale
        // Шапка
        context.fillRect( headX, headY, params.standWidth * scale, HEAD_HEIGHT * scale )
        context.fillStyle = 'rgb( 50, 50, 50 )'

        // Черта под шапкой
        context.beginPath()
        context.moveTo( headX, headY + HEAD_HEIGHT * scale )
        context.lineTo( headX + params.standWidth * scale, headY + HEAD_HEIGHT * scale )
        context.stroke()

        // Обводим шапку
        context.strokeStyle = "#0000ff"
        context.lineWidth   = 1
        context.rect( headX, headY, params.standWidth * scale, HEAD_HEIGHT * scale )
        context.stroke()

        // текст шапки
        context.font = "24px sans-serif"
        context.font = `${ params.isItalic ? 'italic' : '' }
        ${ params.isBold ? 'bold' : '' }
        ${ params.fontSize * scale }px
        ${ params.fontFamily }`
        const headTextWidth = context.measureText( params.headText ).width

        const headTextX = headX + params.standWidth * scale / 2 - headTextWidth / 2
        const headTextY = headY + HEAD_HEIGHT * scale - 24 / 2 // Размер шрифта / 2
        context.fillText( params.headText, headTextX, headTextY, params.standWidth * scale )
      }

      const drawPockets = ( params, context ) => {
        params.pockets.forEach( ( pocket ) => {

          const standX = ( canvasWidth - params.standWidth * scale ) / 2 // px
          const standY = ( canvasHeight - params.standHeight * scale ) / 2 // px

          const pocketStartX = standX + pocket.x * scale
          const pocketStartY = ( standY + params.standHeight * scale ) - pocket.y * scale

          // Тень
          context.shadowColor = 'rgba( 0, 0, 0, 0.2 )'
          context.shadowBlur = 5

          //  Сам карман
          context.fillStyle = 'rgba( 220, 220, 220, 0.7 )'
          context.fillRect( pocketStartX, pocketStartY, sizes[ pocket.size ].width * scale, sizes[ pocket.size ].height * scale )

          // Сбрасываем тень для текста
          context.shadowColor = 'none'
          context.shadowBlur = 0

          // Текст размера кармана
          const fontSize = 20
          context.font = `${ fontSize }px sans-serif`
          context.fillStyle = 'rgba( 0, 0, 0, 0.5 )'
          const pocketSizeTextWidth = context.measureText( pocket.size ).width // px
          const pocketSizeTextX = pocketStartX + sizes[ pocket.size ].width * scale / 2 - pocketSizeTextWidth / 2
          const pocketSizeTextY = pocketStartY + sizes[ pocket.size ].height * scale / 2
          context.fillText( pocket.size, pocketSizeTextX, pocketSizeTextY )
        } )
      }

      const drawSize = ( params, context ) => {
        // Shadow
        context.shadowColor = '#888'
        context.shadowBlur = 15

        // Triangle
        context.fillStyle = '#fff'
        const TRIANGLE_SIZE = 150

        context.beginPath();
        context.moveTo( 0, 0 )
        context.lineTo( TRIANGLE_SIZE, 0 )
        context.lineTo( 0,TRIANGLE_SIZE )
        context.fill()

        // Text
        context.font = "bold 24px sans-serif"
        const sizesText = `${ params.standWidth } x ${ params.standHeight }`
        const textWidth = context.measureText( sizesText ).width

        const rotateAngle = 45
        const rotatedYDelta = TRIANGLE_SIZE * Math.cos( ( rotateAngle * Math.PI ) / 180 )
        const centeredRotatedXDelta = textWidth / 2

        context.fillStyle = "#000"
        context.rotate( ( -rotateAngle * Math.PI ) / 180 )
        context.fillText( sizesText, -centeredRotatedXDelta, rotatedYDelta - 10 )

        context.resetTransform()
        // Сбрасываем тень
        context.shadowColor = 'none'
        context.shadowBlur = 0
      }

      const drawUps = () => {}

      // Рисуем
      await drawBackground( params, context )
      await drawStand( params, context )
      drawHead( params, context )
      drawPockets( params, context )
      drawSize( params, context )
      drawUps( params, context )

      // SVG (see fork https://github.com/derzunov/canvas2svg)
      await drawStand( params, svgContext )
      drawHead( params, svgContext )
      drawPockets( params, svgContext )
      state.svg = svgContext.getSerializedSvg()
    }

    const setOrientation = () => {
      // TODO: Отрефачить эту парашу
      // Выбрали вертикальную
     if ( state.orientation === 'v' ) {
        for ( let size in sizes ) {
          const width = sizes[ size ].width
          const height = sizes[ size ].height
          if ( width > height ) { // Нужно поменять местами длину и ширину
            sizes[ size ].width = height
            sizes[ size ].height = width
          }
        }
     } else { // Выбрали горизонтальную
       for ( let size in sizes ) {
         const width = sizes[ size ].width
         const height = sizes[ size ].height
         if ( width < height ) { // Нужно поменять
           sizes[ size ].width = height
           sizes[ size ].height = width
         }
       }
     }
    }

    const onBackgroundImageChange = async ( event ) => {
      const input = event.target
      state.backgroundImage = await readFileAsDataFromInput( input )
    }

    const onStandImageChange = async ( event ) => {
      const input = event.target
      state.standImage = await readFileAsDataFromInput( input )
    }

    const onLoadState  = async ( event ) => {
      const input = event.target
      const loadedStateText = await readFileAsTextFromInput( input )
      const loadedStateJson = await JSON.parse( loadedStateText )
      for ( const stateKey in loadedStateJson ) {
        state[ stateKey ] = loadedStateJson [ stateKey ]
      }
    }

    const addPocket = ( x = 10, y = 500 ) => {
      const defaultPocketObject = { size: 'A4', orientation: 'v', x, y }
      state.pockets.push( { ...defaultPocketObject } )
    }

    // --------------------------------------------------------------------
    // Калькуляция --------------------------------------------------------

    // Создаём объект возможных материалов для основы
    // Реактивность тут не нужна, этот список задаётся из кода руками
    // TODO?! Возможно, реактивность понадобится,
    // тк в зависимости от параметров список допустимых материалов может меняться
    const materialyOsnovy = makeMaterialsList(
        [
          materialNames.PVH_10MM_BELYJ,
          materialNames.FANERA_BERYOZA_3MM_CHYORNYJ,
          materialNames.PVH_3MM_CHYORNYJ,
        ]
    )

    // Создаём объект возможных материалов для карманов
    // Реактивность тут не нужна, этот список задаётся из кода руками
    // TODO?! Возможно, реактивность понадобится,
    // тк в зависимости от параметров список допустимых материалов может меняться
    const materialyKarmanov = makeMaterialsList(
        [
          materialNames.ORGSTEKLO_1_5MM_PROZRACHNYJ,
          materialNames.ORGSTEKLO_2MM_PROZRACHNYJ,
        ]
    )

    // Количество материалов
    const calculateMaterialsRate = ( params ) => {

      const materialsRateObject = {}

      // Основа ----------------------------------------------------------------
      const ploshadOsnovyMm2 = params.standHeight * params.standWidth // mm^2
      // Расход на основу в кв. м
      const ploshadOsnovyM2 = roundUpNumber( ploshadOsnovyMm2 / ( 1000 * 1000 ) ) // m^2

      // Добавляем в итоговый объект израсходованных материалов материал основы с его количеством
      materialsRateObject[ params.materialOsnovy ] = {
        ...materialyOsnovy[ params.materialOsnovy ],
        quantity: ploshadOsnovyM2,
      }

      // Карманы ----------------------------------------------------------------

      // Есть ли вообще карманы
      if ( params.pockets.length ) {

        let obshayaPloshadKarmanovMM2 = 0
        params.pockets.forEach( ( pocket ) => {
          obshayaPloshadKarmanovMM2 += sizes[ pocket.size ].width * sizes[ pocket.size ].height
        } )

        // Общая площадь карманов в кв м
        const ploshadKarmanovM2 = roundUpNumber( obshayaPloshadKarmanovMM2 / ( 1000 * 1000 ) )

        // Добавляем в итоговый объект израсходованных материалов материал карманов с их общей площадью
        materialsRateObject[ params.materialKarmanov ] = {
          ...materialyKarmanov[ params.materialKarmanov ],
          quantity: ploshadKarmanovM2,
        }
      }

      return materialsRateObject
    }

    // Количество работ (пока заглушка)
    const calculateWorksRate = ( params ) => {
      console.log( params )
      return {}
    }

    // Считаем цену для клиента
    const calculateClientPrice = ( materialsRateObject, worksRateObject ) => {
      const css = "font-size: 12px; color: #57F287"
      console.log( '%c' + JSON.stringify( materialsRateObject ), css )
      console.log( '%c' + JSON.stringify( worksRateObject ), css )

      let cenaMaterialov = 0

      // Идем по всем материалам, множим их расход на их цену и прибавляем к общим затратам на материалы
      Object.values( materialsRateObject ).forEach( ( materialObj ) => {
        // TODO: Здесь будут учитываться дополнительные условия для отдельных материалов, размеров и прочего
        cenaMaterialov += ( materialObj.cena * materialObj.quantity )
      } )

      return roundUpNumber( cenaMaterialov )
    }

    // Формируем отчёт для менеджера text/html/csv/email/etc.
    const createManagersReport = ( materialsRateObject, worksRateObject ) => {

      const calculateMaterialItemPrice = ( materialItem ) => {
        // Отчасти повторяет функцию calculateClientPrice только потому,
        // что я предполагаю разные подсчёты для клиента и менеджера
        // TODO: Здесь будут учитываться дополнительные условия для отдельных материалов, размеров и прочего
        return roundUpNumber( materialItem.quantity * materialItem.cena )
      }

      const createMaterialItemHTML = ( materialItem ) => {
        return `
          <p>${ materialItem.title } x ${ materialItem.quantity } ${ materialItem.edIzm } = ${ calculateMaterialItemPrice( materialItem ) }₽</p>
        `
      }

      let materialsHtml = '<h6>Материалы:</h6>'

      Object.values( materialsRateObject ).forEach( ( materialItem ) => {
        materialsHtml = materialsHtml + createMaterialItemHTML( materialItem )
      } )


      const css = "font-size: 12px; color: #57F287"
      console.log( '%c' + JSON.stringify( materialsRateObject ), css )
      console.log( '%c' + JSON.stringify( worksRateObject ), css )
      return materialsHtml
    }


    // Функция запускающая все расчёты
    const calculate = ( params ) => {
      const materialsRateObject = calculateMaterialsRate( params )
      const worksRateObject = calculateWorksRate( params )
      clientPrice.value = calculateClientPrice( materialsRateObject, worksRateObject )
      managersReportHtml.value = createManagersReport( materialsRateObject, worksRateObject )
    }

    // / Калькуляция ------------------------------------------------------

    onMounted( () => {
      setOrientation()
      draw( canvas.value, state )
    } )

    watch( [ state ], () => {
      setOrientation() // deprecated
      draw( canvas.value, state )
    },{
      flush: 'post'
    } )

    return {
      calculatorContainer,
      canvas,
      state,
      sizes,

      clientPrice,
      managersReportHtml,

      materialyOsnovy,
      materialyKarmanov,

      // Functions
      onBackgroundImageChange,
      onStandImageChange,
      saveCanvasToJpeg,
      onLoadState,
      addPocket,

      calculate,

      // Utils
      hexToRgb,
      rgbToCmyk,
      saveObjectToJSONFile,
      saveSvgToFile,
      saveHTMLToFile,
      ruToLat,
    }
  }
}
</script>

<style scoped>
canvas {
  width: 100%;
}
</style>
