<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
        </h5>
      </div>
      <div class="col-md-7"></div>
      <div class="col-md-2 right">
        <router-link class="btn btn-outline-success" to="/prices/add">
          Добавить
        </router-link>
      </div>
    </div>
    <h5>Прайс листы</h5>

    <div class="mb-3" style="display: flex; justify-content: flex-start;">
      <label style="padding: 7px 5px 0 0;">Стадия:</label>
      <select style="width: 300px; margin: 0 5px 0 0;" class="form-select" name="js_stage" id="js_stage" v-model="stage">
        <option value="-1">все</option>
        <option v-for="stageItem in stages"
                :value="stageItem.id"
                :key="stageItem.id"
        >
          {{ stageItem.name }}
        </option>
      </select>
      <label style="padding: 7px 5px 0 0;">Группа:</label>
      <select class="form-select" style="width: 280px; margin: 0 5px 0 0;" name="group" id="group" v-model="groupId">
        <option selected="selected" value="all">Все</option>
        <option v-for="groupItem in groups"
                :value="groupItem.id"
                :key="groupItem.id"
        >
          {{ groupItem.name }}
        </option>
      </select>
      <div>
          <select v-model="subgroupId" v-if="currentGroup.subgroups?.length" class="form-select" name="subgroup" id="subgroup">
            <option selected="selected" value="all">Все подгруппы</option>
            <template v-if="groupId !== 'all'">

              <option v-for="subgroupItem in currentGroup.subgroups"
                      :value="subgroupItem.id"
                      :key="subgroupItem.name"
              >
                {{ subgroupItem.name }}
              </option>
            </template>
          </select>
      </div>
    </div>

    <!--  -->
    <table class="table b-prices-table">
      <thead>
      <tr>
        <th class="col-md-1"></th>
        <th scope="col" class="col-md-2">
          <font-awesome-icon
              style="cursor:pointer;"
              @click="toggleAlphaSort"
              v-if="isSortAsc"
              :icon="['fas', 'sort-asc']" />
          <font-awesome-icon
              style="cursor:pointer;"
              @click="toggleAlphaSort"
              v-if="!isSortAsc"
              :icon="['fas', 'sort-desc']" />
          Группа
        </th>
        <th scope="col" class="col-md-2">Подгруппа</th>
        <th scope="col" class="col-md-2">Продукт</th>
        <th scope="col" class="col-md-2">Имя файла</th>
        <th scope="col" class="col-md-2">Комментарий</th>
        <th scope="col" class="col-md-2">Стадия</th>
        <th scope="col" class="col-md-1 right"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="( priceList, index ) in allPriceLists"
          :data-index="index"
          :key="index"
          class="b-prices-table__row"
          :class="{ 'b-prices-table__row_need-update': priceList.needRecalculate }"
      >
        <td>
          <router-link style="text-decoration: none; color: black;" v-if="priceList.needRecalculate" :to="`/prices/show/${ priceList[ 'file_name' ] }`" title="Требуется пересчёт цен">
            <font-awesome-icon :icon="['fas', 'exclamation-triangle']" />
          </router-link>
        </td>
        <td >
          <span
              @click="() => { chooseGroupByClickOnPriceGroup( priceList ) }"
              style="cursor: pointer;"
          >
            {{ R?.find( R.propEq( 'id', priceList[ 'group' ] ) )( groups ).name  }}
          </span>
        </td>
        <td>
          {{
            R?.find( R.propEq( 'id', priceList[ 'subgroup' ] ) )(
                R?.find( R.propEq( 'id', priceList[ 'group' ] ) )( groups ).subgroups
            ).name
          }}
        </td>
        <td>
          <router-link :to="`/prices/show/${ priceList[ 'file_name' ] }`" title="Открыть прайс">
            {{ priceList[ 'header' ] }}
          </router-link>
        </td>
        <td>
          <span @click.prevent="() => { goToPriceList( priceList[ 'file_name' ] ) }"
                class="_gray"
                style="cursor: pointer;"
                title="Открыть html"
          >
             {{ priceList[ 'file_name' ] }}
          </span>
        </td>

        <td>
          <span class="_gray" :title="priceList[ 'admin_comment' ]?.substring( 0, 500 ) ">
             {{ priceList[ 'admin_comment' ]?.substring( 0, 30 ).trim() + '...' }}
          </span>
        </td>
        <td>{{ ( priceList[ 'stage' ] === -1 || priceList[ 'stage' ] === '-1' ) ? 'не указано' : ( R?.find( R.propEq( 'id', priceList[ 'stage' ] ) )( stages ))?.name }}</td>
        <td class="right">
          <a href="#"
             title="Редактировать контент прайс-листа"
             class="_gray b-prices-table__edit-control"
             style="vertical-allign: baseline;"
             @click.prevent="() => { goToEditContent( priceList[ 'file_name' ] ) }">
            <font-awesome-icon
                style="width: 22px; height: 22px;"
                :icon="['fas', 'pencil-square']"
            />
          </a>

          <!-- Удаление прайса -->
          <ModalUniversal :modalId="`delete_price_${ priceList[ 'file_name' ] }`"
                          title="Подтверждение удаления прайс-листа"
                          actionButtonText="Удалить"
                          :action="() => { deletePrice( priceList[ 'file_name' ] ) }"
          >
            <template #trigger>
              <a href="#"
                 class="_gray b-prices-table__edit-control"
                 style="vertical-allign: baseline; margin-left: 7px;"
                 title="Удалить прайс-лист"
                 @click.prevent="() => { return }">
                <font-awesome-icon
                    style="width: 22px; height: 22px;"
                    :icon="['fas', 'times']"
                />
              </a>
            </template>

            <div style="text-align: left;">
              Вы уверены,что хотите удалить прайс-лист {{ priceList[ 'file_name' ] }}?
              Данные будут потеряны.
            </div>
          </ModalUniversal>
        </td>
      </tr>
      </tbody>
    </table>

  </div>

</template>

<script>
import axios from "axios"
import * as R from "ramda"
import { onMounted, ref, watch } from 'vue'
import {
  useRouter,
} from 'vue-router'
import pluralize from 'pluralizr'

import ModalUniversal from '@/components/ModalUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru' // TODO: вынести в константы конфига,
// TODO: либо, если мы предполагаем совместную поставку сервиса с админкой,
// TODO: Можно забрать BASE_URL регуляркой:
// TODO: /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/
// TODO: Тоже в отдельной утиле, поставляющей константы/конфиги

export default {

  components: {
    ModalUniversal,
  },

  setup() {
    const router = useRouter()
    const isDev = ref( IS_DEV )
    const isSaving = ref( false )

    const isSortAsc = ref( false )

    // Price refs and vars -------------------------------------------
    const fileName = ref( 'price-name_1' )
    const header = ref( 'Заголовок прайс-листа' )
    const toptext = ref( 'Текст перед таблицей прайса' )
    const bottomtext = ref( 'Текст после таблицы прайса' )
    const htmlResultString = ref( '' )
    const markupFactor = ref( 1.5 ) // Коэфициент наценки, на  него будет умножаться цена из файла-выгрузки из 1C
    const one_s_codes = ref( '00-00002340;00-00000252;00-00000215' ) // Строка связанных кодов 1с. Если какой-то из них элемент прайса выгрузки 1с изменится, по каждому прайсу будет оповещён владелец

    const groupId = ref( 'all' ) // Real Id in db or `all`
    const groups = ref( [ 'Загружается' ] ) // Real groups array with populated subgroups (will be fetched)
    const currentGroup = ref( {} ) // Chosen group object with subroups

    const subgroupId = ref( 'all' ) // Real Id in db or `all`
    const subgroups = ref( [ 'Загружается' ] )

    const allPriceLists = ref( [] )
    const changedPriceCodes = ref( {} ) // Object

    const stages = ref( [] )
    const stage = ref( -1 )


    // Functions: -------------------------------------------------------

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }
    const fetchStages = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=stages`
      const response = await axios.get( reqStr )
      stages.value = response.data
    }

    const setCurrentGroupById = ( groupId ) => {

      // Группа "Все"
      if ( groupId === 'all' ) {
        return currentGroup.value = {
          name: 'Все',
          id: groupId,
          subroups: [ { name: 'Сначала выберите группу', id: 'all' } ]
        }
      }
      const foundedGroup = R.find( R.propEq( 'id', groupId ) )( groups.value )
      if ( foundedGroup ) {
        // По просьбе Дениса сделано так, что при смене группы устанавливаем значение подгруппы в all
        subgroupId.value = 'all'

        // Сортировка списка подгрупп
        foundedGroup.subgroups.sort( ( a, b ) => {
          return ( a.name < b.name ) ? -1 : 1
        } )

        return currentGroup.value = foundedGroup
      } else {
        console.error( `DE: setCurrentGroupById: Group with passed id (${ groupId }) not found in:` )
        console.table( groups.value )
        return currentGroup.value
      }
    }

    const setNeedUpdate = ( priceList ) => {

      // Если прайс помечен как актуальный, то и нечего дальше по кодам ходить
      if ( priceList.is_actualized ) return false

      priceList.one_s_codes.split( ';' ).every( ( code ) => {
        if ( changedPriceCodes.value[ code.trim() ] &&
            ( ( Math.abs( changedPriceCodes.value[ code.trim() ].percents ) >= priceList.change_threshold ) ||
            ( changedPriceCodes.value[ code.trim() ]?.value[ 0 ] === '?' ) )
        ) {
          priceList.needRecalculate = true
          // Stop the loop
          return false
        }
        return true
      } )
    }
    const fetchOneSChangedCodes = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=changed`
      const response = await axios.get( reqStr )
      if ( response.data ) {
          changedPriceCodes.value = response.data
      }
      return changedPriceCodes
    }

    const fetchAllPriceLists = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=all`
      const response = await axios.get( reqStr )
      allPriceLists.value = response.data

      await fetchOneSChangedCodes()
      allPriceLists.value.forEach( ( priceList ) => {
        setNeedUpdate( priceList )
      } )

    }

    const deletePrice = async ( file_name ) => {
      console.info( `Delete price (name: ${ file_name })` )

      const requestUrl = `${ BASE_URL }/tools/price/`
      await axios.delete( requestUrl, {
        data: new URLSearchParams( { file_name } )
      } )
      await fetchAllPriceLists()
    }

    // Промежуточная переменная с исходным массивом, чтобы при фильтрации allPriceLists.value
    // не растерять данные.
    // Она заполняется из в onMounted ниже
    let allPriceListsCached = [];

    const filterPriceListsByGroup = () => {
      allPriceLists.value = allPriceListsCached.filter( ( priceList ) => {
        return ( priceList[ 'group' ] === groupId.value ) ||
            ( groupId.value === 'all' )
      } )

      // Сортируем по хэдеру прайса
      allPriceLists.value = sortPriceListsByHeader( allPriceLists.value )
    }

    const filterPriceListsByStage = () => {
      allPriceLists.value = allPriceListsCached.filter( ( priceList ) => {
        return ( priceList[ 'stage' ] === stage.value ) ||
            ( stage.value === -1 ) || ( stage.value === '-1' )
      } )

      // Сортируем по хэдеру прайса
      allPriceLists.value = sortPriceListsByHeader( allPriceLists.value )
    }

    const filterPriceListsBySubgroup = () => {
      // Забираем только те подгруппы, которые внутри группы
      filterPriceListsByGroup()

      allPriceLists.value = allPriceLists.value.filter( ( priceList ) => {
        return ( priceList[ 'subgroup' ] === subgroupId.value ) ||
            ( subgroupId.value === 'all' )
      } )

      // Сортируем по хэдеру прайса
      allPriceLists.value = sortPriceListsByHeader( allPriceLists.value )
    }

    const sortPriceListsByHeader = ( priceLists ) => {
      return priceLists.sort( ( a, b ) => {
        return ( a.header < b.header ) ? -1 : 1
      } )
    }

    const goToPriceList = ( filename ) => {
      window.open( `${ BASE_URL }/tools/price/db/price-lists/html/${ filename }.html`, '_blank' ).focus()
    }

    const goToEditContent = ( fileName ) => {
      router.push( `/prices/content/${ fileName }` )
    }

    const goToEditStruct = ( fileName ) => {
      router.push( `/prices/struct/${ fileName }` )
    }

    const toggleAlphaSort = () => {
      isSortAsc.value = !isSortAsc.value
    }

    const sortByGroup = () => {

      // Мы же отобразим весь список в новом порядке,
      // поэтому и группа должна смениться на "все"
      groupId.value = 'all'

      if ( isSortAsc.value ) {
        allPriceLists.value = allPriceListsCached.sort( ( a, b ) => {
          return a.group < b.group ? 1 : -1
        } )
      } else {
        allPriceLists.value = allPriceListsCached.sort( ( a, b ) => {
          return a.group > b.group ? 1 : -1
        } )
      }
    }

    const chooseGroupByClickOnPriceGroup = ( priceList ) => {
      groupId.value = priceList[ 'group' ]
    }

    onMounted( async () => {
      await fetchGroups()
      await fetchStages()
      // await fetchSubgroups()
      setCurrentGroupById( groupId.value )
      await fetchAllPriceLists()
      allPriceListsCached = JSON.parse( JSON.stringify( allPriceLists.value ) )
    } )

    watch( [ stage ], () => {
      filterPriceListsByStage()
    } )

    watch( [ groupId ], () => {
      setCurrentGroupById( groupId.value )
      filterPriceListsByGroup()
    } )

    watch( [ subgroupId ], () => {
      filterPriceListsBySubgroup()
    } )

    watch( [ isSortAsc ], () => {
      sortByGroup()
    } )

    return {
      isDev,
      isSaving,
      isSortAsc,

      fileName,
      header,
      toptext,
      bottomtext,
      htmlResultString,
      markupFactor,
      one_s_codes,
      groups,
      groupId,
      currentGroup,
      subgroups,
      subgroupId,
      allPriceLists,
      stages,
      stage,

      pluralize,
      goToPriceList,
      goToEditContent,
      goToEditStruct,
      toggleAlphaSort,
      deletePrice,

      chooseGroupByClickOnPriceGroup,

      // Tools
      R,
    }
  }
}
</script>

<style>
.form-row button {
  visibility: hidden;
}
.form-row:hover button {
  visibility: visible;
}

.b-prices-table {}

.b-prices-table__row {
  vertical-align: middle;
}
.b-prices-table__row_need-update {
  background: rgba(255, 182, 193, 0.05);
}

.b-prices-table__edit-control {
  visibility: hidden;
}
.b-prices-table__row:hover .b-prices-table__edit-control {
  visibility: visible;
}

</style>
