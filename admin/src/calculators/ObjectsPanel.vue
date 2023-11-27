<!--
TODO
 изучить:
  react.js
  async await (js)
  ES
-->


<template>
  <div class="stickers-calculator">
    <nav>
      <!-- Objects.vue -->
      <div class="objects-field">
        <div class="objects">
          <!-- Example of object -->
          <Item v-for="i in count" :id-export="objects[i-1][0]" :sizeX-export="objects[i-1][1]" :sizeY-export="objects[i-1][2]" :image-export="objects[i-1][3]" :name-export="objects[i-1][4]" :ext-export="objects[i-1][5]"></Item>
        </div>
      </div>

      <div class="input-wrapper">
        <input type="file" id="input-file" style="width: 0;" accept=".jpg, .jpeg, .png" @change="readFileM" multiple>
        <label for="input-file" class="input-styled">
          <span class="icon-input">Загрузить новый стикер</span>
        </label>
      </div>
    </nav>

    <main>
      <div class="border-field" id="border-field">
        <template v-if="list.listY == 0">
          <canvas id="field" :width="10000" :height="list.listX"></canvas>
        </template>
        <template v-else>
          <canvas id="field" :width="list.listY" :height="list.listX"></canvas>
        </template>
      </div>
      <div class="calculator-panel">
        <div class="button-calculator">
          <button id="place-an-image" @click="draw">Рассчитать</button>
          <button id="send-to-prod">Отправить</button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>

import Item from './Object.vue'
import {ref, reactive, onMounted} from "vue";
import { get_pos1, quickSortObj } from "../utils/test";
import {convertPxToMm as pxInMm} from "../utils/pxInMm";
import { multiplyImage } from "../utils/multiplyImage";
import { getSizeX } from "../utils/getSizeX";
//import "../utils/canvas2svg";
// import * as saveSvgAsPng from "https://cdn.skypack.dev/save-svg-as-png@1.4.17";
import { objects } from '../main';



// Информация о холсте
const list = reactive({
  space: ref(5),
  listX: ref(602),
  listY: ref(0),
  outputSVG: ref(false)
})
const count = ref(0)

// Список данных, которые пойдут на обработку
const exportData = ref([])

// Список обработанных данных
const importData = ref([]) // [id.string, positionX:int, positionY:string]

const multiply = ref(0)
const canvasSize = ref([0, 0])
const pxToMm = ref(0)



onMounted(() => {
  multiply.value = multiplyImage(document.getElementById("field").offsetHeight, list.listX);
  canvasSize.value[0] = document.getElementById("field").offsetWidth;
  canvasSize.value[1] = document.getElementById("field").offsetHeight;
  pxToMm.value = 96 / 25.4;
})



const countImages = (multiply, canvasHeight) => {
  let data = []
  for (let i = 0; i < objects.value.length; i++) {
    for (let j = 0; j < objects.value[i][6]; j++) {
      console.log(objects.value[i][1], objects.value[i][2])
      data.push([objects.value[i][0], objects.value[i][1], objects.value[i][2], objects.value[i][3]]);
    }
  }
  return data;
}

const draw = () => {
  console.log('draw called');
  let canvas = document.getElementById("field");
  let ctx = canvas.getContext("2d");
  exportData.value = countImages(multiply, canvas.offsetHeight);
  importData.value = get_pos1(list.space, list.listX, list.listY, exportData.value)
  console.log(importData.value.length)
  if (importData.value.length > 0) { list.outputSVG = true }
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, canvas.width, canvas.height);
  for (let i = 0; i < importData.value.length; i++) {
    let img = new Image();
    img.src = exportData.value[i][3];
    ctx.drawImage(img, importData.value[i][1], importData.value[i][2], exportData.value[i][1], exportData.value[i][2]);
  }

  // for (let i = 0; i < importData.length; i++) {
  //   ctx.fillRect(importData[1][i][0][0], importData[1][i][0][1], importData[1][i][1][0], importData[1][i][1][1]);
  //   ctx.stroke();
  // }
  // console.log(importData)
}

const readFile = (event) => {
  //let inputFile = document.getElementById('input-file');
  return new Promise((resolve, reject) => {
    let reader = new FileReader();
    let imageURL
    reader.onerror = () => reject(new DOMException("Problem parsing input file."))
    reader.onload = () => resolve(imageURL = reader.result)
    reader.onloadend = () => {
      let image = new Image();
      image.src = imageURL;
      image.onload = () => {
        let fileExtension = event.target.files[0]['name'].split('.').at(-1);
        let fileName = event.target.files[0]['name'].slice(0, ((fileExtension.length * -1) - 1));
        exportData.value.push([count.value, sizeX, sizeY]);
        objects.value.push([count.value, sizeX, sizeY, image.src, fileName, fileExtension, 1, '']);
        count.value++;
        //draw();
      }
    }
    reader.readAsDataURL(event.target.files[0])});
}

const readFileM = ( inputFile ) => {
  return new Promise((resolve, reject) => {
    for (let i = 0; i < inputFile.target.files.length; i++) {
      let reader = new FileReader();
      let imageURL
      reader.onerror = () => reject(new DOMException("Problem parsing input file."))
      reader.onload = () => resolve(imageURL = reader.result)
      reader.onloadend = () => {
        let image = new Image();
        image.src = imageURL;
        image.onload = () => {
          let fileExtension = inputFile.target.files[i]['name'].split('.').at(-1);
          let fileName = inputFile.target.files[i]['name'].slice(0, ((fileExtension.length * -1) - 1));
          let sizeY = pxInMm(image.height * multiply.value);
          let sizeX = getSizeX(image.width, image.height, sizeY, multiply.value);
          objects.value.push([count.value, sizeX, sizeY, image.src, fileName, fileExtension, 1, '']);
          console.log(objects.value)
          console.log(multiply.value)
          count.value++;
          //draw();
        }
      }
      reader.readAsDataURL(inputFile.target.files[i])
    }
  });
};

</script>

<style scoped>

.stickers-calculator {
  display: flex;
}

nav {
  width: 400px;
  height: 100vh;
  margin-right: 10px;
  display: flex;
  flex-direction: column;
}

.objects-field {
  overflow-y: scroll;
  display: flex;
  flex-direction: column;
  flex: auto;
  background-color: var(--bg-color);
  border-radius: 10px;
  border: 1px solid var(--gray-color);
  margin-bottom: 10px;
}

.objects {
  display: flex;
  flex-direction: column;
  flex: auto;
}

.input-wrapper {
  align-self: flex-end;
  display: flex;
  margin-top: 10px;
}

.input-styled {
  display: flex;
  background-color: var(--red-color);
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  width: 400px;
  height: 80px;
  cursor: pointer;
}

.icon-input {
  font-family: 'Open Sans', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: white;
}



main {
  display: flex;
  flex-direction: column;
  flex: auto;
  margin-left: 10px;
}

.border-field {
  width: calc(100vw - 420px);
  height: calc(100vh - 100px);
  background-color: var(--bg-color);
  border-radius: 10px;
  border: 1px solid var(--gray-color);
  overflow-x: scroll;
}

#field {
  margin: 20px;
  height: calc(100% - 40px);
  background-color: white;
}

.calculator-panel {
  justify-content: flex-end;
  margin-top: 20px;
  align-self: flex-end;
  display: flex;
  width: 100%;
  height: 80px;
}

.button-calculator {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.button-calculator button {
  border: none;
  font-family: 'Open Sans', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: white;
  height: 30px;
  width: 125px;
  margin-right: 10px;
  margin-top: 3px;
  margin-bottom: 3px;
  background-color: var(--red-color);
  border-radius: 4px;
}

</style>