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
      <router-link class="btn btn-outline-success" to="/ipm/add">Добавить</router-link>
    </div>
  </div>
  <h5>Список URL</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col" class="col-md-2">Id</th>
        <th scope="col" class="col-md-4">Url</th>
        <th scope="col" class="col-md-4">Страница</th>
        <th scope="col" class="col-md-2 center">Кол-во</th>
        <th scope="col" class="col-md-2 center">Клики</th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="( url, index ) in urls"
        :data-index="index"
        :key="url">
      <td class="_gray">{{ index }}</td>
      <th>
        <a href="#" @click.prevent="() => { goToNews( index ) }">
          <!-- Показываем длинну только элементов с типо news, для этого filter -->
          {{ url }}
        </a>
      </th>
      <td>
        <a v-if="url !== '*'" :href="url"
           target="_blank"
           title="Посмотреть на бою"
        >
          {{ urlsTitles[ url ] }}
        </a>

        <a v-else href="/"
           target="_blank"
           title="Посмотреть на бою"
        >
          Все страницы
        </a>

      </td>
      <td class="center">
        <a class="url__active-count" href="#" @click.prevent="() => { goToNews( index ) }">
          <!-- Показываем длинну только элементов с типом news -->
          <span>
            {{ filterActiveItems( filterItemsByType( urlsPopulated[ url ], 'news' ) )?.length }}
          </span>
        </a>
        <span class="_gray" > ({{ filterItemsByType( urlsPopulated[ url ], 'news' )?.length }})</span>
      </td>
      <td class="center">
        {{ getItemsInteractCount( filterItemsByType( urlsPopulated[ url ], 'news' ) ) }}
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

    const fetchUrlsPopulated = async () => {
      const reqString = `${ BASE_URL }/tools/ipm/?type=urls&action=all&populate=true`
      const response = await axios.get( reqString )
      urlsPopulated.value = response.data
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
      await fetchUrlsPopulated()
      await fetchUrls()
      await fillUrlsTitles()
    } )

    return {
      // vars
      urlsPopulated,
      urls,
      urlsTitles,

      // methods
      goToNews,
      filterActiveItems,
      filterItemsByType,
      getItemsInteractCount,
    }
  }
}
</script>
