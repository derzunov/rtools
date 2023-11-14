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
      <a class="btn btn-outline-danger" v-on:click="reset">Reset</a>
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
        <th scope="col" class="col-md-1"></th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="( filter, index ) in filters"
        :data-index="index"
        :key="index"
        :ref="( element ) => {
          if ( element ) {
            rows[ filter.filter ] = element
          }
        }"
    >
      <td class="_gray" style="width: 50px;">-</td>
      <th>
        <router-link v-if="filter.filter.length" :to="`/semantic/edit?f=${ filter.filter }`">
          {{ filter.filter }}
        </router-link>
        <router-link v-else :to="`/semantic/edit?f=${ filter.filter }`">
          #
        </router-link>
      </th>
      <td>
        <a target="_blank" :href="`https://r-color.ru/catalog/naklejki/?f=${ filter.filter }`">{{ filter.title }}</a>

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
      <td class="center">0</td>
      <td></td>
      <td>
<!--        <span v-on:click="deleteFilter( filter.filter )"-->
<!--              class="center"-->
<!--              style="color: red; cursor: pointer; font-weight: bold"-->
<!--        >-->
<!--          x-->
<!--        </span>-->

        <ModalUniversal modalId="delete_filter"
                        title="Подтверждение удаления фильтра"
                        actionButtonText="Удалить"
                        cancelButtonText="Отменить"
                        :action="() => { deleteFilter( filter.filter ) }"
                        ref="deleteFilterModalRef"
        >
          <div style="text-align: left;">
            Уверены, что хотите удалить фильтр?
          </div>

          <template #trigger>

            <button @click.prevent="() => { return }"
                    class="btn btn-danger"
                    title="удалить"
                    style="margin: 0">
              удалить
            </button>

          </template>

        </ModalUniversal>

      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios"
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, onBeforeUpdate } from 'vue'
import { filterActiveItems, filterItemsByType, sleep } from '@/utils'
import ModalUniversal from '@/components/ModalUniversal'


// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes( 'localhost' ) ?
    'http://localhost' :
    'https://r-color.ru'

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
    const rows = ref( {} ) // для якорных ссылок, заполняются в шаблоне

    const deleteFilterModalRef = ref( null )

    const fetchFilters = async () => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/read-files.php`
      const response = await axios.get( reqString )
      filters.value = response.data
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
      const response = await axios.get( reqString )
      sleep( 500 )
      fetchFilters()
      console.log( response )
    }

    onBeforeUpdate(() => {
      rows.value = []
    } )

    onMounted( async () => {
      await fetchFilters()

      console.table( rows.value )

      anchor( rows.value[ route.query.f ]  )
    } )

    return {
      // vars
      urlsPopulated,
      urls,
      urlsTitles,
      filters,
      rows,
      deleteFilterModalRef,

      // methods
      goToNews,
      filterActiveItems,
      filterItemsByType,
      getItemsInteractCount,
      reset,
      deleteFilter,
    }
  }
}
</script>
