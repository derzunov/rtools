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
    <h5>Создать кампанию</h5>
    <form @submit.prevent="create" id="ipm-add-new" class="mb-5 col-md-8" action="#">
      <div class="mb-3">
          <label for="url" class="form-label _gray">
            URL кампании<br>
          </label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon3">{{ baseUrl }}</span>
            <input required
                   id="url"
                   name="url"
                   class="form-control"
                   type="text"
                   v-model = "url"
                   @change="onUrlChange"
            >
          </div>
      </div>
      <div class="mb-3">
        <label for="title" class="form-label _gray">
          Заголовок
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
          Текст
        </label>
        <input required
               id="description"
               name="description"
               class="form-control"
               type="text"
               v-model="description"
        >
      </div>
      <div class="mb-3">
        <label for="link" class="form-label">
          Перейти по URL
        </label>
        <input id="link"
               name="link"
               class="form-control"
               type="url"
               v-model="link"
        >
      </div>

      <div class="mb-3">
        <label for="variant" class="form-label">
          Вариант отображения
        </label>
        <select class="form-select" name="variant" v-model="currentVariant">
          <option v-for="( variantItem, variantIndex ) in variants"
                  :value="variantIndex"
                  :key="variantIndex"
          >
            {{ variantItem }}
          </option>
        </select>
      </div>

      <div class="row">
        <label for="date_start" class="form-label">
          Продолжительность кампании
        </label>
        <div class="mb-3 col-md-5">
          <input required
                 id="date_start"
                 name="date_start"
                 class="form-control"
                 type="date"
                 v-model="dateStart"
          />
        </div>
        <div class="mb-3 col-md-5" style="position:relative">
          <div style="position:absolute; left: -3px; top: 5px;">
            -
          </div>
          <input required
                 id="date_end"
                 name="date_end"
                 class="form-control"
                 type="date"
                 v-model="dateEnd"
          />
        </div>
        <div class="mb-3 col-md-2 _gray" style="padding: 6px; text-align: center;">
          {{ campaignPeriodInDays + ' ' + pluralize( 'ru', campaignPeriodInDays, [ 'день', 'дня', 'дней' ] ) }}
        </div>
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
  urlDumbProtect,
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
    const url = ref( '/' )
    const title = ref( '' )
    const description = ref( '' )
    const link = ref( '' )
    const dateStart = ref( '' )
    const dateEnd = ref( '' )
    const currentVariant = ref( 0 ) // Enum
    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Загружается' ] )

    const currentUrlId = ref( 0 )

    if ( route.params.currentUrl ) {
      url.value = route.params.currentUrl
    }

    if ( isDev.value ) {
      title.value = `Заголовок ${ random( 255 ) }`
      description.value = `Контент ${ random( 255 ) }`
      link.value = 'https://ya.ru'
      dateStart.value = '2023-01-01'
      dateEnd.value = '2023-12-30'
    }

    const campaignPeriodInDays = ref( 0 )

    const create = async ( event ) => {

      const formdata = new FormData()
      formdata.append( "url", url.value )
      formdata.append( "title", title.value )
      formdata.append( "description", description.value )
      formdata.append( "link", link.value )
      formdata.append( "date_start", dateStart.value )
      formdata.append( "date_end", dateEnd.value )
      formdata.append( "interacts", 0 )
      formdata.append( "variant", Number( currentVariant.value ) )
      formdata.append( "type", "news" )

      isSaving.value = true
      await axios.post( `${ BASE_URL }/tools/ipm/`, formdata )

      // Get the fresh index of our url (we need it for router.push) --------------
      // fetch fresh urls list ( with just created too, if it was created )
      const urls = await fetchUrls()

      if ( urls && urls.length ) {
        // find our url in list, get its index and save it to currentUrlId
        urls.forEach( ( urlItem, urlIndex ) => {
          if ( urlItem === url.value ) {
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
      await router.push( `/news/${ currentUrlId.value }` )
    }

    const onUrlChange = () => {
      url.value = urlDumbProtect( url.value )
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
      url,
      title,
      description,
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
