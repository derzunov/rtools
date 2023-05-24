<template>
  <div class="edit">
    <div class="row ipm-top-menu">
      <div class="col-md-4">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <span>/</span>
          <router-link style="color: black" to="/prices/groups/show" title="Группы">
            Группы
          </router-link>
          <span>/</span>
          <span>Редактирование</span>
        </h5>
      </div>
      <div class="col-md-6"></div>
      <div class="col-md-2 center"></div>
    </div>
    <form method="post"
          id="ipm-edit-groups"
          class="mb-5 col-md-8"
          :action="`${ BASE_URL }/`"
          @submit.prevent="saveGroups">

      <!-- Группа -->
      <div class="mb-3"
           v-for="( groupItem ) in groups"
           :key="groupItem.id"
      >
        <h5 required
               :id="`group_${ groupItem.id }`"
               name="groups[]"
               class=""
               type="text"
        >
          {{ groupItem.name }}
        </h5>

        <!-- Список подгрупп для группы -->
        <ul>
          <li v-for="( subgroupItem ) in groupItem.subgroups"
              :key="subgroupItem.id">
            <input required
                   :id="`subgroup_${ subgroupItem.id }`"
                   name="group.subgroups[]"
                   class="form-control mb-1"
                   type="text"
                   v-model="subgroupItem.name"
                   style="display: inline-block; width: 80%;"
            >
            <button @click.prevent="() => { deleteSubgroup( groupItem, subgroupItem ) }"
                    class="btn btn-light"
                    title="Удалить подгруппу"
                    style="display: inline-block; margin-left: 10px; vertical-align: baseline;"
            >
              <font-awesome-icon
                  style=""
                  :icon="['fas', 'times']"
              />
            </button>
          </li>
          <button @click.prevent="() => { groupItem.subgroups.push( { id: Date.now(), name: '' } ) }"
                  class="btn btn-light"
                  title="Добавить подгруппу">
            <font-awesome-icon
                style=""
                :icon="['fas', 'plus']"
            />
          </button>
        </ul>

      </div>

      <input hidden required
             id="type"
             name="type"
             class="form-control"
             type="text"
             value="groups"
      >

      <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>

    <!-- Удаление подгруппы -->
    <ModalUniversal modalId="delete_subgroup"
                    title="Подтверждение удаления прайс-листа"
                    actionButtonText=""
                    cancelButtonText="Ясно"
                    :action="() => { return }"
                    ref="relatedPricesModalRef"
    >
      <div style="text-align: left;">
        Подгруппа содержит связанные прайсы.
        <br>
        <br>
        <ul v-html="relatedPricesHtml"></ul>
        <br>
        Сначала удалите связи с прайсами
      </div>
    </ModalUniversal>

  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'

import {
  useRouter
} from 'vue-router'
import * as R from 'ramda'
import ModalUniversal from '@/components/ModalUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  components: {
    ModalUniversal,
  },

  setup() {
    const router = useRouter()

    const groups = ref( [ 'Группы загружаются' ] )
    const relatedPricesHtml = ref( '' )
    const relatedPricesModalRef = ref( null )

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=groups`
      const response = await axios.get( reqStr )
      if ( response.data && response.data?.length ) {
        groups.value = response.data
      }
    }

    const saveGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/`
      const formData = new FormData()

      formData.append( 'action', 'groups' )
      formData.append( 'groups_populated', JSON.stringify( groups.value ) )

      await axios.post( reqStr, formData )
      await router.push( '/prices/groups/show/' )
    }

    const getRelatedPrices = async ( subgroupId ) => {
      const reqStr = `${ BASE_URL }/tools/price/?action=all`
      const { data: prices } = await axios.get( reqStr )
      const relatedPrices = prices.filter( price => price.subgroup === subgroupId )

      return relatedPrices
    }

    const deleteSubgroup = async ( groupItem, subgroupItem ) => {
      const relatedPrices = await getRelatedPrices( subgroupItem.id )
      if ( !relatedPrices.length ) {
        const subgroupIndex = R.findIndex( R.propEq( 'id', subgroupItem.id ) )( groupItem.subgroups )
        groupItem.subgroups.splice( subgroupIndex, 1 )
      } else {
        relatedPricesHtml.value = ''

        relatedPrices.forEach( ( price ) => {
          relatedPricesHtml.value += `<li>${ price.header };</li> `
        } )

        relatedPricesModalRef.value.show()
      }
    }

    onMounted( async () => {
      await fetchGroups()
    } )

    return {
      BASE_URL,
      groups,
      relatedPricesHtml,
      relatedPricesModalRef,

      saveGroups,
      deleteSubgroup,
    }
  }
}
</script>
