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
          <i>{{ chengedCodeItem.one_s_code }}</i>
          -
          {{ chengedCodeItem.value?.split( ';' )[ 1 ] }}
          -
          <b>{{ chengedCodeItem.old_price }} ₽</b>
          <span> => </span>
          <b>{{ chengedCodeItem.current_price }} ₽</b>
          <span> | </span>
          <b :class="{
            _red: chengedCodeItem.percents > 0,
            _green: chengedCodeItem.percents < 0 }"
          >{{ chengedCodeItem.percents }}%</b>
          <span> - </span>
          <span @click.prevent="() => deleteChangedOneSPriceByCode( chengedCodeItem.one_s_code )" class="">
            <font-awesome-icon
                class="edit-control edit-control_danger"
                @click="() => { return 0; }"
                :icon="['fas', 'xmark']"
            />
          </span>
        </li>
      </ul>

      <!-- Нет в наличии -->
      <h5>Нет на складе</h5>
      <ul>
        <li class="mb-3"
            v-for="( outItem ) in outOfStock"
            :key="outItem.id"
        >
          <i>{{ outItem.one_s_code }}</i>
          -
          {{ outItem.value?.split( ';' )[ 1 ] }}
<!--          - -->
<!--          <b>{{ outItem.value?.split( ';' )[ 5 ] }} ₽</b>-->
        </li>
      </ul>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { tracer } from '@/utils'

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
      changedPrice.value = []
      outOfStock.value = []
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

    // Depricated
    const saveOneSChangedCodes = async () => {
      const reqStr = `${ BASE_URL }/tools/price/`
      const formData = new FormData()

      formData.append( 'action', 'codes' )
      formData.append( 'codes', JSON.stringify( {...changedPrice, ...outOfStock } ) )

      await axios.post( reqStr, formData )
    }

    const deleteChangedOneSPriceByCode = async ( one_s_changed_code ) => {
      tracer.debug( `deleteChangedOneSPriceByCode called: Delete price with one_s_changed_code: ${ one_s_changed_code })` )

      const requestUrl = `${ BASE_URL }/tools/price/`
      await axios.delete( requestUrl, {
        data: new URLSearchParams( { one_s_changed_code } )
      } )
      fetchOneSChangedCodes()
    }

    onMounted( async () => {
      await fetchOneSChangedCodes()
    } )

    return {
      BASE_URL,
      changedPrice,
      outOfStock,

      saveOneSChangedCodes,
      deleteChangedOneSPriceByCode,
    }
  }
}
</script>
