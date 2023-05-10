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
      <div class="col-md-7"></div>
      <div class="col-md-2 center">
      </div>
    </div>
    <h5>Редактировать структуру прайса</h5>
    <form @submit.prevent="savePrice" id="add-price" class="mb-5 col-md-12" action="#">
      <div class="mb-3" style="display: flex; justify-content: space-between;">
        <div>
          <label for="file_name" class="form-label _gray">
            Имя файла прайс-листа (без расширения)
          </label>
          <input required
                 id="file_name"
                 name="title"
                 class="form-control"
                 type="text"
                 placeholder="Имя файла"
                 v-model="file_name"
          >
        </div>
        <div>
          <div>
            <label for="group" class="form-label _gray">
              Группа
            </label>
            <select class="form-select" name="group" id="group" v-model="group">
              <option v-for="( groupItem, groupIndex ) in groups"
                      :value="groupIndex"
                      :key="groupIndex"
              >
                {{ groupItem }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label for="header" class="form-label _gray">
          Заголовок прайса
        </label>
        <input required
               id="header"
               name="title"
               class="form-control"
               type="text"
               v-model="header"
        >
      </div>

      <div class="mb-3">
        <label for="toptext" class="form-label _gray">
          Текст перед прайсом
        </label>
        <textarea required
                  id="toptext"
                  name="toptext"
                  class="form-control"
                  type="text"
                  v-model="toptext"
        ></textarea>
      </div>

      <div
          class="form-row"
          v-for="( row, rowIndex ) in table.thead.rows" :key="row.id"
      >
        <div class="mb-3" style="display: flex; justify-content: space-between;">
          <div class="b-table-input" v-for="( col, colIndex ) in row.cols" :key="colIndex">
<!--            <span v-if="rowIndex === 0">-->
            <span>
              cs:
              <input required
                     class=""
                     style="width: 32px;"
                     type="number"
                     min="1"
                     :max="table.thead.rows[ 1 ].cols.length"
                     v-model="col.colspan"
              >
            </span>
<!--            <span v-if="rowIndex === 0">-->
<!--              rs:-->
<!--              <input required-->
<!--                     class=""-->
<!--                     style="width: 32px;"-->
<!--                     type="number"-->
<!--                     min="1"-->
<!--                     max="2"-->
<!--                     v-model="col.rowspan"-->
<!--              >-->
<!--            </span>-->
            <input required style="font-weight: bold;"
                   class="form-control"
                   type="text"
                   v-model="row.cols[ colIndex ].value"
            >

            <!-- Добавление ячейки заголовка -->
            <div class="b-col-control b-col-control_add"
                 title="Добавить ячейку"
                 @click.prevent="() => { addCol( table, row, rowIndex, colIndex ) }">
              +
            </div>

            <!-- Удаление ячейки заголовка таблицы -->
            <div class="b-col-control b-col-control_close"
                 title="Удалить ячейку"
                 @click.prevent="() => { deleteCol( table, row, rowIndex, colIndex ) }">
              +
            </div>
          </div>
        </div>
      </div>

      <div
          class="form-row"
          v-for="( row, rowIndex ) in table.rows"
          :key="row.id"
          :data-row-index="rowIndex"
      >
        <div style="display: flex; justify-content: space-between;">
          <div class="b-table-input" v-for="( col, index ) in row.cols" :key="index">
            <input disabled
                   class="form-control"
                   type="text"
                   v-model="row.cols[ index ].value"
            >
          </div>
        </div>
      </div>

      <div class="mb-3">
        <!--        <button-->
        <!--            @click.prevent="() => { addRow() }"-->
        <!--            title="Добавить строку"-->
        <!--            class="btn btn-primary">+</button>-->
      </div>

      <div class="mb-3">
        <label for="bottomtext" class="form-label _gray">
          Текст после таблицы прайса
        </label>
        <textarea required
                  id="bottomtext"
                  name="title"
                  class="form-control"
                  type="text"
                  v-model="bottomtext"
        ></textarea>
      </div>

      <div>
        <label for="one_s_codes" class="form-label _gray">
          Связанные с прайсом коды 1с
        </label>
        <input required
               class="form-control mb-3"
               type="text"
               id="one_s_codes"
               v-model="one_s_codes"
        >
      </div>

      <div>
        <label for="markup_factor" class="form-label _gray">
          Наценка
        </label>
        <input style="width: 70px;" required
               id="markup_factor"
               class="form-control mb-3"
               type="text"
               v-model="markup_factor"
        >
      </div>

      <button type="submit" :disabled="isSaving" class="btn btn-primary">Сохранить</button>
    </form>

    <div class="mb-3" v-html="htmlResultString"></div>

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
  addCol,
  deleteCol,
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
    const isSaving = ref( false )

    // Price refs and vars -------------------------------------------
    const file_name = ref( 'Загружается' )
    const header = ref( 'Загружается' )
    const toptext = ref( 'Загружается' )
    const bottomtext = ref( 'Загружается' )
    const htmlResultString = ref( '' )
    const markup_factor = ref( 1.5 ) // Коэфициент наценки, на  него будет умножаться цена из файла-выгрузки из 1C
    const one_s_codes = ref( 'Загружается' ) // Строка связанных кодов 1с. Если какой-то из них элемент прайса выгрузки 1с изменится, по каждому прайсу будет оповещён владелец

    const group = ref(0) // Enum
    const groups = ref( [ 'Загружается' ] )


    // Table object
    const table = ref( {
      thead: {
        rows: [
          {
            id: Date.now() + random( 100 ),
            cols: [
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1,
              },
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1
              },
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1
              }
            ],
          },
          {
            id: Date.now() + random(100),
            cols: [
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1,
              },
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1
              },
              {
                value: 'Загружается',
                colspan: 1,
                rowspan: 1
              }
            ],
          }
        ]
      },
      rows: [
        {
          id: Date.now() + random(100),
          cols: [
            {
              value: 'Загружается',
              colspan: 1,
              rowspan: 1,
            },
            {
              value: 'Загружается',
              colspan: 1,
              rowspan: 1
            },
            {
              value: 'Загружается',
              colspan: 1,
              rowspan: 1
            }
          ],
        },
      ],
    } )

    // Functions: -------------------------------------------------------

    const fetchPriceList = async () => {
      const response = await axios.get( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }` )
      console.log( response.data )

      file_name.value = response.data.file_name
      header.value = response.data.header
      toptext.value = response.data.toptext
      bottomtext.value = response.data.bottomtext
      markup_factor.value = response.data.markup_factor
      one_s_codes.value = response.data.one_s_codes
      group.value = response.data.group

      // Table object
      table.value = response.data.table

      // Подписываемся на изменения таблицы ПОСЛЕ того,
      // как зафетчили данные и присвоили новый объект
      // Иначе слушать будем изменения не этого объекта,
      // а того, который был присвоен при инициализации
      watch( [ table.value ], async () => {
        const priceObject = makePriceObject()
        htmlResultString.value = await parsePriceToHtml( priceObject )
      } )
    }

    const makePriceObject = () => {
      return {
        file_name: file_name.value,
        header: header.value,
        toptext: toptext.value,
        table: table.value,
        bottomtext: bottomtext.value,
        markup_factor: markup_factor.value,
        one_s_codes: one_s_codes.value,
        group: group.value, // Enum - индекс группы в массиве групп для фильтрации и сортировки
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
      // await router.push( `/prices/content/${ route.params.passedFileName }` )
      // window.open( `${ BASE_URL }/tools/price/?action=file&name=${ route.params.passedFileName }`, '_blank' )
      // window.open( `${ BASE_URL }/tools/price/db/price-lists/html/${ route.params.passedFileName }.html`, '_blank' )//.focus()
    }

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    onMounted(async () => {
      await fetchGroups()
      await fetchPriceList()
    })

    return {
      // external vars
      isDev,
      isSaving,

      // internal vars
      table,
      file_name,
      header,
      toptext,
      bottomtext,
      htmlResultString,
      markup_factor,
      one_s_codes,
      groups,
      group,

      // external functions
      route,
      pluralize,

      // internal func
      savePrice,
      addCol,
      deleteCol,
    }
  }
}
</script>
