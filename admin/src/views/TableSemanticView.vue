<template>
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
      <router-link class="btn btn-outline-success" to="/semantic/add">Добавить</router-link>
    </div>
  </div>
  <h5>Наклейки</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col" class="col-md-2" style="width: 50px;">i</th>
        <th scope="col" class="col-md-4">Filter</th>
        <th scope="col" class="col-md-2 center">Title</th>
        <th scope="col" class="col-md-2 center">Description</th>
        <th scope="col" class="col-md-2 center">H1</th>
        <th scope="col" class="col-md-2 center">Html</th>
        <th scope="col" class="col-md-4">Статистика</th>
        <th scope="col" class="col-md-4">Комментарий</th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="( filter, index ) in filters"
        :data-index="index"
        :key="index">
      <td class="_gray" style="width: 50px;">-</td>
      <th>
        <router-link :to="`/semantic/edit?f=${ filter.filter }`">
          {{ filter.filter }}
        </router-link>
      </th>
      <td>
        {{ filter.title }}
      </td>
      <td class="center">
        {{ filter.description }}
      </td>
      <td class="center">
        {{ filter.h1 }}
      </td>
      <td class="center">
        <span v-html="filter.html" ></span>
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios"
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { filterActiveItems, filterItemsByType } from '@/utils'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes( 'localhost' ) ?
    'http://localhost' :
    'https://r-color.ru'

export default {
  setup() {
    const router = useRouter()

    const urlsPopulated = ref( {} )
    const urls = ref( [] )
    const urlsTitles = ref( {} )
    const filters = ref( [] )

    const fetchUrlsPopulated = async () => {
      const reqString = `${ BASE_URL }/tools/ipm/?type=urls&action=all&populate=true`
      const response = await axios.get( reqString )
      urlsPopulated.value = response.data
    }
    const fetchFilters = async () => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/read-files.php`
      const response = await axios.get( reqString )
      filters.value = response.data
    }

    const fetchUrls = async () => {
      const reqString = `${ BASE_URL }/tools/ipm/?type=urls&action=all`
      const response = await axios.get( reqString )
      urls.value = response.data
    }

    // Работает только на бою
    // Для работы с localhost нужно настраивать CORS
    const getUrlTitle = async ( url ) => {
      if ( url === '*' ) {
        return 'All pages'
      }

      try {
        const response = await axios.get( `${ BASE_URL }\\${ url }` )
        const matches = response.data.match( /<title>(.*?)<\/title>/ )
        if ( matches && matches.length ) {
          return matches[ 1 ]
        }
      } catch( error ) {
        console.error( error )
      }

      return "Нет тайтла"
    }

    const fillUrlsTitles = () => {
      urls.value.forEach( async ( url ) => {
        const title = await getUrlTitle( url )
        urlsTitles.value[ url ] = title.replace( ' || РЕСПУБЛИКА ЦВЕТА', '' )
      } )
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

    onMounted( async () => {
      await fetchFilters()
      await fetchUrlsPopulated()
      await fetchUrls()
      await fillUrlsTitles()
    } )

    return {
      // vars
      urlsPopulated,
      urls,
      urlsTitles,
      filters,

      // methods
      goToNews,
      filterActiveItems,
      filterItemsByType,
      getItemsInteractCount,
    }
  }
}
</script>
