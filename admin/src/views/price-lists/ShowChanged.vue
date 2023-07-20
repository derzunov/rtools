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

      <div class="right">
        <span class="form-check-label">Изменившиеся цены в прайсах </span>
        <span style="display: inline-block" class="form-check form-switch">
          <input v-model="isShowAll"
                 class="form-check-input"
                 type="checkbox"
                 id="show-all-changes-checkbox"
                 style="cursor: pointer;"
          >
          <label class="form-check-label" for="show-all-changes-checkbox">Все изменившиеся цены</label>
        </span>
      </div>


      <!-- Позиции с изменившейся ценой -->
      <h5 class="bold">Изменения цен</h5>
      <table width="100%" class="table center mb-5 b-changes-table">
        <thead>
          <tr>
            <th class="left">
              Код 1с
            </th>
            <th class="left">
              Наименование
            </th>
            <th>
              Ед. Изм.
            </th>
            <th class="right">
              Старая цена
            </th>
            <th class="right">
              Новая цена
            </th>
            <th>
              %
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody
            v-for="( chengedCodeItem ) in changedPrice"
            :key="chengedCodeItem.one_s_code"
            :class="{ '_green': chengedCodeItem.is_all_actualized && !isShowAll }"
            @click="() => { chengedCodeItem.is_expanded = !chengedCodeItem.is_expanded }"
        >
          <tr
              class="mb-3 b-changes-table__row"
              style="cursor: pointer;"
          >
            <td class="left">
              <i>{{ chengedCodeItem[ 'one_s_code' ] }}</i>
            </td>
            <td class="left">
              {{ chengedCodeItem.value?.split( ';' )[ 1 ] }}
            </td>

            <td class="_gray">
              {{ chengedCodeItem.value?.split( ';' )[ 3 ] }}
            </td>

            <td class="right">
              <b style="cursor: context-menu;" :title="new Date( chengedCodeItem.old_date ).toLocaleDateString()">
                {{ chengedCodeItem.old_price }}
              </b>
            </td>

            <td class="right">
              <b style="cursor: context-menu;" :title="new Date( chengedCodeItem.date ).toLocaleDateString()">
                {{ chengedCodeItem.current_price }}
              </b>
            </td>

            <td>
              <b :class="{
                _red: chengedCodeItem.percents > 0,
                _green: chengedCodeItem.percents < 0 }"
              >{{ chengedCodeItem.percents }}%</b>
            </td>
            <td>
              <!-- Модалка подтверждения удаления -->
              <ModalUniversal :modalId="`delete_position_${ chengedCodeItem.one_s_code }`"
                              title="Подтверждение удаления"
                              actionButtonText="Удалить"
                              :action="() => deleteChangedOneSPriceByCode( chengedCodeItem.one_s_code )"
              >
                <template #trigger>

                  <span>
                    <font-awesome-icon
                        class="edit-control edit-control_danger"
                        @click="() => { return 0 }"
                        :icon="[ 'fas', 'xmark' ]"
                    />
                  </span>
                </template>
                <div style="text-align: left;">
                  <p>Подтвердите удаление позиции:</p>
                  <p>{{ chengedCodeItem.value?.split( ';' )[ 1 ] }}</p>
                </div>
              </ModalUniversal>
            </td>
          </tr>
          <!-- Связанные неактуализированные прайс-листы -->
          <tr
              style="font-style: italic; font-size: 16px;"
              v-for="( relatedNotActualizedPriseList ) in chengedCodeItem.related_not_actualized_price_lists"
              :key="relatedNotActualizedPriseList.header"
              v-show="chengedCodeItem.is_expanded"
          >
            <td style="border: none" class="right">Прайс:</td>
            <td style="border: none"  class="left">
              <router-link :to="`/prices/show/${ relatedNotActualizedPriseList[ 'file_name' ] }`" title="Открыть прайс">
                {{ relatedNotActualizedPriseList[ 'header' ] }}
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Нет в наличии -->
      <h5 class="bold">Нет на остатках:</h5>
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
import { onBeforeMount, watch, ref } from 'vue'
import { tracer } from '@/utils'
import ModalUniversal from '@/components/ModalUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {
  components: {
    ModalUniversal,
  },

  setup() {
    const isShowAll = ref( false ) // Показать все изменившиеся цены или только упомянутые в прайсах
    const changedPrice = ref( [] )
    const outOfStock = ref( [] )

    const fetchOneSChangedCodes = async () => {
      let reqStr = `${ BASE_URL }/tools/price/?action=changed`
      if ( !isShowAll.value ) {
        reqStr = `${ BASE_URL }/tools/price/?action=related-changed`
      }

      const response = await axios.get( reqStr )
      if ( response.data ) {
        // Обнуляем списки
        changedPrice.value = []
        outOfStock.value = []

        // Обновляем списки
        for ( const codeItem of Object.values( response.data ) ) {

          if ( codeItem.value[ 0 ] === '?' ) {
            outOfStock.value.push( codeItem )
          } else {

            // Получаем список связанных неактуализированных прайс-листов
            let relatedNotActualizedPriceLists = []

            // Ходим на сервак и заполняем массив неактуализированных прайс-листов
            // только для списка позиций, имеющих связанные прайс-листы
            // Для общего списка позиций оставим этот массив пустым
            // (Снижаем нагрузку на сервак в общем списке)
            // (Если понадобится, сможем предусмотреть новый эндпоинт, который будет сразу популейтить неактуализированные прайс-листы)
            if ( !isShowAll.value ) {
              const req = `${ BASE_URL }/tools/price/?action=related-not-actualized&one_s_code=${ codeItem[ 'one_s_code' ] }`
              const response = await axios.get( req )
              relatedNotActualizedPriceLists = response.data
            }

            // Проставляем для изменившейся позиции признак актуализированности всех связанных прайс-листов
            // (по принципу "если список неактуализированных пуст, значит все актуализированы")
            codeItem.is_all_actualized = !relatedNotActualizedPriceLists.length
            codeItem.related_not_actualized_price_lists = relatedNotActualizedPriceLists

            // Раскрыт ли список связанных неактуализированных прайс-листов ()
            codeItem.is_expanded = ref( false )

            changedPrice.value.push( codeItem )
          }
        }
      }
    }

    const deleteChangedOneSPriceByCode = async ( one_s_changed_code ) => {
      tracer.debug( `deleteChangedOneSPriceByCode called: Delete price with one_s_changed_code: ${ one_s_changed_code })` )

      const requestUrl = `${ BASE_URL }/tools/price/`
      await axios.delete( requestUrl, {
        data: new URLSearchParams( { one_s_changed_code } )
      } )
      await fetchOneSChangedCodes()
    }

    onBeforeMount( async () => {
      await fetchOneSChangedCodes()
    } )

    watch( [ isShowAll ], () => {
      fetchOneSChangedCodes()
    } )

    return {
      BASE_URL,
      isShowAll,
      changedPrice,
      outOfStock,

      deleteChangedOneSPriceByCode,
    }
  }
}
</script>

<style scoped>
  .b-changes-table {}
  .b-changes-table .edit-control {
    visibility: hidden;
  }

  .b-changes-table__row:hover .edit-control {
    visibility: visible;
  }
</style>
