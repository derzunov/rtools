<template>
  <h4>
    Конструктор стендов
  </h4>
  <div class="row stands-constructor">
    <div class="col-md-6 stands-constructor__controls">

      <div class="input-group mb-3">
        <span class="input-group-text">Ширина</span>
        <input type="number" v-model="state.standWidth" class="form-control" id="stand_width">
        <span class="input-group-text">Высота</span>
        <input type="number" v-model="state.standHeight" class="form-control" id="stand_width">
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text">Количество в ряду</span>
        <input type="number" v-model="state.pocketsCountInRow" class="form-control" id="stand_width">
        <span class="input-group-text">Количество рядов</span>
        <input type="number" v-model="state.pocketsRowsCount" class="form-control" id="stand_width">
      </div>

    </div>
    <div class="col-md-6 stands-constructor__canvas">
      <canvas ref="canvas"></canvas>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted } from 'vue'

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
  },
  setup( props ) {

    const state = reactive( {
      standWidth: props.standWidth || 1000,
      standHeight: props.standHeight || 1000,
      pocketsCountInRow: props.pocketsCountInRow || 3,
      pocketsRowsCount: props.pocketsRowsCount || 2,
    } )

    const canvas = ref( null )

    const draw = ( canvas, params ) => {
      const CANVAS_PADDING = 50 // px

      const REAL_WALL_WIDTH = 2000 // 2 метра
      const REAL_WALL_HEIGHT = 2000 // 2 метра

      // Размеры канвы (соотношение, как соотношение "стены")
      const canvasWidth = canvas.getBoundingClientRect().width
      canvas.width = canvasWidth // Проставляем атрибут явно (так надо)
      // Высоту считаем исходя из соотношения ширина/высота реальной стены
      const canvasHeight = canvas.height = canvasWidth * REAL_WALL_HEIGHT / REAL_WALL_WIDTH

      const CANVAS_STAND_SPACE = canvasWidth - CANVAS_PADDING * 2 // px - допустимое место для стенда

      // Масштаб (мм vs. px)
      let scale = 1
      if ( params.standWidth > params.standHeight ) {
        scale = CANVAS_STAND_SPACE / params.standWidth // px/mm
      } else {
        scale = CANVAS_STAND_SPACE / params.standHeight
      }

      const context = canvas.getContext( '2d' )

      const drawBackground = () => {
        // TODO: Добавить картинку для фона
        context.fillStyle = 'rgba( 245, 245, 220, 1 )'
        context.fillRect( 0, 0, canvasWidth, canvasHeight )
      }
      const drawStand = () => {
        console.log( canvasWidth, canvasHeight )
        console.log( params )
        console.log( scale )

        const standX = ( canvasWidth - params.standWidth * scale ) / 2
        const standY = ( canvasHeight - params.standHeight * scale ) / 2

        context.fillStyle = 'rgb( 200, 200, 200 )'
        context.fillRect( standX, standY, params.standWidth * scale, params.standHeight * scale )
      }
      const drawSize = () => {
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

        // Reset transformation matrix to the identity matrix
        context.setTransform( 1, 0, 0, 1, 0, 0 )
      }
      const drawUps = () => {}

      // Рисуем
      drawBackground()
      drawStand()
      drawSize()
      drawUps()
    }

    onMounted( () => {
      draw( canvas.value, state )
    } )

    watch( [ state ], () => {
      draw( canvas.value, state )
    },{
      flush: 'post'
    } )

    return {
      canvas,
      state,
    }
  }
}
</script>

<style scoped>
canvas {
  width: 100%;
}
</style>
