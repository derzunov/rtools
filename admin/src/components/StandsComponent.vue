<template>
  <h4>
    Конструктор стендов
  </h4>

  <h4>{{ productName }}</h4>
  <p>{{ productDescription }}</p>
  <div class="row stands-constructor">
    <div class="col-md-6 stands-constructor__controls">

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

      <div class="input-group mb-3">
        <span class="input-group-text">Ширина</span>
        <input type="number" v-model="state.standWidth" step="50" min="200" max="2000" class="form-control" id="stand_width">
        <span class="input-group-text">Высота</span>
        <input type="number" v-model="state.standHeight" step="50" min="300" max="2000" class="form-control" id="stand_height">
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text">Количество в ряду</span>
        <input type="number" v-model="state.pocketsCountInRow" class="form-control" id="pockets_count_in_row">
        <span class="input-group-text">Количество рядов</span>
        <input type="number" v-model="state.pocketsRowsCount" class="form-control" id="pockets_rows_count">
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text">Размер</span>
        <select class="form-control" v-model="state.pocketSize">
          <option v-for="( sizeObject, size ) in sizes" :key="size">{{ size }}</option>
        </select>
        <span class="input-group-text">Ориентация</span>
        <select v-model="state.orientation" class="form-control">
          <option value="v">Вертикально</option>
          <option value="h">Горизонтально</option>
        </select>
      </div>

      <div class="input-group mb-3">
        <button
            @click="() => {
              const fileName = `stand_${ state.standWidth }x${ state.standHeight }_${ ruToLat( productName ) }`
              saveObjectToJSONFile( state, fileName  )
              saveSvgToFile( state.svg, fileName  )
            }"
            class="btn btn-primary" >
          Сохранить состояние
        </button>
      </div>

    </div>
    <div class="col-md-6 stands-constructor__canvas">
      <canvas @click="saveCanvasToImage" ref="canvas" style="cursor: copy;"></canvas>

      <h5>SVG:</h5>
      <div v-html="state.svg"></div>

    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted } from 'vue'
import {
  loadImage,
  rgbToCmyk,
  hexToRgb,
  saveObjectToJSONFile,
  saveSvgToFile,
  ruToLat,
  C2S, //(https://github.com/derzunov/canvas2svg)
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
    orientation: String, // v - вертикальная, h - горизонтальная
    backgroundImage: String,
    standColor: String,
    standImage: String,
    svg: String,
  },
  setup( props ) {

    const canvas = ref( null )

    const state = reactive( {
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
      orientation: props.orientation || 'v', // v - вертикальная, h - горизонтальная
      svg: props.svg || ''
    } )


    const draw = async ( canvas, params ) => {
      const CANVAS_PADDING = 50 // px

      // Размеры канвы (соотношение, как соотношение "стены")
      const canvasWidth = canvas.getBoundingClientRect().width
      canvas.width = canvasWidth // Проставляем атрибут явно (так надо)
      const canvasHeight = canvas.height = canvasWidth // Квадратная канва

      const CANVAS_STAND_SPACE = canvasWidth - CANVAS_PADDING * 2 // px - допустимое место для стенда

      // Масштаб (мм vs. px)
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
        const image = await loadImage( params.backgroundImage )
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
          const image = await loadImage( params.standImage )
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
        const headTextWidth = context.measureText( params.headText ).width

        const headTextX = headX + params.standWidth * scale / 2 - headTextWidth / 2
        const headTextY = headY + HEAD_HEIGHT * scale - 24 / 2 // Размер шрифта / 2
        context.fillText( params.headText, headTextX, headTextY, params.standWidth * scale )
      }

      const drawPockets = ( params, context ) => {
        const POCKETS_PADDING = 50 // mm
        // TODO: Добавить проверку помещается ли ряд карманов
        const standX = ( canvasWidth - params.standWidth * scale ) / 2 // px
        const standY = ( canvasHeight - params.standHeight * scale ) / 2
        const rowWidth = params.pocketsCountInRow * sizes[ state.pocketSize ].width + POCKETS_PADDING * ( params.pocketsCountInRow + 1 ) // mm
        const rowsHeight = params.pocketsRowsCount * sizes[ state.pocketSize ].height + POCKETS_PADDING * ( params.pocketsRowsCount + 1 ) // mm
        const rowStartX = standX + ( ( ( params.standWidth - rowWidth ) / 2 )  + POCKETS_PADDING ) * scale // px

        // Тень
        context.shadowColor = 'rgba( 0, 0, 0, 0.2 )'
        context.shadowBlur = 5

        // Ряды
        for ( let row = 0; row < params.pocketsRowsCount; row++ ) {
          const rowStartY = standY + ( ( ( params.standHeight - rowsHeight ) / 2 )  + POCKETS_PADDING  + row * ( sizes[ state.pocketSize ].height + POCKETS_PADDING ) ) * scale  // px

          // Карманы в ряду
          for ( let pocket = 0; pocket < params.pocketsCountInRow; pocket++ ) {
            context.fillStyle = 'rgba( 220, 220, 220, 0.6 )'
            context.fillRect( rowStartX + pocket * ( ( sizes[ state.pocketSize ].width * scale ) + POCKETS_PADDING * scale ), rowStartY, sizes[ state.pocketSize ].width * scale, sizes[ state.pocketSize ].height * scale )
          }
        }

        // Сбрасываем тень
        context.shadowColor = 'none'
        context.shadowBlur = 0
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

    const onBackgroundImageChange = ( event ) => {
      const input = event.target
      if ( input.files && input.files[ 0 ] ) {
        const reader = new FileReader()
        reader.onload = function ( event ) {
          state.backgroundImage = event.target.result
        }
        reader.readAsDataURL( input.files[ 0 ] )
      }
    }

    const onStandImageChange = ( event ) => {
      const input = event.target
      if ( input.files && input.files[ 0 ] ) {
        const reader = new FileReader()
        reader.onload = function ( event ) {
          state.standImage = event.target.result
        }
        reader.readAsDataURL( input.files[ 0 ] )
      }
    }

    const saveCanvasToImage = () => {
      const dataURL = canvas.value.toDataURL( 'image/jpeg' )
      const link = document.createElement( 'a' )
      document.body.appendChild( link ) // Firefox requires the link to be in the body
      link.href = dataURL
      link.download = 'stand.jpg'
      link.click()
      document.body.removeChild( link )
    }

    onMounted( () => {
      setOrientation()
      draw( canvas.value, state )
    } )

    watch( [ state ], () => {
      setOrientation()
      draw( canvas.value, state )
    },{
      flush: 'post'
    } )

    return {
      canvas,
      state,
      sizes,

      // Functions
      onBackgroundImageChange,
      onStandImageChange,
      saveCanvasToImage,

      // Utils
      hexToRgb,
      rgbToCmyk,
      saveObjectToJSONFile,
      saveSvgToFile,
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
