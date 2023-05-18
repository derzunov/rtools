<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-3">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <router-link
              style="padding-left: 10px;
              color: black; text-decoration: none;"
              to="/prices"
              title="К списку прайс-листов">
            /prices
          </router-link>
        </h5>
      </div>
    </div>

    <form @submit.prevent="savePrice" id="add-price" class="mb-3 col-md-12" action="#">
      <table style="width: 100%;">
        <tr>
          <td>
            <div class="mb-3" style="display: flex; justify-content: space-between;">
              <div>
                <h5><span class="_gray">{{ file_name + '.html' }}</span></h5>
                <router-link :to="`/prices/content/${ file_name }`" title="Редактировать">
                  Редактировать
                </router-link>
              </div>
              <div>
                <div>
                  <h5><span class="_gray">{{ groups[ group ].name }}</span></h5>
                  <p><span class="_gray">{{ groups[ group ].subgroups ? groups[ group ].subgroups[ subgroup ]?.name : 'Не найдено' }}</span></p>
                </div>
              </div>
            </div>
<!--            <div class="mb-3">-->
<!--              <h3>{{ header }}</h3>-->
<!--            </div>-->

<!--            <div class="mb-3">-->
<!--              <textarea required-->
<!--                        id="toptext"-->
<!--                        name="toptext"-->
<!--                        class="form-control"-->
<!--                        type="text"-->
<!--                        v-model="toptext"-->
<!--                        placeholder="Текст перед прайсом"-->
<!--              ></textarea>-->
<!--            </div>-->
          </td>
          <td style="width: 100px;"></td>
        </tr>
      </table>

      <!-- Шапка таблицы прайса -->
<!--      <div class="b-price-form-table_wrapper b-price-form-table_wrapper_white mb-3" style="background: #fff;">-->
<!--        <table class="b-price-form-table">-->
<!--          <tr-->
<!--              class="form-row b-price-form-table__row"-->
<!--              v-for="( row, rowIndex ) in table.thead.rows"-->
<!--              :key="row.id"-->
<!--              :data-row-index="rowIndex"-->
<!--          >-->
<!--            <td class="b-table-input"-->
<!--                v-for="( col, index ) in row.cols"-->
<!--                :key="index"-->
<!--                :colspan="col.colspan"-->
<!--            >-->
<!--              <input required-->
<!--                     class="form-control"-->
<!--                     type="text"-->
<!--                     v-model="row.cols[ index ].value"-->
<!--                     style="font-weight: bold; background: #eee;"-->
<!--                     :title="rowIndex === 1 ? 'Для вертикального слияния ячеек введите \'+\'' : ''"-->
<!--              >-->
<!--            </td>-->

<!--            <td>-->
<!--              &lt;!&ndash; Ячейка-заглушка. Под ней в строках tbody располагаются контролы добавить/удалить &ndash;&gt;-->
<!--            </td>-->
<!--          </tr>-->

<!--&lt;!&ndash;          <h5>Содержание таблицы прайса:</h5>&ndash;&gt;-->
<!--          <tr-->
<!--              class="b-price-form-table__row form-row"-->
<!--              v-for="( row, rowIndex ) in table.rows" :key="row.id"-->
<!--          >-->

<!--            <td class="b-table-input"-->
<!--                v-for="( col, index ) in row.cols"-->
<!--                :key="index"-->
<!--            >-->
<!--              <input class="form-control"-->
<!--                     type="text"-->
<!--                     v-model="row.cols[ index ].value"-->
<!--              >-->
<!--            </td>-->
<!--            <td class="mb-3 center" style="min-width: 100px;">-->

<!--              &lt;!&ndash; Добавление строки таблицы &ndash;&gt;-->
<!--              <button @click.prevent="() => { addRow( table, rowIndex ) }"-->
<!--                      class="btn btn-success"-->
<!--                      style="margin: 0;"-->
<!--                      title="Добавить строку">+</button>-->

<!--              &lt;!&ndash; Удаление строки таблицы &ndash;&gt;-->
<!--              <ModalUniversal :modalId="`delete_row_${ rowIndex }`"-->
<!--                              title="Подтверждение удаления строки"-->
<!--                              actionButtonText="Удалить"-->
<!--                              :action="() => { deleteRow( table, rowIndex ) }"-->
<!--              >-->
<!--                <template #trigger>-->

<!--                  <button @click.prevent="() => { return }"-->
<!--                          class="btn btn-light"-->
<!--                          title="Удалить строку"-->
<!--                          style="margin-left: 5px; border: 1px solid #eee;">-->
<!--                    <span style="transform: rotate(45deg);-->
<!--                                display: block;-->
<!--                                position: relative;-->
<!--                                left: 1px;">-->
<!--                      +-->
<!--                    </span>-->
<!--                  </button>-->

<!--                </template>-->
<!--                <div style="text-align: left;">-->
<!--                  Вы уверены,что хотите удалить строку?-->
<!--                  Данные будут потеряны.-->
<!--                </div>-->
<!--              </ModalUniversal>-->

<!--            </td>-->
<!--          </tr>-->
<!--        </table>-->
<!--      </div>-->

      <table style="width: 100%">
        <tr>
          <td>
<!--            <div class="mb-3">-->
<!--              <textarea required-->
<!--                        id="bottomtext"-->
<!--                        name="title"-->
<!--                        class="form-control"-->
<!--                        type="text"-->
<!--                        v-model="bottomtext"-->
<!--                        placeholder="Текст после прайса"-->
<!--              ></textarea>-->
<!--            </div>-->

<!--            <div>-->

<!--              <div class="b-price-form-table_wrapper mb-3">-->
<!--                <table style="width: 100%;">-->
<!--                  <tr>-->
<!--                    <td width="10%" >-->
<!--                      <label for="markup_factor" class="form-label _gray">-->
<!--                        Наценка-->
<!--                      </label>-->
<!--                      <input style="width: 70px;" required-->
<!--                             id="markup_factor"-->
<!--                             class="form-control mb-3"-->
<!--                             type="text"-->
<!--                             v-model="markup_factor"-->
<!--                      >-->
<!--                    </td>-->

<!--                    <td width="10%">-->
<!--                      &lt;!&ndash;          Процент изменения цены из 1с при котором нужно пересчитать прайс&ndash;&gt;-->
<!--                      &lt;!&ndash;          Если изменение цены превысило порог, то я отмечаю прайс, который нужно пересчитать&ndash;&gt;-->
<!--                      <label for="markup_factor" class="form-label _gray">-->
<!--                        Порог %-->
<!--                      </label>-->
<!--                      <input style="width: 70px;"-->
<!--                             id=""-->
<!--                             class="form-control mb-3"-->
<!--                             type="text"-->
<!--                             v-model="change_threshold"-->
<!--                      >-->
<!--                    </td>-->
<!--                    <td width="80%">-->
<!--                      <label for="one_s_codes" class="form-label _gray">-->
<!--                        Связанные с прайсом коды 1с-->
<!--                      </label>-->
<!--                      <input required-->
<!--                             class="form-control mb-3"-->
<!--                             type="text"-->
<!--                             id="one_s_codes"-->
<!--                             v-model="one_s_codes"-->
<!--                      >-->
<!--                    </td>-->
<!--                  </tr>-->
<!--                </table>-->
<!--              </div>-->
<!--            </div>-->

<!--            <div style="text-align: right;" >-->
<!--              <button type="submit" :disabled="isSaving" class="btn btn-primary">Сохранить</button>-->
<!--              <router-link to="/prices" type="submit" class="btn btn-secondary">Отмена</router-link>-->
<!--            </div>-->
          </td>
          <td style="width: 100px;"></td>
        </tr>
      </table>
    </form>

    <table style="width: 100%;">
      <tr>
        <td>
          <!-- Превью таблицы -->
          <div class="mb-3" v-html="htmlResultString"></div>
        </td>
        <td style="width: 100px;"></td>
      </tr>
    </table>

    <ToastUniversal toastId="price-list_saved"
                    ref="toastSavedRef">
      Изменения прайс-листа сохранены
    </ToastUniversal>
  </div>
</template>

<script>
import axios from "axios"
import { onMounted, ref, watch } from 'vue'
import {
  useRouter,
  useRoute
} from 'vue-router'
import {
  random,
  sleep,
  parsePriceToHtml,
  addRow,
  deleteRow,
} from '@/utils'
import pluralize from 'pluralizr'

import ToastUniversal from '@/components/ToastUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  components: {
    ToastUniversal,
  },

  setup() {
    const router = useRouter()
    const route = useRoute()
    const isDev = ref( IS_DEV )
    const isSaving = ref( false )

    // Price refs and vars -------------------------------------------
    const file_name = ref( 'Загружается' )
    const header = ref( 'Загружается' )
    const toptext = ref( 'Загружается' )
    const bottomtext = ref( 'Загружается' )
    const htmlResultString = ref( '' )
    const markup_factor = ref( 1.5 ) // Коэфициент наценки, на  него будет умножаться цена из файла-выгрузки из 1C
    const change_threshold = ref( 5 ) // Порог изменения цены из 1с в %, при превышении которого будем помечать прайс подлежащиим пересчёту
    const one_s_codes = ref( 'Загружается' ) // Строка связанных кодов 1с. Если какой-то из них элемент прайса выгрузки 1с изменится, по каждому прайсу будет оповещён владелец

    const group = ref(0) // Enum
    const groups = ref( [ 'Загружается' ] )

    const subgroup = ref(0) // Enum
    const subgroups = ref( [ 'Загружается' ] )


    // Table object
    const table = ref({
      thead: {
        rows: [
          {
            id: Date.now() + random( 100 ),
            cols: [ 'Загружается', 'Загружается', 'Загружается' ],
          },
          {
            id: Date.now() + random( 100 ),
            cols: [ 'Загружается', 'Загружается', 'Загружается' ],
          },
        ]
      },
      rows: [
        {
          id: Date.now() + random( 100 ),
          cols: [ 'Загружается', 'Загружается', 'Загружается' ],
        },
      ],
    })

    const toastSavedRef = ref( null )

    // Functions: -------------------------------------------------------

    const fetchPriceList = async () => {
      const response = await axios.get( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }` )

      file_name.value = response.data.file_name
      header.value = response.data.header
      toptext.value = response.data.toptext
      bottomtext.value = response.data.bottomtext
      markup_factor.value = response.data.markup_factor
      change_threshold.value = response.data.change_threshold
      one_s_codes.value = response.data.one_s_codes
      group.value = response.data.group
      subgroup.value = response.data.subgroup

      // Table object
      table.value = response.data.table

      // Подписываемся на изменения таблицы ПОСЛЕ того,
      // как зафетчили данные и присвоили новый объект
      // Иначе слушать будем изменения не этого объекта,
      // а того, который был присвоен при инициализации
      watch(
          [
            header,
            toptext,
            table.value,
            bottomtext,
            markup_factor,
            change_threshold,
          ],
          async () => {
            const priceObject = makePriceObject()
            htmlResultString.value = await parsePriceToHtml( priceObject )
          }
      )
    }

    const makePriceObject = () => {
      return {
        file_name: file_name.value,
        header: header.value,
        toptext: toptext.value,
        table: table.value,
        bottomtext: bottomtext.value,
        markup_factor: markup_factor.value,
        change_threshold: change_threshold.value,
        one_s_codes: one_s_codes.value,
        group: group.value, // Enum - индекс группы в массиве групп для фильтрации и сортировки
        subgroup: subgroup.value,
        updateDate: Date.now(),
      }
    }

    const savePrice = async () => {

      // All price fields to save in a single object
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )

      const formdata = new FormData()

      // Без Cc так как форм дата все равно приведет к нижнему регистру
      formdata.append( "file_name", priceObject.file_name )
      formdata.append( "pricelist_json", JSON.stringify( priceObject ) )
      formdata.append( "pricelist_html", htmlResultString.value )

      isSaving.value = true
      await axios.post( `${ BASE_URL }/tools/price/`, formdata ) // Переделать на /tools/api/prices/ ( так же с ipm )

      // Отладочная информация до того как будет готов backend api
      console.log( `POST ${ BASE_URL }/tools/price/` )
      console.log( 'JSON:' )
      console.log ( '%c%s', 'color: green;', JSON.stringify( priceObject, null, 2 ) );

      console.log( 'HTML:' )
      console.log( '%c%s', 'color: lightblue;', await parsePriceToHtml( priceObject ) )

      // ---------------------------------------------------------------------------
      await sleep( 500 )
      isSaving.value = false
      console.log( router )
      window.scrollBy( 0, 500 )
      toastSavedRef.value.show()
      // await router.push( `/prices/content/${ route.params.passedFileName }` )
      //window.open( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }`, '_blank' )
      //window.open( `${ BASE_URL }/tools/price/db/price-lists/html/${ route.params.passedFileName }.html`, '_blank' )//.focus()
    }

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgoups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    const log = async ( message ) => {
      console.log( message )
    }

    onMounted( async () => {
      await fetchGroups()
      await fetchPriceList()
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )
    })

    return {
      isDev,
      isSaving,

      table,
      file_name,
      header,
      toptext,
      bottomtext,
      htmlResultString,
      markup_factor,
      change_threshold,
      one_s_codes,
      groups,
      group,
      subgroups,
      subgroup,
      toastSavedRef,

      // Functions
      savePrice,
      pluralize,
      addRow,
      deleteRow,
      route,

      log,
    }
  }
}
</script>
