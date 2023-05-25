<template>
  <div class="edit">
    <div class="row ipm-top-menu">
      <div class="col-md-6">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <span>/</span>
          <router-link style="color: black" to="/prices/" title="Прайсы">
            Прайсы
          </router-link>
          <span>
            / Изменения 1с
          </span>
        </h5>
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-2 center"></div>
    </div>

    <form method="post"
          id="ipm-edit-1s"
          class="mb-5 col-md-8"
          :action="`${ BASE_URL }/`"
          @submit.prevent="saveOneSChangedCodes">

      <!-- Позиции с изменившейся ценой -->
      <h5>Позиции с изменившейся ценой</h5>
      <ul>
        <li class="mb-3"
            v-for="( chengedCodeItem ) in changedPrice"
            :key="chengedCodeItem.id"
        >
          {{ chengedCodeItem.value?.split( ';' )[ 1 ] }}
          -
          <b>{{ chengedCodeItem.value?.split( ';' )[ 5 ] }}</b>
        </li>
      </ul>

      <!-- Нет в наличии -->
      <h5>Нет на складе</h5>
      <ul>
        <li class="mb-3"
            v-for="( outItem ) in outOfStock"
            :key="outItem.id"
        >
          {{ outItem.value?.split( ';' )[ 1 ] }}
          -
          <b>{{ outItem.value?.split( ';' )[ 5 ] }}</b>
        </li>
      </ul>
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
    const changedPrice = ref( [] )
    const outOfStock = ref( [] )

    const fetchOneSChangedCodes = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=changed`
      const response = await axios.get( reqStr )
      if ( response.data ) {
        Object.values( response.data ).forEach( ( codeItem ) => {

          if ( codeItem.value[ 0 ] === '?' ) {
            outOfStock.value.push( codeItem )
          } else {
            changedPrice.value.push( codeItem )
          }
        } )
      }
    }

    const saveOneSChangedCodes = async () => {
      const reqStr = `${ BASE_URL }/tools/price/`
      const formData = new FormData()

      formData.append( 'action', 'codes' )
      formData.append( 'codes', JSON.stringify( [...changedPrice, ...outOfStock] ) )

      await axios.post( reqStr, formData )
    }

    onMounted( async () => {
      await fetchOneSChangedCodes()
    } )

    return {
      BASE_URL,
      changedPrice,
      outOfStock,

      saveOneSChangedCodes,
    }
  }
}
</script>
