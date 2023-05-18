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
    <h5>Подгрурппы</h5>
    <form method="post"
          id="ipm-edit-groups"
          class="mb-5 col-md-8"
          :action="`${ BASE_URL }/`"
          @submit.prevent="saveGroups">
      <div class="mb-3"
           v-for="( groupItem, groupIndex ) in groups"
           :key="groupIndex"
      >
        <input required
               id="group"
               name="groups[]"
               class="form-control"
               type="text"
               v-model = "groupItem.name"
        >

        <!-- Список подгрупп для группы -->
        <input required
               id="subgroup"
               name="group.subgroups[]"
               class="form-control _gray ms-5 mb-1"
               type="text"
               v-for="subgroupItem in groupItem.subgroups"
               :key="subgroupItem.id"
               v-model="subgroupItem.name"
        >

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
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  setup() {
    const groups = ref( [ 'Группы загружаются' ] )

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
    }

    onMounted( async () => {
      await fetchGroups()
    } )

    return {
      BASE_URL,
      groups,

      saveGroups,
    }
  }
}
</script>
