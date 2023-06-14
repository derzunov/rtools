<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-10">
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
      <div class="col-md-2 right">
        <router-link class="btn btn-outline-success" :to="`/prices/content/${ file_name }`" title="Редактировать">
          Редактировать
        </router-link>
      </div>
    </div>

    <div class="mb-3" style="float: right; padding: 10px 0 0 0;">
      <h5 class="_gray">
        {{ groupName }} / {{ subgroupName }}
      </h5>
    </div>

    <!-- Превью таблицы -->
    <div class="mb-3" v-html="htmlResultString"></div>

    <div class="mb-3 right" style="font-size: 0.8rem;">
      Дата сохранения
      <b style="cursor: pointer;" @click="goToPriceList( file_name )">{{ file_name }}.html</b>
      : {{ new Date( update_date ).toLocaleString() }}
    </div>

    <hr>

    <!-- Блок обновившихся связанных кодов 1с -->

    <table v-if="!is_actualized" width="100%">
      <!-- Изменилась цена -->
      <tr class="mb-3" v-if="relatedChangedPrices?.length" style="vertical-align: top;">
        <td><h5>Изменения цен:</h5></td>
        <td>

          <table width="100%">
            <tr v-for="changedPrice in relatedChangedPrices"
                :key="changedPrice.one_s_code">

              <td>{{ changedPrice.one_s_code }}</td>
              <td>{{ changedPrice.value.split( ';' )[ 1 ] }}</td>
              <td>
                <router-link target="_blank" style="color: black; text-decoration: none;" to="/prices/changed" title="Перейти к изменениям цен">
                  <b>{{ changedPrice.old_price }} ₽</b>
                </router-link>
                <span> => </span>
                <router-link target="_blank" style="color: black; text-decoration: none;" to="/prices/changed" title="Перейти к изменениям цен">
                  <b>{{ changedPrice.current_price }} ₽</b>
                </router-link>
              </td>

              <td>
                <b :class="{ _red: changedPrice.percents > 0, _green: changedPrice.percents < 0 }"
                > {{ changedPrice.percents }} % </b>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr v-if="relatedChangedPrices?.length">
        <td>
          <hr>
        </td>
        <td>
          <hr>
        </td>
      </tr>

      <!-- Нет на складе -->
      <tr class="mb-3" v-if="relatedOutOfStock?.length" style="vertical-align: top;">
        <td><h5>Нет на остатках:</h5></td>
        <td>
          <table width="100%">
            <tr v-for="changedPrice in relatedOutOfStock"
                :key="changedPrice.one_s_code">

              <td>{{ changedPrice.one_s_code }}</td>
              <td>{{ changedPrice.value.split( ';' )[ 1 ] }}</td>
              <td>
                <b>{{ changedPrice.old_price }} ₽</b>
              </td>

            </tr>
          </table>
        </td>
      </tr>

      <tr v-if="relatedOutOfStock?.length">
        <td>
          <hr>
        </td>
        <td>
          <hr>
        </td>
      </tr>
    </table>
    <table width="100%">
      <!-- Комментарий -->
      <tr style="vertical-align: top;">
        <td>
          <h5>Комментарий:</h5>
        </td>
        <td>
          {{ admin_comment }}
        </td>
      </tr>

    </table>
    <br>
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

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {
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
    const need_update = ref( false )

    const group = ref( 0 ) // index in array
    const groups = ref( [ { id: 'default value', name: 'empty' } ] )
    const subgroup = ref( 0 ) // Index in array

    const groupName = ref( '' )
    const subgroupName = ref( '' )

    const update_date = ref( 0 )
    const admin_comment = ref( '' )

    const is_actualized = ref( true )
    const actualized_date = ref( null )

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

    // 1s changes
    const relatedChangedPrices = ref( [] )
    const relatedOutOfStock = ref( [] )

    // Functions: -------------------------------------------------------

    const fetchPriceList = async () => {
      tracer.debug( 'fetchPriceList called' )
      const response = await axios.get( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }` )

      file_name.value = response.data.file_name
      header.value = response.data.header
      toptext.value = response.data.toptext
      bottomtext.value = response.data.bottomtext
      markup_factor.value = response.data.markup_factor
      change_threshold.value = response.data.change_threshold
      one_s_codes.value = response.data.one_s_codes
      need_update.value = response.data?.need_update
      group.value = response.data.group
      subgroup.value = response.data.subgroup
      update_date.value = response.data.update_date
      admin_comment.value = response.data.admin_comment || 'no comments yet'

      is_actualized.value = response.data.is_actualized
      actualized_date.value = response.data.actualized_date

      const _getGroup = () => {
        return groups.value.filter( groupItem => groupItem.id === group.value )[ 0 ]
      }
      const getGroupName = () => {
        return _getGroup()?.name
      }
      const getSubroupName = () => {
        return _getGroup()?.subgroups?.filter( subgroupItem => subgroupItem.id === subgroup.value )[ 0 ]?.name
      }

      groupName.value = getGroupName()
      subgroupName.value = getSubroupName()

      // Table object
      table.value = response.data.table
    }

    const makePriceObject = () => {
      tracer.debug( 'makePriceObject called' )

      // Если прайс не актуализирован и имеются измененные цены связанных 1с-кодов, подставляем иконку с восклицательным знаком
      let signedHeader = header.value
      if ( !is_actualized.value && ( relatedChangedPrices.value.length || relatedOutOfStock.value.length ) ) {
        signedHeader = '<svg class="svg-inline--fa fa-triangle-exclamation" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="triangle-exclamation" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path class="" fill="currentColor" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32z"></path></svg> ' + signedHeader
      }

      return {
        file_name: file_name.value,
        header: signedHeader,
        toptext: toptext.value,
        table: table.value,
        bottomtext: bottomtext.value,
        markup_factor: markup_factor.value,
        change_threshold: change_threshold.value,
        one_s_codes: one_s_codes.value,
        need_update: need_update.value,
        group: group.value, // Enum - индекс группы в массиве групп для фильтрации и сортировки
        subgroup: subgroup.value,
        update_date: update_date.value,
        admin_comment: admin_comment.value,
        is_actualized: is_actualized.value,
        actualized_date: actualized_date.value,
      }
    }

    const fetchGroups = async () => {
      tracer.debug( 'fetchGroups called' )
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    const fetchRelatedOneSChangedCodes = async () => {
      tracer.debug( 'fetchRelatedOneSChangedCodes called' )
      const reqStr = `${ BASE_URL }/tools/price/?action=changed`
      const response = await axios.get( reqStr )
      if ( response.data ) {

        one_s_codes.value.split( ';' ).forEach( ( relatedCode ) => {

          // Есть ли связанный с прайсом код в объекте изменившихся кодов
          if ( response.data[ relatedCode.trim() ] ) {

            // Отсутствует на складе?
            if ( response.data[ relatedCode.trim() ].value[ 0 ] === '?' ) {
              relatedOutOfStock.value.push( response.data[ relatedCode.trim() ] )
            } else {
              // Или просто изменилась цена
              if ( Math.abs( response.data[ relatedCode.trim() ].percents ) >= change_threshold.value ) {
                // И изменения превысили заданный в прайсе порог
                relatedChangedPrices.value.push( response.data[ relatedCode.trim() ] )
              }
            }
          }
        } )
      }
    }

    const goToPriceList = ( file_name ) => {
      window.open( `${ BASE_URL }/tools/price/db/price-lists/html/${ file_name }.html`, '_blank' ).focus()
    }

    onBeforeMount( async () => {
      await fetchGroups()
      // tracer.debug( 'Groups fetched' )
      await fetchPriceList()
      // tracer.info( 'Price fetched' )
      await fetchRelatedOneSChangedCodes()
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )
      // tracer.error( 'html result built' )
    })

    return {
      isDev,
      isSaving,

      table,
      file_name,
      update_date,
      header,
      toptext,
      bottomtext,
      htmlResultString,
      markup_factor,
      change_threshold,
      one_s_codes,
      admin_comment,
      groups,
      group,
      subgroup,
      toastSavedRef,
      relatedChangedPrices,
      relatedOutOfStock,
      groupName,
      subgroupName,

      is_actualized,

      // Functions
      pluralize,
      addRow,
      deleteRow,
      route,
      goToPriceList,

      // Utils
      R,
      tracer,
    }
  }
}
</script>

<style>
  .b-price-table__update-date {
    display: none;
  }
</style>
