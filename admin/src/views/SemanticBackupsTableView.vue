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
      <ModalUniversal modalId="backup_filters"
                      title="Создать архив"
                      actionButtonText="Архивировать"
                      cancelButtonText="Отменить"
                      :action="() => { create( backupComment ) }"
      >
        <div style="text-align: left;">
          <textarea
              v-model="backupComment"
              class="form-control"
              rows="3"
              placeholder="Примечание"
          >
          </textarea>
        </div>

        <template #trigger>

          <button @click.prevent="() => { return }"
                  class="btn btn-outline-success"
                  title="Архивировать"
                  style="margin: 0">
            Архивировать
          </button>

        </template>

      </ModalUniversal>
    </div>
  </div>
  <h5>Backup</h5>
  <table class="table" style="width: 100%;">
    <thead>
      <tr>
        <th scope="col">Дата</th>
        <th scope="col">Файл</th>
        <th scope="col" class="col-md-4">Примечание</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="( backup, index ) in backups"
          :key="index"
      >
        <td class="left">{{ new Date( backup.date ).toLocaleDateString( "ru-RU" ) }}</td>
        <td>
          <a target="_blank" :href="`${ BASE_URL }/tools/catalog-admin/naklejki/backups/${ backup.date }.json`">
            {{ backup.date }}
          </a>
        </td>
        <td>
          {{ backup.comment }}
        </td>
        <td class="right">
          <button
              @click.prevent="()=>{}"
              type="button"
              class="btn btn-success"
              title="Восстановить из архива"
          >
            Восстановить
          </button>

          <!--    -------------------------      -->
          <ModalUniversal modalId="delete_backup"
                          title="Удалить архив"
                          actionButtonText="Удалить"
                          cancelButtonText="Отменить"
                          :action="() => { deleteBackup( backup.date ) }"
          >
            <div style="text-align: left;">
              Действительно хотите удалить архив?
            </div>

            <template #trigger>

              <button @click.prevent="() => { return }"
                      class="btn btn-danger"
                      title="Удалить"
                      style="margin: 0">
                Удалить
              </button>

            </template>

          </ModalUniversal>
          <!--    -------------------------      -->
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import axios from 'axios';
import { onBeforeMount, ref } from 'vue';
import ModalUniversal from '@/components/ModalUniversal'


// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes( 'localhost' ) ?
    'http://localhost' :
    'https://r-color.ru'

export default {
  name: 'SemanticBackupsTableView.vue',

  components: {
    ModalUniversal,
  },

  setup() {

    const backups = ref( [] )
    const backupComment = ref( '' )

    const read = async () => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/backups.php`
      const result = await axios.get( reqString )
      backups.value = result.data.reverse()
      console.log( backups.value )
    }

    const create = async ( backupComment ) => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/create-backups.php?comment=${ backupComment }`
      await axios.get( reqString )
      await read()
    }

    const deleteBackup = async ( backupId ) => {
      const reqString = `${ BASE_URL }/tools/catalog-admin/naklejki/delete-backup.php?b=${ backupId }`
      await axios.get( reqString )
      await read()
    }

    onBeforeMount( async () => {
      read()
    } )

    return {
      // vars
      backups,
      backupComment,
      BASE_URL,

      // funcs
      create,
      deleteBackup,
    }
  }
}
</script>

<style scoped>

</style>
