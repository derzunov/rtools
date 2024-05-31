<template>
  <div class="row ipm-top-menu">
    <div class="col-md-3">
      <h5>
        <router-link style="color: black" to="/" title="На главную">
          <font-awesome-icon :icon="['fas', 'house']" />
        </router-link>
      </h5>
    </div>
    <div class="col-md-3"></div>
    <div class="col-md-6 right">

      <input
          style="
            width: 120px;
            display: inline-block;
            height: 36px;
            vertical-align: middle;
            border-radius: 7px;
            border: 1px solid;"
          placeholder="Поиск"
          type="text"
          v-model="filterBy" >

      <!-- Начально будет выбрано второе значение -->
      <select style="
            width: 120px;
            display: inline-block;
            height: 36px;
            vertical-align: middle;
            border-radius: 7px;
            border: 1px solid;"
          v-model="selectedAtomicFilter">
        <option selected value="">Все</option>
        <option v-for="atomicFilter in atomicFilters" :value="atomicFilter.furl" :key="atomicFilter.furl">{{atomicFilter.furl}}</option>
      </select>


      <ModalUniversal modalId="delete_filters"
                      title="Подтверждение удаления всех фильтров"
                      actionButtonText="Удалить"
                      cancelButtonText="Отменить"
                      :action="() => { reset() }"
      >
        <div style="text-align: left;">
          Уверены, что хотите удалить все фильтры?
        </div>

        <template #trigger>

          <button @click.prevent="() => { return }"
                  class="btn btn-outline-danger"
                  title="reset"
                  style="margin: 0">
            Reset
          </button>

        </template>

      </ModalUniversal>

<!--      <a class="btn btn-outline-danger" v-on:click="reset">Reset</a>-->
      <router-link class="btn btn-outline-success" to="/semantic/add">Добавить</router-link>
      <router-link class="btn btn-outline-warning" to="/semantic/backups">Бэкапы</router-link>
    </div>
  </div>
  <h5>Наклейки - семантика фильтров</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col" class="col-md-2" style="width: 50px;">i</th>
        <th scope="col" class="col-md-4">Filter</th>
        <th scope="col" class="col-md-2 center">Title + Description</th>
        <th scope="col" class="col-md-2 center">H1 + Subheader</th>
        <th scope="col" class="col-md-2 center">H2 + HTML</th>
        <th scope="col" class="col-md-4 center">Статистика</th>
        <th scope="col" class="col-md-4">Комментарий</th>
        <th scope="col" class="col-md-1"></th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="( filter, index ) in filtersFiltered"
        :data-index="index"
        :key="index"
        :ref="( element ) => {
          if ( element ) {
            rows[ filter.filter ] = element
          }
        }"
    >
      <td class="_gray" style="width: 50px;">{{index + 1}}</td>
      <th>
        <router-link v-if="filter.filter.length" :to="`/semantic/edit?f=${ filter.filter }`">
          {{ filter.filter }}
        </router-link>
        <router-link v-else :to="`/semantic/edit?f=${ filter.filter }`">
          #
        </router-link>
      </th>
      <td>
        <div>
          <b>{{ filter.title }}</b>
        </div>
        <div :title="filter.description">
          {{ filter.description.slice( 0, TEXT_LENGTH_CUT ) + '...' }}
        </div>
      </td>
      <td>
        <div>
          <a target="_blank" v-if="filter.filter" :href="`https://r-color.ru/catalog/naklejki/?f=${ filter.filter }`">{{ filter.h1 }}</a>
          <a target="_blank" v-if="!filter.filter" :href="`https://r-color.ru/catalog/naklejki/`">{{ filter.h1 }}</a>
        </div>
        <div :title="filter.subheader">{{ filter.subheader.slice( 0, TEXT_LENGTH_CUT ) + '...' }}</div>
      </td>
      <td>
        <div>
          <a target="_blank" v-if="filter.filter" :href="`https://r-color.ru/catalog/naklejki/?f=${ filter.filter }#rc_h2`">{{ filter.h2 }}</a>
          <a target="_blank" v-if="!filter.filter" :href="`https://r-color.ru/catalog/naklejki/#rc_h2`">{{ filter.h2 }}</a>
        </div>
        <div :title="filter.html" v-html="filter.html.slice( 0, TEXT_LENGTH_CUT ) + '...'" ></div>
      </td>
      <td class="center">0</td>
      <td></td>
      <td>
<!--        <span v-on:click="deleteFilter( filter.filter )"-->
<!--              class="center"-->
<!--              style="color: red; cursor: pointer; font-weight: bold"-->
<!--        >-->
<!--          x-->
<!--        </span>-->
        <button @click.prevent="() => { deleteFilter( filter.filter ) }"
                class="btn btn-danger"
                title="удалить"
                style="margin: 0">
          удалить
        </button>

      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios"
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, onBeforeUpdate, watch } from 'vue'
import { filterActiveItems, filterItemsByType, sleep } from '@/utils'
import ModalUniversal from '@/components/ModalUniversal'


// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes( 'localhost' ) ?
    'http://localhost' :
    'https://r-color.ru'

const TEXT_LENGTH_CUT = 100

export default {
  components: {
    ModalUniversal,
  },
  setup() {
    const router = useRouter()
    const route = useRoute()

    const urlsPopulated = ref( {} )
    const urls = ref( [] )
    const urlsTitles = ref( {} )
    const filters = ref( [] )
    const filterBy = ref( null )
    const filtersFiltered = ref( [] )
    const atomicFilters = ref( [] )
    const selectedAtomicFilter = ref( '' )
    const rows = ref( {} ) // для якорных ссылок, заполняются в шаблоне

    const deleteFilterModalRef = ref( null )

    const fetchFilters = async () => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/read-files.php`
      const response = await axios.get( reqString )
      filters.value = response.data
      filtersFiltered.value = { ...response.data }
    }

    const fetchAtomicFilters = async () => {
      try {
        const reqStr = `${ BASE_URL }/tools/catalog-admin/naklejki/filters/filters.json`
        const response = await axios.get( reqStr )

        const filters = []

        response.data.forEach( ( category ) => {
          category.filters.forEach( ( filter ) => {
            filters.push( filter )
          } )
        } )

        return filters
      } catch( error ) {
        console.error( error )
        return []
      }
    }

    const getItemsInteractCount = ( items = [] ) => {
      let count = 0;
      items.forEach( ( item ) => {
        count += item?.interacts
      } )
      return count
    }

    const goToNews = ( urlId ) => {
      router.push( `news/${ urlId }` )
    }


    const anchor = ( element ) => {

      console.table( rows.value  )

      if( element ) {
        element.style.backgroundColor = 'lightgray'
        window.scrollTo( 0, element.offsetTop )
      }
    }

    const reset = async () => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/delete-files.php`
      const response = await axios.get( reqString )
      sleep( 500 )
      filters.value = response.data
    }

    const deleteFilter = async ( filter ) => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/delete-filter.php?f=${ filter }`
      alert(`Вы удаляете фильтр ${ filter }`);
      const response = await axios.get( reqString )
      sleep( 500 )
      fetchFilters()
      console.log( response )
    }

    watch([filterBy], ()  => {
      console.log(filterBy.value);
      filtersFiltered.value = [];
      filters.value.forEach( ( filter ) => {
        if ( filter.filter.includes( filterBy.value ) ) {
          filtersFiltered.value.push(filter);
        }
      } )
    } )

    watch([selectedAtomicFilter], ()  => {
      filterBy.value = selectedAtomicFilter.value
    } )

    onBeforeUpdate(() => {
      rows.value = []
    } )

    onMounted( async () => {
      await fetchFilters()
      atomicFilters.value = await fetchAtomicFilters()

      console.log( '====================' )
      console.table( atomicFilters.value )
      console.log( '====================' )
      console.table( rows.value )

      anchor( rows.value[ route.query.f ]  )
    } )

    return {
      // vars
      urlsPopulated,
      urls,
      urlsTitles,
      filters,
      filterBy,
      filtersFiltered,
      atomicFilters,
      selectedAtomicFilter,
      rows,
      deleteFilterModalRef,

      // methods
      goToNews,
      filterActiveItems,
      filterItemsByType,
      getItemsInteractCount,
      reset,
      deleteFilter,

      // constants
      TEXT_LENGTH_CUT,
    }
  }
}
</script>

<style scoped>
.table {
  font-size: 14px;
}
</style>
