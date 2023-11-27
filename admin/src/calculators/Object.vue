<script setup>

import {reactive} from "vue";

let props = defineProps( {
  idExport: Number,
  imageExport: String,
  nameExport: String,
  extExport: String,
  sizeXExport: Number,
  sizeYExport: Number,
})

const dataImage = reactive({
  openPanel: 'object-hidden',
  openPanelButton: 'hidden',
  countImage: 1,
  widthImage: props.sizeXExport,
  heightImage: props.sizeYExport,
})




const plusFunc = (variable, factor) => {
  dataImage[variable] += 1 * factor;
}

const minusFunc = (variable, factor) => {
  if (dataImage[variable] >= 0) {
    dataImage[variable] -= 1 * factor;
  }
}

const panelView = () => {
  if (dataImage.openPanel == 'object-hidden') {
    dataImage.openPanel = 'object-showed';
  } else {
    dataImage.openPanel = 'object-hidden';
  }
  if (dataImage.openPanelButton == 'hidden') {
    dataImage.openPanelButton = 'showed';
  } else {
    dataImage.openPanelButton = 'hidden';
  }
}



</script>

<template>
  <div class="object" :class=dataImage.openPanel>
    <div class="info-panel">
      <div class="img-image">
        <img id="picture" :src="imageExport">
      </div>
      <div class="desc">
        <div class="img-name" :class="idExport">
          <p :title="nameExport">{{ nameExport }}</p>
        </div>
      </div>
      <div class="data-image">
        <p class="file-extension"><slot>{{ extExport }}</slot></p>
        <button class="open-options" @click="panelView"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Options.svg" style="width: 20px;"></button>
      </div>
    </div>
    <div id="options-panel">
      <div class="big-image"><img id="big-picture" :src="imageExport"></div>
      <div class="option-input">
        <div class="option">
          <span id="option">Длина (мм): </span>
          <div id="var-buttons">
            <button @click="minusFunc('widthImage', 10)" class="var-button var-minus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Minus.svg" style="width: 15px;"></button>
            <input v-model="dataImage.widthImage" id="var-text">
            <button @click="plusFunc('widthImage', 10)" class="var-button var-plus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Plus.svg" style="width: 15px;"></button>
          </div>
        </div>
        <div class="option">
          <span id="option">Ширина (мм): </span>
          <div id="var-buttons">
            <button @click="minusFunc('heightImage', 10)" class="var-button var-minus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Minus.svg" style="width: 15px;"></button>
            <input v-model="dataImage.heightImage" id="var-text">
            <button @click="plusFunc('heightImage', 10)" class="var-button var-plus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Plus.svg" style="width: 15px;"></button>
          </div>
        </div>
        <div class="option">
          <span id="option">Количество: </span>
          <div id="var-buttons">
            <button @click="minusFunc('countImage', 1)" class="var-button var-minus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Minus.svg" style="width: 15px;"></button>
            <input v-model="dataImage.countImage" id="var-text">
            <button @click="plusFunc('countImage', 1)" class="var-button var-plus"><img id="optionsIMG" :class=dataImage.openPanelButton src="../assets/Plus.svg" style="width: 15px;"></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .object {
    display: flex;
    width: 100%;
    margin-bottom: -1px;
    flex-direction: column;
    background-color: white;
    overflow: hidden;
    transition-duration: .2s;
    border: 1px solid var(--gray-color);
  }

  .object-hidden {
    height: 80px;
  }

  .object-showed {
    height: 260px;
  }

  .info-panel {
    display: flex;
    flex: auto;
  }

  .img-image {
    flex: auto;
    width: 60px;
    max-height: 60px;
    margin-top: 10px;
    margin-left: 10px;
    display: flex;
  }

  #picture {
    max-width: 60px;
    max-height: 60px;
    border-radius: 5px;
    margin: auto;
  }

  .desc {
    flex: auto;
    height: 80px;
    display: flex;
    flex-direction: column;
    font-family: 'Open Sans', sans-serif;
    font-size: 1rem;
  }

  .img-name {
    margin-top: 10px;
    margin-left: 10px;
    font-family: 'Open Sans', sans-serif;
    font-size: 1em;
    height: 40px;
    width: 230px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3em;
  }

  .img-size {
    margin-bottom: 8px;
    margin-left: 10px;
  }

  .data-image {
    display: flex;
    flex-direction: column;
  }

  .file-extension {
    display: flex;
    width: 40px;
    height: 25px;
    background-color: var(--light-gray-color);
    border: 1px solid var(--gray-color);
    border-radius: 5px;
    justify-content: center;
    align-items: center;
    align-self: flex-start;
    margin-right: 10px;
    margin-top: 10px;
    font-family: 'Open Sans', sans-serif;
    font-size: 1rem;
  }

  .open-options {
    align-self: flex-end;
    margin-right: 10px;
    margin-top: 15px;
  }

  .open-options .hidden {
    transition-duration: .2s;
    transform: rotate(0deg);
  }

  .open-options .showed {
    transition-duration: .2s;
    transform: rotate(180deg);
  }



  #options-panel {
    display: flex;
    flex-direction: row;
  }

  .big-image {
    display: flex;
    margin-left: 10px;
    margin-top: 25px;
    margin-right: 10px;
    height: 120px;
    width: 120px;
    border: 1px solid gray;
    border-radius: 6px;
  }

  #big-picture {
    max-width: 100%;
    max-height: 100%;
    border-radius: 5px;
    margin: auto;
  }

  .option-input {
    display: flex;
    flex: auto;
    flex-direction: column;
    background-color: var(--light-gray-color);
    height: 180px;
  }

  .option {
    display: flex;
    flex-direction: row;
    margin-left: 15px;
    margin-top: 25px;
  }

  .option #option {
    font-family: 'Open Sans', sans-serif;
    font-size: 1rem;
    width: 110px;
  }

  #var-buttons {
    display: flex;
    flex-direction: row;
    margin-left: 4px;
    margin-top: -1px;
  }

  #var-buttons .var-button {
    width: 26px;
    height: 26px;
    background-color: white;
    border: 1px solid gray;
    display: flex;
  }

  .var-button #optionsIMG {
    margin: auto;
  }

  #var-buttons #var-text {
    font-family: 'Open Sans', sans-serif;
    font-size: 1rem;
    text-align: center;
    width: 50px;
    height: 26px;
    border: 1px solid gray;
  }

  #var-buttons .var-minus {
    border-radius: 3px 0 0 3px;
    margin-right: -1px;
  }

  #var-buttons .var-plus {
    border-radius: 0 3px 3px 0;
    margin-left: -1px;
  }

</style>