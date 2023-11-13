<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
        </h5>
        <h6><router-link class="" to="/semantic/table">Наклейки</router-link></h6>
      </div>
      <div class="col-md-7"></div>
      <div class="col-md-2 center">
      </div>
    </div>

    <!-- Придумать заголовок -->
    <h5>...</h5>

    <form @submit.prevent="create" id="ipm-add-new" class="mb-5 col-md-8" action="#">
      <div class="mb-3">
        <label for="filter" class="form-label _gray">
          Запрос кластера<br>
        </label>
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon3">{{ baseHost }}/tools/{{ catalogProduct }}/</span>

          <select v-model="catalogProduct" style="border-color: rgb(206, 212, 218);">
            <option selected></option>
            <option v-for="product in products" :key="product" :value="product">{{ product }}</option>
          </select>

          <div class="field_tooltip">
            <p v-for="product in products" :key="product" class="field_tooltip__item">
              {{ product }}
            </p>
          </div>

          <span class="input-group-text" id="basic-addon4">/?f=</span>

<!--          <select class="form-control" v-model="selectedFilters" style="border-color: rgb(206, 212, 218); width: 250px;">-->
<!--            <option selected></option>-->
<!--            <option v-for="filter in filters" :key="filter.id" :value="filter.furl">{{ filter.furl }}</option>-->
<!--          </select>-->

<!--          <div class="input-group mb-3" >-->
            <select v-for="select in selectedFilters" :key="select.id" class="form-control" v-model="select.value" style="border-color: rgb(206, 212, 218); width: 250px;">
              <option v-for="filter in filters" :key="filter.id" :value="filter.furl">{{ filter.furl }}</option>
            </select>
<!--          </div>-->
          <button @click.prevent="addFilter" type="button" class="btn btn-success">+</button>

        </div>
      </div>

      <div class="mb-3">
        <label for="title" class="form-label _gray">
          Title
        </label>
        <input required
               id="title"
               name="title"
               class="form-control"
               type="text"
               v-model="title"
        >
      </div>

      <div class="mb-3">
        <label for="description" class="form-label _gray">
          Description
        </label>
        <textarea required
               id="description"
               name="description"
               class="form-control"
               v-model="description"
        >
        </textarea>
      </div>

      <div class="mb-3">
        <label for="h1" class="form-label _gray">
          H1
        </label>
        <input required
               id="h1"
               name="h1"
               class="form-control"
               type="text"
               v-model="h1"
               placeholder="H1 для фильтра"
        >
      </div>

      <div class="mb-3">
        <label for="subheader" class="form-label _gray">
          subheader
        </label>
        <input required
               id="subheader"
               name="subheader"
               class="form-control"
               type="text"
               v-model="subheader"
               placeholder="Subheader для фильтра"
        >
      </div>

      <div class="mb-3">
        <label for="h2" class="form-label _gray">
          H2
        </label>
        <input required
               id="h2"
               name="h2"
               class="form-control"
               type="text"
               v-model="h2"
               placeholder="H2 для калькулятора"
        >
      </div>

      <div class="mb-3">
        <label for="html" class="form-label _gray">
          HTML
        </label>
        <textarea v-model="html" id="html" required class="form-control" placeholder="HTML"></textarea>
      </div>

      <div class="mb-3">
        <label for="isindex" class="form-label _gray" style="margin-right: 10px;">
          Индексировать
        </label>
        <input id="isindex"
               name="isindex"
               type="checkbox"
               v-model="isindex"
        >
      </div>

      <button type="submit" :disabled="isSaving" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
</template>

<style>
  .field_tooltip {
    position: absolute;
    /* пока не используем, возможно пригодится */
    display: none;

  }
  .field_tooltip__item {
    margin: 0;
  }
</style>

<script>
import axios from "axios"
import { onMounted, ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  random,
  sleep,
} from '@/utils'
import pluralize from 'pluralizr'

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

  setup() {
    const router = useRouter()
    const route = useRoute()
    const isDev = ref( IS_DEV )
    const baseUrl = ref(`${ window.location.protocol }//${ window.location.host }`)
    const baseHost = ref(`${ window.location.host }`)
    const isSaving = ref( false )
    // Form values:
    const filter = ref( '' )
    const selectedFilters = ref( [] )
    const catalogProduct = ref( '' )
    const description = ref( '' )
    const h1 = ref( '' )
    const subheader = ref( '' )
    const isindex = ref( false )
    const h2 = ref( '' )
    const html = ref( '' )
    const title = ref( '' )
    const link = ref( '' )
    const dateStart = ref( '' )
    const dateEnd = ref( '' )
    const currentVariant = ref( 0 ) // Enum
    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Загружается' ] )

    const products = ref( [] )
    const filters = ref( [] )

    if ( route.params.currentUrl ) {
      filter.value = route.params.currentUrl
    }

    if ( isDev.value ) {
      catalogProduct.value = `naklejky`
      description.value = `Дескрипшн для фильтра ${ random( 255 ) }`
      h1.value = 'H1 для фильтра/фильтров'
      subheader.value = 'Subheader для фильтра/фильтров'
      isindex.value = false
      h2.value = 'H2 для фильтра/фильтров'
      html.value = 'Семантичный <b>HTML</b>'
      title.value = 'Title для фильтра'
    }

    const create = async () => {

      const formdata = new FormData()

      const selectedFiltersValues = []
      selectedFilters.value.forEach( ( selectedFilter ) => {
        if ( selectedFilter.value.length ) {
          selectedFiltersValues.push( selectedFilter.value )
        }
      } )

      formdata.append( "filter", selectedFiltersValues.sort().join( '__' ).trim() )
      formdata.append( "catalog", catalogProduct.value )
      formdata.append( "title", title.value )
      formdata.append( "description", description.value )
      formdata.append( "h1", h1.value )
      formdata.append( "subheader", subheader.value )
      formdata.append( "h2", h2.value )
      formdata.append( "html", html.value )
      formdata.append( "isindex", isindex.value )

      isSaving.value = true
      await axios.post( `${ BASE_URL }/tools/catalog-admin/`, formdata )
      // ---------------------------------------------------------------------------
      await sleep( 500 )
      isSaving.value = false
      await router.push( `/semantic/table?f=${ selectedFiltersValues.sort().join( '__' ) }` )
    }

    const fetchProducts = async () => {
      const reqStr = `${ BASE_URL }/tools/catalog-admin/products.json`
      const response = await axios.get( reqStr )
      return response.data
    }

    const fetchFilters = async ( product ) => {
      try {
        const reqStr = `${ BASE_URL }/tools/catalog-admin/${ product.value }/filters/filters.json`
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

    const onFilterChange = ( event ) => {
      console.log( event.target.value )
    }

    const addFilter = () => {
      selectedFilters.value.push( { id: Date.now(), value: '' } )
    }

    onMounted(async () => {
      products.value = await fetchProducts()
    } )

    watch( [ catalogProduct ], async () => {
      filters.value = await fetchFilters( catalogProduct )
    } )

    return {
      isDev,
      baseUrl,
      baseHost,
      isSaving,
      filter,
      selectedFilters,
      catalogProduct,
      description,
      h1,
      subheader,
      h2,
      html,
      isindex,
      title,
      link,
      dateStart,
      dateEnd,
      currentVariant,
      variants,

      products,
      filters,

      create,
      pluralize,

      onFilterChange,
      addFilter,
    }
  }
}
</script>
