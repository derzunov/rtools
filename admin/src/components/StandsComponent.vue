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

        <div class="input-group mb-3">
          <label class="input-group-text" for="inputGroupSelect01">Основа</label>
          <select class="form-select" v-model="state.standMaterialOneSCode" >
            <option v-for="( materialObject, materialCode ) in standMaterials" :value="materialCode" :key="materialCode">{{ materialObject.title }}</option>
          </select>
        </div>

      </div>
      <div class="col-md-6 stands-constructor__canvas">
        <h5>Итоговая цена: {{ complexPrice }}</h5>
        <canvas @click="saveCanvasToJpeg( $event.target, 'stand_preview' )" ref="canvas" style="cursor: pointer;"></canvas>

        <h5>SVG:</h5>
        <div v-html="state.svg"></div>

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

  getOneSDb,
  getPriceByOneSCode,
  getUnitsByOneSCode,
  getMaterialSheetSizesByOneSCode,
  getPositionNameByOneSCode,
} from '@/utils'

import sizes from '@/constants/sizes'
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
    standMaterialOneSCode: String,
  },
  setup( props ) {

    console.log( 'getOneSDb result: ', getOneSDb() )
    console.log( 'getPriceByOneSCode result: ', getPriceByOneSCode( '00-00001907' ) )
    console.log( 'getUnitsByOneSCode result: ', getUnitsByOneSCode( '00-00001907' ) )

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
      standMaterialOneSCode: props.standMaterialOneSCode || '00-00002155'
    } )

    const STAND_MATERIALS_CODES = [
      // ПВХ
      '00-00002155',
      '00-00002198',
      // Фанера
      '00-00002263',
      '00-00001907', // Просто для тестов, у этой позиции нет размеров вида ШШ*ВВ в названии
    ]

    // Кандидат на перенос в utils
    // makeMaterialsList, makeWorksList, makeItemsList, makePocketsList...
    // Списки 1с позиций, требующихся конкретным калькуляторам
    // формируются общими мейкерами
    // Набор полей объектов списка определяется конкретным мейкером
    const makeMaterialsList = ( oneSCodes ) => {

      const materials = {}

      oneSCodes.forEach( ( oneSCode ) => {
        materials[ oneSCode ] = {
          oneSCode,
          title: getPositionNameByOneSCode( oneSCode ),
          sizes: getMaterialSheetSizesByOneSCode( oneSCode ),
        }
      } )
      return materials
    }

    const standMaterials = makeMaterialsList( STAND_MATERIALS_CODES )

    const complexPrice = ref( 0 )

    const calculateStand = async ( params ) => {

      // Получаем размеры листа выбранного материала
      const {
        materialSheetWidth,
        materialSheetHeight
      } = getMaterialSheetSizesByOneSCode( params.standMaterialOneSCode )

      const materialSheetSquare = materialSheetWidth * materialSheetHeight // mm^2
      const standSquare = params.standHeight * params.standWidth // mm^2

      const priceString = getPriceByOneSCode( params.standMaterialOneSCode )

      const materialSheetPrice = parseFloat( priceString )

      const materialRate = materialSheetPrice * standSquare / materialSheetSquare

      return Math.round( materialRate )
    }
    const calculatePockets = ( params ) => {
      return params.pockets.length
    }
    const calculateWork = ( params ) => {
      return params.pockets.length * 6 // Пока просто с потолка
    }

    const calculate = async ( params ) => {
      // const materialsOfStand = []
      // complexPrice.value = params.toString() + await getPriceFromOneS( '00-00002244' )
      complexPrice.value =  await calculateStand( params ) + ' + ' + calculatePockets( params ) + ' + ' + calculateWork( params )
    }

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

    onMounted( () => {
      setOrientation()
      draw( canvas.value, state )
      calculate( state )
    } )

    watch( [ state ], () => {
      setOrientation()
      draw( canvas.value, state )
      calculate( state )
      console.log( calculatorContainer.value )
    },{
      flush: 'post'
    } )

    return {
      calculatorContainer,
      canvas,
      state,
      sizes,

      complexPrice,
      standMaterials,

      // Functions
      onBackgroundImageChange,
      onStandImageChange,
      saveCanvasToJpeg,
      onLoadState,
      addPocket,

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
