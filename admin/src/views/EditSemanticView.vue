<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
        </h5>
        <h6><router-link class="" :to="`/semantic/table?f=${ currentFilter }`">Наклейки</router-link></h6>
      </div>
      <div class="col-md-7"></div>
      <div class="col-md-2 center">
      </div>
    </div>

    <h5>Редактирование</h5>

    <form @submit.prevent="create" id="ipm-add-new" class="mb-5 col-md-8" action="#">
      <div class="mb-3">
        <label for="filter" class="form-label _gray">
          Запрос кластера<br>
        </label>
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon3">{{ baseHost }}/tools/{{ catalogProduct }}/</span>

          <select disabled v-model="catalogProduct" style="border-color: rgb(206, 212, 218); width: 100px;">
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
            <select disabled v-for="select in selectedFilters" :key="select.id" class="form-control" v-model="select.value" style="border-color: rgb(206, 212, 218); width: 100px;">
              <option v-for="filter in filters" :key="filter.id" :value="filter.furl">{{ filter.furl }}</option>
            </select>
<!--          </div>-->

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
                  rows="3"
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
        <textarea required
               id="subheader"
               name="subheader"
               class="form-control"
                  rows="3"
               v-model="subheader"
               placeholder="Subheader для фильтра"
        ></textarea>
      </div>

      <div class="mb-3">
        <label for="html" class="form-label _gray">
          HTML
        </label>
        <textarea rows="10" v-on:blur="()=>{
            if(html.match(/<body>(\w|\W)*<\/body>/gm)) {
              html = html.match(/<body>(\w|\W)*<\/body>/gm)[ 0 ];
            }

            html = html.replace('<body>', '');
            html = html.replace('</body>', '');
            html =  html.trim();
        }" v-model="html" id="html" class="form-control" placeholder="HTML"></textarea>
      </div>

      <div class="mb-3">
        <label for="temporary" class="form-label _gray">
          Temporary
        </label>
        <textarea rows="3" v-model="temporary" id="temporary" class="form-control" placeholder="Список ключевых фраз для карточек"></textarea>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label _gray">
          Комментарий
        </label>
        <input
               id="comment"
               name="comment"
               class="form-control"
               type="text"
               v-model="comment"
        >
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
    const catalogProduct = ref( 'naklejki' )
    const description = ref( '' )
    const h1 = ref( '' )
    const subheader = ref( '' )
    const isindex = ref( false )
    const comment = ref( '' )
    const html = ref( '' )
    const temporary = ref( '' )
    const title = ref( '' )
    const link = ref( '' )
    const dateStart = ref( '' )
    const dateEnd = ref( '' )
    const currentVariant = ref( 0 ) // Enum
    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Загружается' ] )

    const products = ref( [] )
    const filters = ref( [] )

    const currentFilter = ref( route.query.f )

    if ( isDev.value ) {
      catalogProduct.value = `naklejky`
      description.value = `Дескрипшн для фильтра ${ random( 255 ) }`
      h1.value = 'H1 для фильтра/фильтров'
      subheader.value = 'Subheader для фильтра/фильтров'
      isindex.value = false
      comment.value = 'Комментарий для фильтра/фильтров'
      html.value = 'Семантичный <b>HTML</b>'
      title.value = 'Title для фильтра'
    }

    const create = async () => {

      const formdata = new FormData()

      const selectedFiltersValues = []
      selectedFilters.value.forEach( ( selectedFilter ) => {
        selectedFiltersValues.push( selectedFilter.value )
      } )

      formdata.append( "filter", selectedFiltersValues.sort().join( '__' ) )
      formdata.append( "catalog", catalogProduct.value )
      formdata.append( "title", title.value )
      formdata.append( "description", description.value )
      formdata.append( "h1", h1.value )
      formdata.append( "subheader", subheader.value )
      formdata.append( "comment", comment.value )
      formdata.append( "html", html.value )
      formdata.append( "isindex", isindex.value )

      temporary.value = temporary.value.trim()

      let temporaryStringWithoutAnchors = temporary.value.replaceAll( '<a href="/catalog/naklejki/">', '' )
      temporaryStringWithoutAnchors = temporary.value.replaceAll( '</a>', '\n' )

      temporary.value  = temporaryStringWithoutAnchors
      const temporaryArray = temporary.value.split( /\r\n|\r|\n/g )

      console.table(temporaryArray)

      const temporaryArrayWithAnchors = []
      temporaryArray.forEach( ( line ) => {
        line = line.trim()
        if (line !== '') {
          line = line.replaceAll( /\t/g, ' ' )
          line = line.replaceAll( new RegExp(String.fromCharCode(160), "g"), ' ' )
          line = line.replaceAll( /(\s)+/g, ' ' )
          line = line.trim()
          line = line[0].toUpperCase() + line.substring( 1 )
          temporaryArrayWithAnchors.push( '<a href="/catalog/naklejki/">' + line + '</a>' )
        }
      } )

      const temporaryStringWithAnchors = temporaryArrayWithAnchors.join('\n')
      console.log(temporaryStringWithAnchors)

      temporary.value = temporaryStringWithAnchors
      formdata.append( "temporary", temporary.value )

      isSaving.value = true
      await axios.post( `${ BASE_URL }/tools/catalog-admin/`, formdata )
      // ---------------------------------------------------------------------------
      await sleep( 500 )
      isSaving.value = false
      await router.push( `/semantic/table?f=${ selectedFiltersValues.sort().join( '__' ) }` )
      console.log( router )
    }

    const fetchProducts = async () => {
      const reqStr = `${ BASE_URL }/tools/catalog-admin/products.json?ts=${Date.now()}`
      const response = await axios.get( reqStr )
      return response.data
    }

    const fetchFilters = async ( product ) => {
      try {
        const reqStr = `${ BASE_URL }/tools/catalog-admin/${ product.value }/filters/filters.json?ts=${Date.now()}`
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

    const fetchCurrentFilterFields = async ( catalogProduct, currentFilter ) => {
      const reqStr = `${ BASE_URL }/tools/catalog-admin/${  catalogProduct }/filters/json/${ currentFilter }.json?ts=${Date.now()}`
      const response = await axios.get( reqStr )
      const data = response.data

      console.table(data)

      description.value = data.description
      h1.value = data.h1
      subheader.value = data.subheader
      isindex.value = data.isindex
      comment.value = data.comment
      html.value = data.html
      temporary.value = data.temporary
      title.value = data.title

      temporary.value = temporary.value.trim()

      let temporaryStringWithoutAnchors = temporary.value.replaceAll( '<a href="/catalog/naklejki/">', '' )
      temporaryStringWithoutAnchors = temporaryStringWithoutAnchors.replaceAll( '</a>', '_derz_' ) // Моя специальная метка

      temporary.value = temporaryStringWithoutAnchors

      temporary.value = temporary.value.replace(/<[^>]*>/g, '')

      const temporaryArrayWithoutAnchors = temporary.value.split( '_derz_' )


      const temporaryArrayWithoutAnchorsClean = []
      temporaryArrayWithoutAnchors.forEach( ( line ) => {
        line = line.trim()
        line = line.replaceAll(/<[^>]*>/g, '')

        // Это делается при сохранении
        // line = line.replaceAll(/\t/g, ' ')
        // line = line.replaceAll(/(\s)+/g, ' ')

        line = line.replaceAll(/[^A-Za-zА-Яа-я\s0-9]+/g, '')
        if (line !== '') {
          temporaryArrayWithoutAnchorsClean.push( line )
        }
      } )

      temporary.value = temporaryArrayWithoutAnchorsClean.join('\n')
    }

    onMounted(async () => {
      products.value = await fetchProducts()

      catalogProduct.value = 'naklejki'
      filters.value = await fetchFilters( catalogProduct )

      currentFilter.value.split( '__' ).forEach( ( filter ) => {
        selectedFilters.value.push( { id: Date.now(), value: filter } )
      } )

      await fetchCurrentFilterFields( catalogProduct.value, currentFilter.value )

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
      comment,
      html,
      temporary,
      isindex,
      title,
      link,
      dateStart,
      dateEnd,
      currentVariant,
      variants,
      currentFilter,

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
