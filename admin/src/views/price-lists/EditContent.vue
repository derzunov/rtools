<template>
  <div class="main">
    <div class="row ipm-top-menu">
      <div class="col-md-12">
        <h5>
          <router-link style="color: black" to="/" title="На главную">
            <font-awesome-icon :icon="['fas', 'house']" />
          </router-link>
          <router-link
              style="padding-left: 10px;
              color: black; text-decoration: none;"
              to="/prices"
              title="К списку прайс-листов">
            /прайсы</router-link>
          <router-link
              style="color: black; text-decoration: none;"
              :to="`/prices/show/${ file_name }`"
              :title="`К просмотру ${ header }`">
            /{{ header }}</router-link>
          <span> /Редактирование </span>
        </h5>
      </div>
    </div>

    <form @submit.prevent="savePrice" id="add-price" class="mb-5 col-md-12" action="#">
      <table style="width: 100%;">
        <tr>
          <td>
            <div class="mb-3" style="display: flex; justify-content: space-between;">
              <div>
                <h5>Редактирование <span class="_gray">{{ file_name + '.html' }}</span></h5>
              </div>
              <div>

                <table>
                  <tr>
                    <td>
                      <select class="form-select" name="group" id="js_group" v-model="group">
                        <option v-for="( groupItem, groupIndex ) in groups"
                                :value="groupIndex"
                                :key="groupItem.id"
                        >
                          {{ groupItem.name }}
                        </option>
                      </select>
                    </td>
                    <td>
                      <select v-if="groups[ group ]" class="form-select" name="subgroup" id="subgroup" v-model="subgroup">
                        <option v-for="( subgroupItem, subgroupIndex ) in groups[ group ].subgroups"
                                :value="subgroupIndex"
                                :key="subgroupItem.id"
                        >
                          {{ subgroupItem.name }}
                        </option>
                      </select>
                    </td>
                  </tr>
                </table>

              </div>
            </div>
            <div class="mb-3">
              <h3>{{ header }}</h3>
            </div>

            <div class="mb-3">
              <textarea required
                        id="toptext"
                        name="toptext"
                        class="form-control"
                        type="text"
                        v-model="toptext"
                        placeholder="Текст перед прайсом"
              ></textarea>
            </div>
          </td>
          <td style="width: 100px;"></td>
        </tr>
      </table>

      <!-- Шапка таблицы прайса -->
      <div class="b-price-form-table_wrapper b-price-form-table_wrapper_white mb-3" style="background: #fff;">
        <table class="b-price-form-table">
          <tr
              class="form-row b-price-form-table__row"
              v-for="( row, rowIndex ) in table.thead.rows"
              :key="row.id"
              :data-row-index="rowIndex"
          >
            <td class="b-table-input"
                v-for="( col, index ) in row.cols"
                :key="index"
                :colspan="col.colspan"
            >
              <input required
                     class="form-control"
                     type="text"
                     v-model="row.cols[ index ].value"
                     style="font-weight: bold; background: #eee;"
                     :title="rowIndex === 1 ? 'Для вертикального слияния ячеек введите \'+\'' : ''"
              >
            </td>

            <td>
              <!-- Ячейка-заглушка. Под ней в строках tbody располагаются контролы добавить/удалить -->
            </td>
          </tr>

<!--          <h5>Содержание таблицы прайса:</h5>-->
          <tr
              class="b-price-form-table__row form-row"
              v-for="( row, rowIndex ) in table.rows" :key="row.id"
          >

            <td class="b-table-input"
                v-for="( col, index ) in row.cols"
                :key="index"
            >
              <input class="form-control"
                     type="text"
                     v-model="row.cols[ index ].value"
              >
            </td>
            <td class="mb-3 center" style="min-width: 100px;">

              <!-- Добавление строки таблицы -->
              <button @click.prevent="() => { addRow( table, rowIndex ) }"
                      class="btn btn-success"
                      style="margin: 0;"
                      title="Добавить строку">+</button>

              <!-- Удаление строки таблицы -->
              <ModalUniversal :modalId="`delete_row_${ rowIndex }`"
                              title="Подтверждение удаления строки"
                              actionButtonText="Удалить"
                              :action="() => { deleteRow( table, rowIndex ) }"
              >
                <template #trigger>

                  <button @click.prevent="() => { return }"
                          class="btn btn-light"
                          title="Удалить строку"
                          style="margin-left: 5px; border: 1px solid #eee;">
                    <span style="transform: rotate(45deg);
                                display: block;
                                position: relative;
                                left: 1px;">
                      +
                    </span>
                  </button>

                </template>
                <div style="text-align: left;">
                  Вы уверены,что хотите удалить строку?
                  Данные будут потеряны.
                </div>
              </ModalUniversal>

            </td>
          </tr>
        </table>
      </div>

      <table style="width: 100%">
        <tr>
          <td>
            <div class="mb-3">
      <!--        <label for="bottomtext" class="form-label _gray">-->
      <!--          Текст после таблицы прайса-->
      <!--        </label>-->
              <textarea required
                        id="bottomtext"
                        name="title"
                        class="form-control"
                        type="text"
                        v-model="bottomtext"
                        placeholder="Текст после прайса"
              ></textarea>
            </div>

            <div>

              <!-- Блок обновившихся связанных кодов 1с -->

              <table v-if="!is_actualized" width="100%">
                <!-- Изменилась цена -->
                <tr class="mb-3" v-if="relatedChangedPrices?.length" style="vertical-align: top;">
                  <td><h5>Изменения цен:</h5></td>
                  <td>

                    <table width="100%">
                      <tr v-for="changedPrice in relatedChangedPrices"
                          :key="changedPrice.one_s_code">

                        <td>{{ changedPrice.one_s_code }}</td>
                        <td>{{ changedPrice.value.split( ';' )[ 1 ] }}</td>
                        <td>
                          <b>{{ changedPrice.old_price }} ₽</b>
                          <span> => </span>
                          <b>{{ changedPrice.current_price }} ₽</b>
                        </td>

                        <td>
                          <b :class="{ _red: changedPrice.percents > 0, _green: changedPrice.percents < 0 }"
                          > {{ changedPrice.percents }} % </b>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

                <tr v-if="relatedChangedPrices?.length">
                  <td>
                    <hr>
                  </td>
                  <td>
                    <hr>
                  </td>
                </tr>

                <!-- Нет на складе -->
                <tr class="mb-3" v-if="relatedOutOfStock?.length" style="vertical-align: top;">
                  <td><h5>Нет на остатках:</h5></td>
                  <td>
                    <table width="100%">
                      <tr v-for="changedPrice in relatedOutOfStock"
                          :key="changedPrice.one_s_code">

                        <td>{{ changedPrice.one_s_code }}</td>
                        <td>{{ changedPrice.value.split( ';' )[ 1 ] }}</td>
                        <td>
                          <b>{{ changedPrice.old_price }} ₽</b>
                        </td>

                      </tr>
                    </table>
                  </td>
                </tr>

                <tr v-if="relatedOutOfStock?.length">
                  <td>
                    <hr>
                  </td>
                  <td>
                    <hr>
                  </td>
                </tr>
              </table>

              <div class="b-price-form-table_wrapper mb-3">
                <table style="width: 100%;">
                  <tr>
                    <td width="10%" >
                      <label for="markup_factor" class="form-label _gray">
                        Наценка
                      </label>
                      <input style="width: 70px;" required
                             id="markup_factor"
                             class="form-control mb-3"
                             type="text"
                             v-model="markup_factor"
                      >
                    </td>

                    <td width="10%">
                      <!--          Процент изменения цены из 1с при котором нужно пересчитать прайс-->
                      <!--          Если изменение цены превысило порог, то я отмечаю прайс, который нужно пересчитать-->
                      <label for="markup_factor" class="form-label _gray">
                        Порог %
                      </label>
                      <input style="width: 70px;"
                             id=""
                             class="form-control mb-3"
                             type="text"
                             v-model="change_threshold"
                      >
                    </td>
                    <td width="80%">
                      <label for="one_s_codes" class="form-label _gray">
                        Связанные с прайсом коды 1с
                      </label>
                      <textarea class="form-control mb-3"
                                rows="3"
                                id="one_s_codes"
                                v-model="one_s_codes"
                                @blur="handleOneSCodesBlur"
                                title="Связанные с прайсом коды 1с через точку с запятой.
      Пример: 00-00000001;00-00000002"
                      ></textarea>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="b-price-form-table_wrapper mb-3">
                <h5>Комментарий администратора</h5>
                <textarea
                    class="form-control mb-3"
                    v-model="admin_comment"
                    name="admin_comment"
                    id="admin_comment"
                    rows="5"></textarea>
              </div>
              <div class="mb-3">
                <p>is_actualized: {{ is_actualized }}</p>
                <p>actualized_date: {{ new Date( actualized_date ).toLocaleString( 'ru-Ru' ) }}</p>
              </div>
            </div>

            <div style="text-align: right;" >
<!--              <button v-if="!is_actualized" @click.prevent="markAsActualized" class="btn btn-warning">Актуализировать</button>-->
              <!-- Модалка подтверждения актуализации -->
              <ModalUniversal modalId="actualize"
                              title="Подтверждение актуализации"
                              actionButtonText="Подтвердить и сохранить прайс"
                              :action="() => { markAsActualized(); savePrice(); }"
                              v-if="!is_actualized"
              >
                <template #trigger>

                  <button @click.prevent="() => { return }"
                          class="btn btn-warning"
                          title="Актуализировать и сохранить"
                          style="margin-left: 5px; border: 1px solid #eee;">
                    Актуализировать
                  </button>

                </template>
                <div style="text-align: left;">
                  Пересчёт цен завершён, цены в прайсе актуализированы
                </div>
              </ModalUniversal>
              <button v-if="is_actualized && ( relatedChangedPrices.length || relatedOutOfStock.length )" @click.prevent="markAsNotActualized" class="btn btn-danger">Деактуализировать</button>
              <button type="submit" :disabled="isSaving" class="btn btn-primary" title="Сохранить без актуализации">Сохранить</button>
              <router-link to="/prices" type="submit" class="btn btn-secondary">Отмена</router-link>
            </div>
          </td>
          <td style="width: 100px;"></td>
        </tr>
      </table>
    </form>

    <table style="width: 100%;">
      <tr>
        <td>
          <!-- Превью прайс-листа -->
          <div class="accordion" id="accordionExample">
            <div class="accordion-item" style="border: none;">
              <span class="accordion-header" id="headingOne">
                <p class="b-accordion__control mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  <span class="b-accordion__arrow">
                    <font-awesome-icon :icon="['fas', 'chevron-down']" />
                  </span>
                  Превью
                </p>
              </span>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="mb-3 b-price-preview" v-html="htmlResultString"></div>
              </div>
            </div>
          </div>

        </td>
        <td style="width: 100px;"></td>
      </tr>
    </table>

    <ToastUniversal toastId="price-list_saved"
                    ref="toastSavedRef"
                    toastClassNames=""
    >
      Изменения прайс-листа сохранены
    </ToastUniversal>
  </div>
</template>

<script>
import axios from "axios"
import * as R from "ramda"
import { onBeforeMount, ref, watch } from 'vue'
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
  tracer,
  cleanOneSCodes,
} from '@/utils'
import pluralize from 'pluralizr'

import ModalUniversal from '@/components/ModalUniversal'
import ToastUniversal from '@/components/ToastUniversal'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const IS_DEV = window.location.host.includes( 'localhost' )
const BASE_URL = IS_DEV ?
    'http://localhost' :
    'https://r-color.ru'

export default {

  components: {
    ModalUniversal,
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

    const group = ref( 0 ) // Enum
    const groups = ref( [ { id: 1, subgroups: [] } ] )

    const subgroup = ref( 0 ) // Enum
    const admin_comment = ref( '' )

    const is_actualized = ref( true )
    const actualized_date = ref( null )


    // Table object
    const table = ref({
      thead: {
        rows: [
          {
            id: Date.now() + random( 100 ),
            cols: [ {
              value: 'Загружается...',
              colspan: 1,
              rowspan: 1,
            } ],
          },
          {
            id: Date.now() + random( 100 ),
            cols: [ {
              value: 'Загружается...',
              colspan: 1,
              rowspan: 1,
            } ],
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

    // 1s changes
    const relatedChangedPrices = ref( [] )
    const relatedOutOfStock = ref( [] )

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
      group.value = R.findIndex( R.propEq( 'id', response.data.group  ) )( groups.value )

      subgroup.value = R.findIndex( R.propEq( 'id', response.data.subgroup  ) )(
          ( R.find( R.propEq( 'id', response.data.group  ) )( groups.value ) ).subgroups
      )
      admin_comment.value = response.data.admin_comment || ''

      is_actualized.value = response.data.is_actualized
      actualized_date.value = response.data.actualized_date

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
            is_actualized,
            actualized_date,
          ],
          async () => {
            const priceObject = makePriceObject()
            console.table( priceObject )
            htmlResultString.value = await parsePriceToHtml( priceObject )
          }
      )
    }

    const fetchRelatedOneSChangedCodes = async () => {
      tracer.debug( 'fetchRelatedOneSChangedCodes called' )
      const reqStr = `${ BASE_URL }/tools/price/?action=changed`
      const response = await axios.get( reqStr )
      if ( response.data ) {

        one_s_codes.value.split( ';' ).forEach( ( relatedCode ) => {

          // Есть ли связанный с прайсом код в объекте изменившихся кодов
          if ( response.data[ relatedCode.trim() ] ) {

            // Отсутствует на складе?
            if ( response.data[ relatedCode.trim() ].value[ 0 ] === '?' ) {
              relatedOutOfStock.value.push( response.data[ relatedCode.trim() ] )
            } else {
              // Или просто изменилась цена
              if ( Math.abs( response.data[ relatedCode.trim() ].percents ) >= change_threshold.value ) {
                // И изменения превысили заданный в прайсе порог
                relatedChangedPrices.value.push( response.data[ relatedCode.trim() ] )
              }
            }
          }
        } )
      }
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
        group: groups.value[ group.value ]?.id, // group.value - индекс группы в массиве групп для фильтрации и сортировки по нему просто заберём наш id
        subgroup: groups.value[ group.value ]?.subgroups[ subgroup.value ]?.id, // subgroup.value - индекс подгруппы в массиве подгрупп
        update_date: Date.now(),
        admin_comment: admin_comment.value,
        is_actualized: is_actualized.value,
        actualized_date: actualized_date.value,
      }
    }

    const markAsActualized = () => {
      is_actualized.value = true
      actualized_date.value = Date.now()
    }

    const markAsNotActualized = () => {
      is_actualized.value = false
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
      window.scrollBy( 0, 500 )
      toastSavedRef.value.show()
      await sleep( 1000 )
      await router.push( `/prices/show/${ route.params.passedFileName }` )
    }

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    const prepareData = async () => {
      try {
        await fetchGroups()
        await fetchPriceList()
        await fetchRelatedOneSChangedCodes()
        const priceObject = makePriceObject()
        htmlResultString.value = await parsePriceToHtml( priceObject )

      } catch( err ) {
        tracer.error( 'ERROR CAUGHT' )
        tracer.error( err )
      }
    }

    const handleOneSCodesBlur = () => {
      // Все описанные здесь преобразования совершаем только на расфокус,
      // Ибо иначе невозможно работать с формой

      // Если последний символ не точка с запятой, то мы добавляем точку с запятой.
      // Все предыдущие "хитрые" условия учтены в вотчере "cleanOneSCodes" - так надо!
      if ( one_s_codes.value.length &&
          one_s_codes.value.trim()[ one_s_codes.value.length - 1 ] !== ';'
      ) {
        one_s_codes.value += ';'
      }
    }

    onBeforeMount( async () => {
      await prepareData()

      // Даем возможность вотчеру сбрасывать подгруппу только после того, как данные были подготовлены
      watch( [ group ], () => {
        subgroup.value = 0
      } )
    } )

    watch( [ one_s_codes ],
        () => {
          one_s_codes.value = cleanOneSCodes( one_s_codes.value )
        },
        { flush: 'post' } )


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
      subgroup,
      admin_comment,
      is_actualized,
      actualized_date,

      relatedChangedPrices,
      relatedOutOfStock,

      toastSavedRef,

      // Functions
      savePrice,
      markAsActualized,
      markAsNotActualized,
      pluralize,
      addRow,
      deleteRow,
      route,

      handleOneSCodesBlur,

      // Utils
      R,
    }
  }
}
</script>
