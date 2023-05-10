<template>
  <div class="edit">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
        </h5>
      </div>
      <div class="col-md-7"></div>
      <div class="col-md-2 center"></div>
    </div>
    <h5>#id {{ $route.params.itemId }}</h5>
    <form @submit.prevent="save" id="ipm-edit-campaign" class="mb-5 col-md-8" action="#">
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
                 v-model="url"
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
               v-model="currentNew.title"
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
               v-model="currentNew.description"
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
               v-model="currentNew.link"
        >
      </div>

      <div class="mb-3">
        <label for="variant" class="form-label">
          Вариант отображения
        </label>
        <select class="form-select" name="variant" v-model="currentNew.variant">
          <option v-for="( variantItem, variantIndex ) in variants" :key="variantIndex" :value="variantIndex">{{ variantItem }}</option>
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
          >
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
          >
        </div>
        <div class="mb-3 col-md-2 _gray" style="padding: 6px; text-align: center;">
          {{ campaignPeriodInDays + ' ' + pluralize( 'ru', campaignPeriodInDays, [ 'день', 'дня', 'дней' ] ) }}
        </div>
      </div>

      <div class="mb-3">
        <label for="link" class="form-label">
          Количество кликов
        </label>
        <input id="interacts"
               name="interacts"
               class="form-control"
               type="number"
               v-model="currentNew.interacts"
        >
      </div>

      <button type="submit" :disabled="isSaving" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import * as R from 'ramda'
import {calculateCampaignPeriodInDays, sleep} from "@/utils"
import pluralize from 'pluralizr'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  setup() {
    const isDev = ref( IS_DEV )
    const baseUrl = ref( `${ window.location.protocol }//${ window.location.host }` )

    const route = useRoute()
    const router = useRouter()

    const urls = ref( [] )
    const news = ref( [] )
    const currentNew = ref( {} )
    const isSaving = ref( false )

    const url = ref( '/' )
    const dateStart = ref( '' )
    const dateEnd = ref( '' )
    const campaignPeriodInDays = ref( 0 )
    // Variants Array: index - value of select option, string val -> option text
    const variants = ref( [ 'Загружается' ] )

    if ( isDev.value ) {
      dateStart.value = '2023-01-01'
      dateEnd.value = '2023-12-30'
    }

    const save = async () => {

      const data = {
        id: Number( route.params.itemId ),

        url: url.value,
        title: currentNew.value.title,
        description: currentNew.value.description,
        link: currentNew.value.link,
        interacts: currentNew.value.interacts,
        variant: Number( currentNew.value?.variant ),
        date_start: dateStart.value,
        date_end: dateEnd.value,

        type: "news",
      }

      isSaving.value = true
      await axios.put( `${ BASE_URL }/tools/ipm/`, new URLSearchParams( data ) )

      await sleep( 500 )
      isSaving.value = false
      await router.push( `/news/${ route.params.urlId }` )
    }

    const fetchNews = async ( url ) => {
      const reqStr = `${ BASE_URL }/tools/ipm/?url=${ url }&type=news&action=all`
      const response = await axios.get( reqStr )
      news.value = response.data
      return response.data
    }

    const fetchUrls = async () => {
      const reqStr = `${ BASE_URL }/tools/ipm/?type=urls&action=all`
      const response = await axios.get( reqStr )
      urls.value = response.data
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

    onMounted( async () => {
      await fetchVariants()
      const urls = await fetchUrls()
      const news = await fetchNews( urls[ route.params.urlId ] )

      currentNew.value = R.find( R.propEq( 'id', Number( route.params.itemId ) ) )( news )
      url.value = urls[ route.params.urlId ]
      dateStart.value = currentNew.value?.date_start
      dateEnd.value = currentNew.value?.date_end
      if ( !currentNew.value.variant ) {
        currentNew.value.variant = 0;
      }

      updateCampaignPeriod()
    } )

    watch( [ dateStart, dateEnd ], () => {
      updateCampaignPeriod()
    } )

    return {
      isDev,
      baseUrl,
      news,
      urls,
      currentNew,
      isSaving,
      url,
      dateStart,
      dateEnd,
      campaignPeriodInDays,
      variants,

      save,
      pluralize,
    }
  }
}
</script>
