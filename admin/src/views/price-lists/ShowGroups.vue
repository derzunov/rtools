<template>
  <div class="edit">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <span>/</span>
          <span>
            Группы
            <router-link to="/prices/subgroups/edit/" title="Редактировать">
              <font-awesome-icon
                  style="width: 22px; height: 22px;"
                  :icon="['fas', 'pencil-square']"
              />
            </router-link>
          </span>
        </h5>
      </div>
      <div class="col-md-7"></div>
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
          <li v-for="subgroupItem in groupItem.subgroups"
              :key="subgroupItem.id">
            <span required
                  :id="`subgroup_${ subgroupItem.id }`"
                  name="group.subgroups[]"
                  class="mb-1"
                  type="text"
            >
              {{ subgroupItem.name }}
            </span>
          </li>
        </ul>

      </div>
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
