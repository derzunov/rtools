<template>
  <div class="edit">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
        </h5>
      </div>
      <div class="col-md-7"></div>
      <div class="col-md-2 center"></div>
    </div>
    <h5>Варианты отображения</h5>
    <form method="post"
          id="ipm-edit-variants"
          class="mb-5 col-md-8"
          :action="`${ BASE_URL }/tools/ipm/`">
      <div class="mb-3"
           v-for="( variantItem, variantIndex ) in variants"
           :key="variantIndex"
      >
        <input required
               id="variant"
               name="variants[]"
               class="form-control"
               type="text"
               :value="variantItem"
        >
      </div>

      <input hidden required
             id="type"
             name="type"
             class="form-control"
             type="text"
             value="variants"
      >

      <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  setup() {

    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Стандартный' ] )

    const fetchVariants = async () => {
      const reqStr = `${ BASE_URL }/tools/ipm/?type=variants&action=all`
      const response = await axios.get( reqStr )
      if ( response.data && response.data?.length ) {
        variants.value = response.data
      }
    }

    onMounted( async () => {
      await fetchVariants()
    } )

    return {
      variants,
      BASE_URL,
    }
  }
}
</script>
