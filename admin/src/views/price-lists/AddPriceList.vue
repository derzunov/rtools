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
    <h5>Новый прайс</h5>

    <form @submit.prevent="savePrice" id="add-price" class="mb-5 col-md-12" action="#">
      <div class="mb-3" style="display: flex; justify-content: space-between;">
        <div>
          <input required
                 id="file_name"
                 name="title"
                 class="form-control"
                 type="text"
                 placeholder="Имя файла (без расширения)"
                 v-model="file_name"
                 style="width: 300px; display: inline; vertical-align: middle;"
          >

          <span class="_gray" style="vertical-align: middle;">.html</span>

          <!-- Модалка для создания имени прайса из названия по-русски -->
          <ModalUniversal modalId="add_file_name"
                          title="Транслитерация имени файла"
                          actionButtonText="Сохранить"
                          :action="() => { return 0 }"
          >
            <template #trigger>

              <button @click.prevent="() => { return }"
                      class="btn btn-light"
                      title="Транслитерация имени файла"
                      style="margin-left: 5px; border: 1px solid #eee;">
                <span style="display: block;
                            position: relative;
                            left: 1px;">
                  +
                </span>
              </button>

            </template>
            <div style="text-align: left;">
              <p class="mb-3">
                <input id="file_name_ru"
                       name="title"
                       class="form-control"
                       type="text"
                       placeholder="Имя файла"
                       v-model="file_name_ru"
                       style="width: 300px;"
                >
              </p>
              <p class="mb-3">
                <b>{{ ruToLat( file_name_ru ) }}</b>
                <span v-if="ruToLat( file_name_ru ).length" class="_gray">.html</span>
              </p>
            </div>
          </ModalUniversal>
        </div>

        <div>
          <table style="width: 100%;">
            <tr>
              <td>
                <select class="form-select" name="group" id="group" v-model="group">
                  <option v-for="( groupItem, groupIndex ) in groups"
                          :value="groupIndex"
                          :key="groupItem.id"
                  >
                    {{ groupItem.name }}
                  </option>
                </select>
              </td>

              <td>
                <select class="form-select" name="subgroup" id="subgroup" v-model="subgroup">
                  <!-- Подгруппа не выбрана -->
<!--              <option checked value="Not selected">Подгруппа не выбрана</option>  -->
                  <option v-for="( subgroupItem, subgroupIndex ) in groups[ group ].subgroups"
                          :value="subgroupIndex"
                          :key="subgroupItem.id"
                  >
                    {{ subgroupItem.name }}
                  </option>
                </select>
              </td>
              <td style="width: 100px;"></td>
            </tr>
          </table>

        </div>


      </div>
      <div class="mb-3">
        <table style="width: 100%;">
          <tr>
            <td>
              <input required
                     id="header"
                     name="title"
                     class="form-control"
                     type="text"
                     v-model="header"
                     title="Рекомендуется в заголовке прайса упоминать название продукта"
                     placeholder="Заголовок прайса, содержащий название продукта"
              >
            </td>
            <td style="width: 100px;"></td>
          </tr>
        </table>
      </div>

      <div class="mb-3">
        <table style="width: 100%;">
          <tr>
            <td>
              <textarea id="toptext"
                        name="toptext"
                        class="form-control"
                        type="text"
                        v-model="toptext"
                        placeholder="Текст перед прайсом"
              ></textarea>
            </td>
            <td style="width: 100px;"></td>
          </tr>
        </table>
      </div>

      <!-- Price table ---------------------------------------------------->
      <div class="b-price-form-table_wrapper b-price-form-table_wrapper_white mb-3">
        <table class="b-price-form-table">
          <tr
              class="form-row b-price-form-table__row"
              v-for="( row, rowIndex ) in table.thead.rows" :key="row.id"
          >
            <td class="b-table-input"
                v-for="( col, colIndex ) in row.cols"
                :key="colIndex"
                :colspan="col.colspan"
            >
              <input required
                     style="font-weight: bold; background: #eee;"
                     class="form-control"
                     type="text"
                     v-model="row.cols[ colIndex ].value"
                     :title="rowIndex === 1 ? 'Для вертикального слияния ячеек введите \'+\'' : ''"
              >

              <div class="b-col-control-group">

                <!-- colspan ячейки заголовка -->
                <div class="b-col-control b-col-control_colspan">
                  cs:
                  <input required
                         style="width: 32px; border: 1px solid rgba( 0, 0, 0, 0.3 ); border-radius: 3px;"
                         type="number"
                         min="1"
                         :max="table.thead.rows[ 1 ].cols.length"
                         v-model="col.colspan"
                  >
                </div>

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
            </td>

            <td>
              <!-- Ячейка-заглушка. Под ней в строках tbody располагаются контролы добавить/удалить -->
            </td>
          </tr>
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
            <td class="mb-3 center" style="width: 100px;">

              <!-- Добавление строки таблицы -->
              <button @click.prevent="() => { addRow( table, rowIndex ) }"
                      class="btn btn-success"
                      style="margin: 0;"
                      title="Добавить строку">+</button>

              <!-- Удаление строки таблицы -->
              <button @click.prevent="() => { deleteRow( table, rowIndex ) }"
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
            </td>
          </tr>
        </table>
      </div>
      <!-- End of price table --------------------------------------------->

      <table style="width: 100%;">
        <tr>
          <td>
            <div class="mb-3">
              <textarea id="bottomtext"
                        name="title"
                        class="form-control"
                        type="text"
                        v-model="bottomtext"
                        placeholder="Текст после прайса"
              ></textarea>
            </div>

            <div class="b-price-form-table_wrapper mb-3">
              <table style="width: 100%;">
                <tr>
                  <td style="width: 10%">
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

                  <td style="width: 10%">
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
                  <td style="width: 80%">
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

            <div class="mb-3">
              <h5 v-show="relatedCodes.length">Связанные коды</h5>
              <table width="100%">
                <tr v-for="changedCode in relatedCodes"
                    :key="changedCode.one_s_code"
                    :class="{ '_red': !changedCode.isCorrect }"
                >

                  <td>{{ changedCode.one_s_code }}</td>
                  <td>{{ changedCode.name }}</td>

                </tr>
              </table>
            </div>

            <!-- Модалка с правилами для прайс-листов -->
            <ModalUniversal modalId="show_prices_rules"
                            title="<a target='_blank' href='https://r-color.ru/tools/price/rules.txt'>Правила работы с прайсами</a>"
                            actionButtonText="Ок"
                            cancelButtonText="Закрыть"
                            :action="() => { return 0 }"
            >
              <template #trigger>
          <span style="cursor: pointer; color: gray; font-size: 14px;">
            <font-awesome-icon
              style="cursor: pointer;"
              :icon="['fas', 'info-circle']"
            />
            Правила работы с прайсами
          </span>
              </template>

              <div style="text-align: left; font-size: 16px; font-weight: normal; white-space: pre-wrap">
                <p class="mb-3">
                  {{ rules }}
                </p>
              </div>
            </ModalUniversal>

            <div class="right"
                 style="display: flex;
                 justify-content: right;
                 height: 38px;"
            >
              <span
                style="padding: 7px 5px 0 0"
              >Стадия: </span>
              <select
                  style="width: 230px; margin: 0 5px 0 0"
                  class="form-select"
                  name="stage"
                  id="js_stage"
                  v-model="stage">
                <option value="-1">Не выбрано</option>
                <option v-for="stageItem in stages"
                        :value="stageItem.id"
                        :key="stageItem.id"
                >
                  {{ stageItem.name }}
                </option>
              </select>
              <button type="submit" id="liveToastBtn"
                      :disabled="isSaving || stage === -1 || stage === '-1'"
                      style="margin: 0;"
                      class="btn btn-primary">
                Сохранить
              </button>
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
                    toastClassNames=""
                    ref="toastSavedRef">
      Новый прайс-лист "{{ header }}" сохранен
    </ToastUniversal>

  </div>
</template>

<script>
import axios from "axios"
import { onBeforeMount, ref, watch } from 'vue'
import {
  useRouter,
  // useRoute
} from 'vue-router'
import {
  random,
  sleep,
  parsePriceToHtml,
  addRow,
  deleteRow,
  addCol,
  deleteCol,
  ruToLat,
  cleanOneSCodes,
} from '@/utils'
import pluralize from 'pluralizr'
import ModalUniversal from "@/components/ModalUniversal"
import ToastUniversal from "@/components/ToastUniversal"

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

  components: {
    ModalUniversal,
    ToastUniversal,
  },

  setup() {
    const router = useRouter()
    const isDev = ref( IS_DEV )
    const isSaving = ref( false )

    // Price refs and vars -------------------------------------------
    const file_name = ref( '' )
    const file_name_ru = ref( '' )
    const header = ref( '' )
    const toptext = ref( '' )
    const bottomtext = ref( '' )
    const htmlResultString = ref( '' )
    const markup_factor = ref( 1.5 ) // Коэффициент наценки, на  него будет умножаться цена из файла-выгрузки из 1C
    const change_threshold = ref( 5 ) // Порог изменения цены из 1с в %, при превышении которого будем помечать прайс подлежащиим пересчёту
    const one_s_codes = ref( '' ) // Строка связанных кодов 1с. Если какой-то из них элемент прайса выгрузки 1с изменится,
    // каждый связанный прайс будет помечен, как подлежащий пересчёту ( при превышении порога (см. выше) )

    const group = ref( 0 ) // Enum
    const groups = ref( [ { id: 'default value', name: 'empty' } ] )

    const subgroup = ref( 0 ) // или Enum

    const is_actualized = ref( true )
    const actualized_date = ref( Date.now() )


    const stage = ref( -1 )
    const stages = ref( [] )

    const rules = ref( 'Информация загружается' )

    // Table object
    const table = ref( {
      thead: {
        rows: [
          {
            id: Date.now() + random( 100 ),
            cols: [ {
              value: 'Наименование',
              colspan: 1,
              rowspan: 1,
            }, {
              value: 'Описание',
              colspan: 1,
              rowspan: 1,
            }, {
              value: 'Тираж (Размер) / Цена, руб/единицы изм.',
              colspan: 3,
              rowspan: 1,
            } ],
          },
          {
            id: Date.now() + random( 100 ),
            cols: [ {
              value: '+',
              colspan: 1,
              rowspan: 1,
            }, {
              value: '+',
              colspan: 1,
              rowspan: 1,
            }, {
              value: 'Вариант 1',
              colspan: 1,
              rowspan: 1,
            }, {
              value: 'Вариант 2',
              colspan: 1,
              rowspan: 1,
            }, {
              value: 'Вариант 3',
              colspan: 1,
              rowspan: 1,
            } ],
          },
        ]
      },
      rows: [
        {
          id: Date.now() + random( 100 ),
          cols: [ {
            value: '',
            colspan: 1,
            rowspan: 1,
          }, {
            value: '',
            colspan: 1,
            rowspan: 1,
          }, {
            value: '',
            colspan: 1,
            rowspan: 1,
          }, {
            value: '',
            colspan: 1,
            rowspan: 1,
          }, {
            value: '',
            colspan: 1,
            rowspan: 1,
          } ],
        },
      ],
    })

    const toastSavedRef = ref( null )

    // related 1s codes
    const relatedCodes = ref( [] )
    /*
    [
      {
        one_s_code,
        name // Наименование позиции, получаем с сервака
      },
      ...
    ]
    * */

    // Functions: -------------------------------------------------------

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
        group: groups.value[ group.value ].id, // group.value - индекс группы в массиве групп для фильтрации и сортировки по нему просто заберём наш id
        subgroup: groups.value[ group.value ].subgroups[ subgroup.value ].id, // subgroup.value - индекс подгруппы в массиве подгрупп
        update_date: Date.now(),
        admin_comment: '',
        is_actualized: true,
        actualized_date: Date.now(),
        stage: stage.value,
      }
    }

    const savePrice = async () => {

      // All price fields to save in a single object
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )

      // Проверки перед сохранением: --------------------------------
      // Не существует ли уже файл с таким именем
      if ( await checkIsPriceExist( priceObject.file_name ) ) {
        alert( `Прайс с именем ${ priceObject.file_name } уже существует` )
        return
      }

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
      console.log( '%c%s', 'color: green;', JSON.stringify( priceObject, null, 2 ) )

      console.log( 'HTML:' )
      console.log( '%c%s', 'color: lightblue;', await parsePriceToHtml( priceObject ) )

      // ---------------------------------------------------------------------------
      await sleep( 500 )
      isSaving.value = false
      //await router.push( "/prices/" )
      await router.push( "/prices/add" )
      window.scrollBy( 0, 500 )
      toastSavedRef.value.show()
      await sleep( 2500 )
      await router.push( `/prices/show/${ priceObject.file_name }` )
    }

    const fetchGroups = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=groups&populate=subgroups`
      const response = await axios.get( reqStr )
      groups.value = response.data
    }

    const checkIsPriceExist = async ( name ) => {
      const reqStr = `${ BASE_URL }/tools/price/?action=file&name=${ name }`
      const response = await axios.get( reqStr )

      return !!response.data
    }

    const checkPriceFileName = () => {
      if ( file_name.value &&
          !( /[a-z]+/i.test( file_name.value ) && file_name.value.toLowerCase() === file_name.value )
      ) {
        file_name.value = ''
        alert( 'Используйте только латиницу в нижнем регистре в имени файла' )
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


    const parseOneSCodes = ( codesString ) => {
      const codes = codesString.split( ';' )
      // Возможны дополнительные манипуляции с парсингом codes
      return codes
    }

    const fetchOneSCodes = async ( codes ) => {
      const relatedCodesObjects = []
      for ( const code of codes ) {
        if ( code.length ) {
          const reqStr = `${ BASE_URL }/tools/price/?action=info&field=name&one_s_code=${ code }`
          const response = await axios.get( reqStr )
          const name = response.data
          const isCorrect = !!name.length

          relatedCodesObjects.push( {
            one_s_code: code,
            name: isCorrect ? name : 'Некорректный код',
            isCorrect,
          } )
        }
      }
      return relatedCodesObjects
    }

    const fetchStages = async () => {
      const reqStr = `${ BASE_URL }/tools/price/?action=stages`
      const response = await axios.get( reqStr )
      stages.value = response.data
    }

    const fetchRules = async () => {
      const reqStr = `${ BASE_URL }/tools/price/rules.txt`
      const response = await axios.get( reqStr )
      rules.value = response.data
    }

    onBeforeMount( async () => {
      await fetchGroups()
      await fetchStages()
      await fetchRules()
      const priceObject = makePriceObject()
      htmlResultString.value = await parsePriceToHtml( priceObject )
    } )

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

    watch( [ file_name_ru ], () => {
      file_name.value = ruToLat( file_name_ru.value )
    } )

    watch( [ file_name ], () => {
      checkPriceFileName( file_name.value )
    } )

    watch( [ one_s_codes ],
        async () => {
          one_s_codes.value = cleanOneSCodes( one_s_codes.value )
          relatedCodes.value = await fetchOneSCodes( parseOneSCodes( one_s_codes.value ) )
        },
        { flush: 'post' } )

    watch( [ group ], () => {
      subgroup.value = 0
    } )

    return {
      isDev,
      isSaving,

      table,
      file_name,
      file_name_ru,
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

      is_actualized,
      actualized_date,

      stage,
      stages,

      rules,

      relatedCodes,

      toastSavedRef,

      savePrice,
      pluralize,
      addRow,
      deleteRow,
      addCol,
      deleteCol,
      ruToLat,
      checkIsPriceExist,

      handleOneSCodesBlur,
    }
  }
}
</script>
