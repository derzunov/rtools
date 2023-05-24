<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-12">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <router-link
              style="padding-left: 10px;
              color: black; text-decoration: none;"
              to="/prices"
              title="К списку прайс-листов">
            /прайсы</router-link>
          <span>/{{ header }} </span>
        </h5>
      </div>
    </div>

    <form id="add-price" class="mb-3 col-md-12" action="#">
      <table style="width: 100%;">
        <tr>
          <td>
            <div class="mb-3" style="display: flex; justify-content: space-between;">
              <div>
                <h5>
                  <span class="_gray">{{ file_name + '.html' }}</span>

                  <router-link :to="`/prices/content/${ file_name }`" title="Редактировать">
                    <font-awesome-icon
                        style="width: 22px; height: 22px;"
                        :icon="['fas', 'pencil-square']"
                    />
                  </router-link>
                </h5>
              </div>
              <div>
                <div>

                  <h5><span class="_gray">{{ R.find( R.propEq( 'id', group ) )( groups )?.name }}</span></h5>
                  <p><span class="_gray">
                    {{
                      R.find( R.propEq( 'id', subgroup ) )(
                          R.find( R.propEq( 'id', group ) )( groups )?.subgroups
                      ).name
                    }}
                  </span></p>
                </div>
              </div>
            </div>
          </td>
          <td style="width: 100px;"></td>
        </tr>
      </table>

    </form>

    <table style="width: 100%;">
      <tr>
        <td>
          <!-- Превью таблицы -->
          <div class="mb-3" v-html="htmlResultString"></div>
        </td>
        <td style="width: 100px;"></td>
      </tr>
    </table>

    <ToastUniversal toastId="price-list_saved"
                    ref="toastSavedRef">
      Изменения прайс-листа сохранены
    </ToastUniversal>
  </div>
</template>

<script>
import axios from "axios"
import * as R from "ramda"
import { onBeforeMount, ref } from 'vue'
import {
  useRoute,
} from 'vue-router'
import {
  random,
  parsePriceToHtml,
  addRow,
  deleteRow,
  tracer,
} from '@/utils'
import pluralize from 'pluralizr'

import ToastUniversal from '@/components/ToastUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  components: {
    ToastUniversal,
  },

  setup() {
    const route = useRoute()
    const isDev = ref( IS_DEV )
    const isSaving = ref( false )

    // Price refs and vars -------------------------------------------
    const file_name = ref( 'Загружается' )
    const header = ref( 'Загружается' )
    const toptext = ref( 'Загружается' )
    const bottomtext = ref( 'Загружается' )
    const htmlResultString = ref( '' )
    const markup_factor = ref( 1.5 ) // Коэфициент наценки, на  него будет умножаться цена из файла-выгрузки из 1C
    const change_threshold = ref( 5 ) // Порог изменения цены из 1с в %, при превышении которого будем помечать прайс подлежащиим пересчёту
    const one_s_codes = ref( 'Загружается' ) // Строка связанных кодов 1с. Если какой-то из них элемент прайса выгрузки 1с изменится, по каждому прайсу будет оповещён владелец

    const group = ref( 2 ) // index in array
    const groups = ref( [ { id: 'default value', name: 'empty' } ] )

    const subgroup = ref( 0 ) // Index in array

    const updatedate = ref( 0 )


    // Table object
    const table = ref({
      thead: {
        rows: [
          {
            id: Date.now() + random( 100 ),
            cols: [ 'Загружается', 'Загружается', 'Загружается' ],
          },
          {
            id: Date.now() + random( 100 ),
            cols: [ 'Загружается', 'Загружается', 'Загружается' ],
          },
        ]
      },
      rows: [
        {
          id: Date.now() + random( 100 ),
          cols: [ 'Загружается', 'Загружается', 'Загружается' ],
        },
      ],
    })

    const toastSavedRef = ref( null )

    // Functions: -------------------------------------------------------

    const fetchPriceList = async () => {
      const response = await axios.get( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }` )

      file_name.value = response.data.file_name
      header.value = response.data.header
      toptext.value = response.data.toptext
      bottomtext.value = response.data.bottomtext
      markup_factor.value = response.data.markup_factor
      change_threshold.value = response.data.change_threshold
      one_s_codes.value = response.data.one_s_codes
      group.value = response.data.group
      subgroup.value = response.data.subgroup
      updatedate.value = response.data.updatedate

      // Table object
      table.value = response.data.table
    }

    const makePriceObject = () => {
      return {
        file_name: file_name.value,
        header: header.value,
        toptext: toptext.value,
        table: table.value,
        bottomtext: bottomtext.value,
        markup_factor: markup_factor.value,
        change_threshold: change_threshold.value,
        one_s_codes: one_s_codes.value,
        group: group.value, // Enum - индекс группы в массиве групп для фильтрации и сортировки
        subgroup: subgroup.value,
        updateDate: updatedate.value,
      }
    }

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    onBeforeMount( async () => {
      await fetchGroups()
      tracer.debug( 'Groups fetched' )
      await fetchPriceList()
      tracer.info( 'Price fetched' )
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )
      tracer.error( 'html result built' )
    })

    return {
      isDev,
      isSaving,

      table,
      file_name,
      header,
      toptext,
      bottomtext,
      htmlResultString,
      markup_factor,
      change_threshold,
      one_s_codes,
      groups,
      group,
      subgroup,
      toastSavedRef,

      // Functions
      pluralize,
      addRow,
      deleteRow,
      route,

      // Utils
      R,
      tracer,
    }
  }
}
</script>
