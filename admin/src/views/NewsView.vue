<template>
  <div class="row ipm-top-menu">
    <div class="col-md-10">
      <h5 class="_nowrap">
        <router-link style="color: black" to="/" title="На главную">
          <font-awesome-icon :icon="['fas', 'house']" />
        </router-link>
        <a :href="currentUrl"
           target="_blank"
           style="padding-left: 10px; color: black; text-decoration: none;"
        >
          {{ currentUrl }}
        </a>
      </h5>
    </div>
    <div class="col-md-2 center">
      <router-link class="btn btn-outline-success" :to="{ name: 'add', params: { currentUrl: currentUrl } }">Добавить</router-link>
    </div>
  </div>

  <h5>Список кампаний</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Заголовок</th>
        <th scope="col">Текст</th>
        <th scope="col">Перейти на</th>
        <th scope="col">Начало</th>
        <th scope="col">Окончание</th>
        <th scope="col" class="center">Клики</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="(newItem, index) in news"
        :data-index="index"
        :key="newItem.id"
        :class="{ 'campaign-item_disabled': !isItemActive( newItem ) }"
    >
      <td class="_gray">{{ newItem.id }}</td>
      <td>{{ newItem.title }}</td>
      <td>{{ newItem.description }}</td>
      <td>
        <a target="_blank" :href="newItem.link">{{ newItem.link }}</a>
      </td>
      <td class="_nowrap">{{ newItem.date_start }}</td>
      <td class="_nowrap">{{ newItem.date_end }}</td>
      <td class="_nowrap center">{{ newItem?.interacts }}</td>
      <td class="col-md-1 center">
        <h5 style="text-align: center">
          <font-awesome-icon
              class="edit-control"
              @click="() => { edit( newItem.id ) }"
              :icon="['fas', 'pen-to-square']"
          />
          <font-awesome-icon
              class="edit-control edit-control_danger"
              @click="() => { deleteNew( currentUrl, newItem.id ) }"
              :icon="['fas', 'xmark']"
          />
        </h5>
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios"
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { isItemActive } from '@/utils'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes('localhost') ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  setup() {
    const route = useRoute()
    const router = useRouter()

    const currentUrl = ref('/')
    const urls = ref([])
    const news = ref([])

    const fetchNews = async () => {
      const url = `${ BASE_URL }/tools/ipm/?url=${ currentUrl.value }&type=news&action=all`
      const response = await axios.get(url)
      news.value = response.data
    }

    const fetchUrls = async () => {
      const url = `${ BASE_URL }/tools/ipm/?type=urls&action=all`
      const response = await axios.get( url )
      urls.value = response.data
    }

    const deleteNew = async ( url, id ) => {
      console.info( `Delete new from url ${ url } with id ${ id }` )

      const requestUrl = `${ BASE_URL }/tools/ipm/`
      await axios.delete(requestUrl, {
        data: new URLSearchParams( { url, id, type: 'news' } )
      })
      await fetchNews()
    }

    const edit = ( itemId ) => {
      router.push( `/ipm/edit/${ route.params.passedUrlId }/${ itemId }` )
    }

    onMounted( async () => {
      await fetchUrls()
      currentUrl.value = urls.value[ route.params.passedUrlId ]
      await fetchNews()
    } )

    watch( [ currentUrl ], () => {
      fetchNews()
    } )

    return {
      currentUrl,
      urls,
      news,

      deleteNew,
      edit,
      isItemActive,
    }
  }
}
</script>
