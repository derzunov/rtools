<template>
  <h4>
    Конструктор стендов
  </h4>
  <div class="row stands-constructor">
    <div class="col-md-6 stands-constructor__controls">

      <!-- Фон -->
      <img src="@/assets/wall.jpg" style="display: none;">

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
        <input type="number" v-model="state.standWidth" class="form-control" id="stand_width">
        <span class="input-group-text">Высота</span>
        <input type="number" v-model="state.standHeight" class="form-control" id="stand_height">
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


    </div>
    <div class="col-md-6 stands-constructor__canvas">
      <canvas ref="canvas"></canvas>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted } from 'vue'
import { loadImage } from '@/utils'
import sizes from '@/constants/sizes'
import backgroundImage from '@/assets/wall.jpg'

export default {
  name: "StandsComponent",
  props: {
    // Что касается карточки товара (так и оставим нереактивными пропсами)
    productName: Number,
    productDescription: Number,

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
      isHead: props.isHead || true,
      headText: props.headText || 'Информация',
      orientation: props.orientation || 'v', // v - вертикальная, h - горизонтальная
    } )


    const draw = async ( canvas, params ) => {
      const CANVAS_PADDING = 50 // px

      // Размеры канвы (соотношение, как соотношение "стены")
      const canvasWidth = canvas.getBoundingClientRect().width
      canvas.width = canvasWidth // Проставляем атрибут явно (так надо)
      const canvasHeight = canvas.height = canvasWidth

      const CANVAS_STAND_SPACE = canvasWidth - CANVAS_PADDING * 2 // px - допустимое место для стенда

      // Масштаб (мм vs. px)
      let scale = 1
      if ( params.standWidth > params.standHeight ) {
        scale = CANVAS_STAND_SPACE / params.standWidth // px/mm
      } else {
        scale = CANVAS_STAND_SPACE / params.standHeight
      }

      const context = canvas.getContext( '2d' )



      const drawBackground = async ( params ) => {
        // TODO: Добавить картинку для фона
        context.fillStyle = 'rgba( 245, 245, 220, 1 )'
        context.fillRect( 0, 0, canvasWidth, canvasHeight )

        const image = await loadImage( params.backgroundImage )
        context.drawImage( image, 0, 0, canvasWidth, canvasHeight )
      }
      const drawStand = ( params ) => {
        // TODO: Добавить картинку для стенда
        const standX = ( canvasWidth - params.standWidth * scale ) / 2
        const standY = ( canvasHeight - params.standHeight * scale ) / 2

        // Тень
        context.shadowColor = "#888"
        context.shadowBlur = 15

        context.fillStyle = 'rgb( 250, 250, 250 )'
        context.fillRect( standX, standY, params.standWidth * scale, params.standHeight * scale )

        // Сбрасываем тень
        context.shadowColor = "none"
        context.shadowBlur = 0
      }

      const drawHead = ( params ) => {
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

        // текст шапки
        context.font = "24px sans-serif"
        const headTextWidth = context.measureText( params.headText ).width

        const headTextX = headX + params.standWidth * scale / 2 - headTextWidth / 2
        const headTextY = headY + HEAD_HEIGHT * scale - 24 / 2 // Размер шрифта / 2
        context.fillText( params.headText, headTextX, headTextY, params.standWidth * scale )
      }

      const drawPockets = ( params ) => {
        const POCKETS_PADDING = 50 // mm
        // TODO: Добавить проверку помещается ли ряд карманов
        const standX = ( canvasWidth - params.standWidth * scale ) / 2 // px
        const standY = ( canvasHeight - params.standHeight * scale ) / 2
        const rowWidth = params.pocketsCountInRow * sizes[ state.pocketSize ].width + POCKETS_PADDING * ( params.pocketsCountInRow + 1 ) // mm
        const rowsHeight = params.pocketsRowsCount * sizes[ state.pocketSize ].height + POCKETS_PADDING * ( params.pocketsRowsCount + 1 ) // mm
        const rowStartX = standX + ( ( ( params.standWidth - rowWidth ) / 2 )  + POCKETS_PADDING ) * scale // px

        // Ряды
        for ( let row = 0; row < params.pocketsRowsCount; row++ ) {
          const rowStartY = standY + ( ( ( params.standHeight - rowsHeight ) / 2 )  + POCKETS_PADDING  + row * ( sizes[ state.pocketSize ].height + POCKETS_PADDING ) ) * scale  // px

          // Карманы в ряду
          for ( let pocket = 0; pocket < params.pocketsCountInRow; pocket++ ) {
            context.fillStyle = 'rgb( 100, 200, 150 )'
            context.fillRect( rowStartX + pocket * ( ( sizes[ state.pocketSize ].width * scale ) + POCKETS_PADDING * scale ), rowStartY, sizes[ state.pocketSize ].width * scale, sizes[ state.pocketSize ].height * scale )
          }
        }
      }

      const drawSize = ( params ) => {
        // Shadow
        context.shadowColor = "#888"
        context.shadowBlur = 15

        // Triangle
        context.fillStyle = "#fff"
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
        context.rotate( (-rotateAngle * Math.PI) / 180 )
        context.fillText( sizesText, -centeredRotatedXDelta, rotatedYDelta - 10 )

        context.resetTransform()
        // Сбрасываем тень
        context.shadowColor = "none"
        context.shadowBlur = 0
      }

      const drawUps = () => {}

      // Рисуем
      await drawBackground( params )
      drawStand( params )
      drawHead( params )
      drawPockets( params )
      drawSize( params )
      drawUps( params )
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
    }
  }
}
</script>

<style scoped>
canvas {
  width: 100%;
}
</style>