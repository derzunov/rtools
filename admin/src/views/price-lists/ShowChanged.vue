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

    <div class="mb-5 col-md-12">

      <!-- Позиции с изменившейся ценой -->
      <h5>Позиции с изменившейся ценой</h5>
      <table width="100%" class="table center mb-5">
        <tbody>
          <tr class="mb-3"
              v-for="( chengedCodeItem ) in changedPrice"
              :key="chengedCodeItem.id"
          >
            <td class="left">
              <i>{{ chengedCodeItem.one_s_code }}</i>
            </td>
            <td>
              {{ chengedCodeItem.value?.split( ';' )[ 1 ] }}
            </td>

            <td class="center">
              {{ chengedCodeItem.value?.split( ';' )[ 3 ] }}
            </td>

            <td>
              <b>{{ chengedCodeItem.old_price }}</b>
            </td>

            <td>
              <b>{{ chengedCodeItem.current_price }} ₽</b>
            </td>

            <td>
              <b :class="{
                _red: chengedCodeItem.percents > 0,
                _green: chengedCodeItem.percents < 0 }"
              >{{ chengedCodeItem.percents }}%</b>
            </td>
            <td>
              <span @click.prevent="() => deleteChangedOneSPriceByCode( chengedCodeItem.one_s_code )" class="">
              <font-awesome-icon
                  class="edit-control edit-control_danger"
                  @click="() => { return 0; }"
                  :icon="['fas', 'xmark']"
              />
            </span>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Нет в наличии -->
      <h5>Нет на остатках:</h5>
      <table class="table">
        <tbody>
          <tr class="mb-3"
              v-for="( outItem ) in outOfStock"
              :key="outItem.id"
          >
            <td>
              <i>{{ outItem.one_s_code }}</i>
            </td>
            <td>
              {{ outItem.value?.split( ';' )[ 1 ] }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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
      const reqStr = `${ BASE_URL }/tools/price/?action=changed`
      const response = await axios.get( reqStr )
      if ( response.data ) {
        // Обнуляем списки
        changedPrice.value = []
        outOfStock.value = []

        // Обновляем списки
        Object.values( response.data ).forEach( ( codeItem ) => {

          if ( codeItem.value[ 0 ] === '?' ) {
            outOfStock.value.push( codeItem )
          } else {
            changedPrice.value.push( codeItem )
          }
        } )
      }
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

      deleteChangedOneSPriceByCode,
    }
  }
}
</script>
