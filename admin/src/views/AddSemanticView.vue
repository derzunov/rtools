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
      <div class="col-md-2 center">
      </div>
    </div>
    <h5>Добавить семантику для фильтра</h5>
    <form @submit.prevent="create" id="ipm-add-new" class="mb-5 col-md-8" action="#">
      <div class="mb-3">
        <label for="catalog" class="form-label _gray">
          Подкаталог внутри /catalog/
        </label>
        <input required
               id="catalog"
               name="catalog"
               class="form-control"
               type="text"
               v-model="catalog"
        >

      </div>
      <div class="mb-3">
        <label for="filter" class="form-label _gray">
          Фильтр<br>
        </label>
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon3">{{ baseUrl }}/tools/catalog/{{ catalog }}/?f=</span>
          <input required
                 id="filter"
                 name="filter"
                 class="form-control"
                 type="text"
                 v-model = "filter"
                 @change="onUrlChange"
          >
        </div>
      </div>
      <div class="mb-3">
        <label for="h1" class="form-label">
          Заголовок H1
        </label>
        <input required
               id="h1"
               name="h1"
               class="form-control"
               type="text"
               v-model="h1"
        >
      </div>

      <div class="mb-3">
        <label for="html" class="form-label">
          HTML
        </label>
        <input required
               id="html"
               name="html"
               class="form-control"
               type="text"
               v-model="html"
        >
      </div>

      <div class="mb-3">
        <label for="title" class="form-label">
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
        <label for="description" class="form-label">
          Description
        </label>
        <input required
               id="description"
               name="description"
               class="form-control"
               type="text"
               v-model="description"
        >
      </div>

      <button type="submit" :disabled="isSaving" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
</template>

<script>
import axios from "axios"
import { onMounted, ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  calculateCampaignPeriodInDays,
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
    const isSaving = ref( false )
    // Form values:
    const filter = ref( '' )
    const catalog = ref( '' )
    const description = ref( '' )
    const h1 = ref( '' )
    const html = ref( '' )
    const title = ref( '' )
    const link = ref( '' )
    const dateStart = ref( '' )
    const dateEnd = ref( '' )
    const currentVariant = ref( 0 ) // Enum
    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Загружается' ] )

    const currentUrlId = ref( 0 )

    if ( route.params.currentUrl ) {
      filter.value = route.params.currentUrl
    }

    if ( isDev.value ) {
      catalog.value = `nakleyky`
      description.value = `Дескрипшн для фильтра ${ random( 255 ) }`
      h1.value = 'H1 для фильтра'
      html.value = 'Семантичный <b>HTML</b>'
      title.value = 'Title для фильтра'
    }

    const campaignPeriodInDays = ref( 0 )

    const create = async ( event ) => {

      const formdata = new FormData()
      formdata.append( "filter", filter.value )
      formdata.append( "catalog", catalog.value )
      formdata.append( "title", title.value )
      formdata.append( "description", description.value )
      formdata.append( "h1", h1.value )
      formdata.append( "html", html.value )

      isSaving.value = true
      await axios.post( `${ BASE_URL }/tools/catalog/`, formdata )

      // Get the fresh index of our filter (we need it for router.push) --------------
      // fetch fresh urls list ( with just created too, if it was created )
      const urls = await fetchUrls()

      if ( urls && urls.length ) {
        // find our filter in list, get its index and save it to currentUrlId
        urls.forEach( ( urlItem, urlIndex ) => {
          if ( urlItem === filter.value ) {
            currentUrlId.value = urlIndex
          }
        } )
      } else {
        // Smth wrong, but we still can reset the form
        event.target.reset()
      }
      // ---------------------------------------------------------------------------
      await sleep( 500 )
      isSaving.value = false
      await router.push( '/' )
    }

    const onUrlChange = () => {
      //filter.value = filterDumbProtect( url.value )
    }

    const fetchUrls = async () => {
      const reqStr = `${ BASE_URL }/tools/ipm/?type=urls&action=all`
      const response = await axios.get( reqStr )
      return response.data
    }

    // fires in onMounted and watch (actualize period ref for view)
    const updateCampaignPeriod = () => {
      if ( !dateStart.value ) {
        campaignPeriodInDays.value = 0
        return
      }
      if ( !dateEnd.value ) {
        campaignPeriodInDays.value = 0
        return
      }

      campaignPeriodInDays.value = calculateCampaignPeriodInDays(
          new Date( dateStart.value ),
          new Date( dateEnd.value )
      )
    }

    const fetchVariants = async () => {
      const reqStr = `${ BASE_URL }/tools/ipm/?type=variants&action=all`
      const response = await axios.get( reqStr )
      variants.value = response.data
    }

    onMounted(async () => {
      updateCampaignPeriod()
      await fetchVariants()
    })

    watch( [ dateStart, dateEnd ], () => {
      updateCampaignPeriod()
    } )

    return {
      isDev,
      baseUrl,
      isSaving,
      filter,
      catalog,
      description,
      h1,
      html,
      title,
      link,
      dateStart,
      dateEnd,
      campaignPeriodInDays,
      currentVariant,
      variants,

      create,
      onUrlChange,
      pluralize,
    }
  }
}
</script>
