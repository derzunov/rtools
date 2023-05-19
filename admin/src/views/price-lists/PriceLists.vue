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
      <div>
          <select class="form-select" name="group" id="group" v-model="groupId">
            <option selected="selected" value="all">Все</option>
            <option v-for="groupItem in groups"
                    :value="groupItem.id"
                    :key="groupItem.id"
            >
              {{ groupItem.name }}
            </option>
          </select>
      </div>
<!--      <h1> {{ currentGroup }} </h1>-->
<!--      <h1> {{ subgroupId }} </h1>-->
      <div>
          <select v-if="currentGroup.subgroups?.length" class="form-select" name="subgroup" id="subgroup" v-model="subgroupId">
<!--            <option selected="selected" value="all">Подгруппа не выбрана</option>-->
            <template v-if="groupId !== 'all'">

              <option v-for="subgroupItem in currentGroup.subgroups"
                      :value="subgroupItem.id"
                      :key="subgroupItem.id"
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
        <th scope="col" class="col-md-3">
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
        <th scope="col" class="col-md-3">Подгруппа</th>
        <th scope="col" class="col-md-3">Продукт</th>
        <th scope="col" class="col-md-2">Имя файла</th>
        <th scope="col" class="col-md-2 right"></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="( priceList, index ) in allPriceLists"
          :data-index="index"
          :key="index"
          class="b-prices-table__row"
      >

        <td>
          {{ R.find( R.propEq( 'id', priceList[ 'group' ] ) )( groups ).name  }}
        </td>
        <td>
          {{
            R.find( R.propEq( 'id', priceList[ 'subgroup' ] ) )(
                R.find( R.propEq( 'id', priceList[ 'group' ] ) )( groups ).subgroups
            ).name
          }}
        </td>
        <td>
          <router-link :to="`/prices/show/${ priceList[ 'file_name' ] }`" title="Открыть прайс">
            {{ priceList[ 'header' ] }}
          </router-link>
        </td>
        <td>
          <span href="#" @click.prevent="() => { goToPriceList( priceList[ 'file_name' ] ) }">
             {{ priceList[ 'file_name' ] }}
          </span>
        </td>
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
  // useRoute
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

    //const group = ref( 'all' ) // all или Enum
    const groupId = ref( 'all' ) // Real Id in db or `all`
    const groups = ref( [ 'Загружается' ] ) // Real groups array with populated subgroups (will be fetched)
    const currentGroup = ref( {} ) // Chosen group object with subroups

    //const subgroup = ref( 'all' ) // all или Enum
    const subgroupId = ref( 'all' ) // Real Id in db or `all`
    const subgroups = ref( [ 'Загружается' ] )

    const allPriceLists = ref( [] )

    // Functions: -------------------------------------------------------

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
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
        // если у группы есть подгуппы, устанавливаем id первой из имеющихся, если нет - `all`
        foundedGroup.subgroups?.length ? subgroupId.value = foundedGroup.subgroups[ 0 ].id : subgroupId.value = 'all'
        return currentGroup.value = foundedGroup
      } else {
        console.error( `DE: setCurrentGroupById: Group with passed id (${ groupId }) not found in:` )
        console.table( groups.value )
        return currentGroup.value
      }
    }

    const fetchAllPriceLists = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=all`
      const response = await axios.get( reqStr )
      allPriceLists.value = response.data
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
    }

    const filterPriceListsBySubgroup = () => {
      // Забираем только те подгруппы, которые внутри группы
      filterPriceListsByGroup()

      allPriceLists.value = allPriceLists.value.filter( ( priceList ) => {
        return ( priceList[ 'subgroup' ] === subgroupId.value ) ||
            ( subgroupId.value === 'all' )
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

    onMounted( async () => {
      await fetchGroups()
      // await fetchSubgroups()
      setCurrentGroupById( groupId.value )
      await fetchAllPriceLists()
      allPriceListsCached = JSON.parse( JSON.stringify( allPriceLists.value ) )
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
      // group,
      groupId,
      currentGroup,
      subgroups,
      // subgroup,
      subgroupId,
      allPriceLists,

      pluralize,
      goToPriceList,
      goToEditContent,
      goToEditStruct,
      toggleAlphaSort,
      deletePrice,

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

.b-prices-table__row {}

.b-prices-table__edit-control {
  visibility: hidden;
}
.b-prices-table__row:hover .b-prices-table__edit-control {
  visibility: visible;
}

</style>
