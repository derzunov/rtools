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
      <router-link class="btn btn-outline-success" to="/semantic/add">Добавить</router-link>
    </div>
  </div>
  <h5>Наклейки</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col" class="col-md-2" style="width: 50px;">i</th>
        <th scope="col" class="col-md-4">Filter</th>
        <th scope="col" class="col-md-2 center">Title</th>
        <th scope="col" class="col-md-2 center">Description</th>
        <th scope="col" class="col-md-2 center">Html</th>
        <th scope="col" class="col-md-4">Статистика</th>
        <th scope="col" class="col-md-4">Комментарий</th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="( filter, index ) in filters"
        :data-index="index"
        :key="index">
      <td class="_gray" style="width: 50px;">-</td>
      <th>
        <router-link to="/semantic/edit">
          {{ filter.name }}
        </router-link>
      </th>
      <td>
        title
      </td>
      <td class="center">
        <a class="url__active-count" href="#" @click.prevent="() => { goToNews( index ) }">
          <!-- Показываем длинну только элементов с типом news -->
          <span>
            {{ filterActiveItems( filterItemsByType( urlsPopulated[ url ], 'news' ) )?.length }}
          </span>
        </a>
        <span class="_gray" > ({{ filterItemsByType( urlsPopulated[ url ], 'news' )?.length }})</span>
      </td>
      <td class="center">
        {{ getItemsInteractCount( filterItemsByType( urlsPopulated[ url ], 'news' ) ) }}
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import axios from "axios"
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { filterActiveItems, filterItemsByType } from '@/utils'

// TODO: Расчет этой константы и будущих других вынести в отдельный сервис
const BASE_URL = window.location.host.includes( 'localhost' ) ?
    'http://localhost' :
    'https://r-color.ru'

export default {
  setup() {
    const router = useRouter()

    const urlsPopulated = ref( {} )
    const urls = ref( [] )
    const urlsTitles = ref( {} )
    const filters = ref( [
          {
            "id": "311116",
            "name": "Материал",
            "furl": "material",
            "filters": [
              {
                "id": "111301",
                "name": "Бумажные",
                "furl": "bumazhnye"
              },
              {
                "id": "311301",
                "name": "Плёнка",
                "furl": "plyonka"
              }
            ]
          },
          {
            "id": "111121",
            "name": "Свойства материала",
            "furl": "svoistva-materiala",
            "filters": [
              {
                "id": "311124",
                "name": "Белые",
                "furl": "belye"
              },
              {
                "id": "111122",
                "name": "С ламинацией",
                "furl": "s-laminaciey"
              },
              {
                "id": "111129111",
                "name": "Магнитные",
                "furl": "magnitnye"
              },
              {
                "id": "111123",
                "name": "Прозрачные",
                "furl": "prozrachnye"
              },
              {
                "id": "111124",
                "name": "Виниловыe",
                "furl": "vinilovye"
              },
              {
                "id": "111125",
                "name": "Водостойкие",
                "furl": "vodostoykie"
              },
              {
                "id": "111126",
                "name": "Перфорированные",
                "furl": "perforirovannye"
              },
              {
                "id": "111127",
                "name": "Термонаклейки",
                "furl": "termonakleyki"
              },
              {
                "id": "111128",
                "name": "Светоотражающие",
                "furl": "svetootrazhayushchie"
              },
              {
                "id": "111129",
                "name": "УФ",
                "furl": "uf"
              },
              {
                "id": "1111391111",
                "name": "Самоклеящиеся",
                "furl": "samokleyashchiesya"
              }
            ]
          },
          {
            "id": "111111",
            "name": "Назначение",
            "furl": "naznachenie",
            "filters": [
              {
                "id": "111112",
                "name": "Декоративные",
                "furl": "dekorativnyye"
              },
              {
                "id": "111113",
                "name": "Информационные",
                "furl": "informacionnye"
              },
              {
                "id": "111114",
                "name": "Рекламные",
                "furl": "reklamnye"
              },
              {
                "id": "111115",
                "name": "Пломбы",
                "furl": "plomby"
              },
              {
                "id": "111168",
                "name": "День Рождения",
                "furl": "den-rozhdeniya"
              },
              {
                "id": "111169",
                "name": "Режим Работы",
                "furl": "rezhim-raboty"
              },
              {
                "id": "111170",
                "name": "Новогодний",
                "furl": "novogodnii"
              },
              {
                "id": "111171",
                "name": "Свадебный",
                "furl": "svadebnyi"
              },
              {
                "id": "111172",
                "name": "Витражный",
                "furl": "vitrazhnyi"
              },
              {
                "id": "111173",
                "name": "Медицинский",
                "furl": "medicinskii"
              },
              {
                "id": "111174",
                "name": "Безопасность",
                "furl": "bezopasnost"
              }
            ]
          },
          {
            "id": "111116",
            "name": "Форма",
            "furl": "forma",
            "filters": [
              {
                "id": "1111201",
                "name": "Фигурные",
                "furl": "figurnye"
              },
              {
                "id": "111117",
                "name": "Круглые",
                "furl": "kruglye"
              },
              {
                "id": "111118",
                "name": "Квадратные",
                "furl": "kvadratnye"
              },
              {
                "id": "111120",
                "name": "Прямоугольные",
                "furl": "pryamougolnye"
              },
              {
                "id": "111119",
                "name": "Треугольные",
                "furl": "treugolnye"
              },
              {
                "id": "11112011",
                "name": "Овальные",
                "furl": "ovalnye"
              }
            ]
          },
          {
            "id": "111130",
            "name": "Размер",
            "furl": "razmer",
            "filters": [
              {
                "id": "111131",
                "name": "Большие",
                "furl": "bolshie"
              },
              {
                "id": "111132",
                "name": "Маленькие",
                "furl": "malenkie"
              }
            ]
          },
          {
            "id": "333333",
            "name": "Одежда",
            "furl": "odezhda-cat",
            "filters": [
              {
                "id": "3333331",
                "name": "Одеежда",
                "furl": "odezhda"
              }
            ]
          },
          {
            "id": 333111,
            "name": "Объекты",
            "furl": "obekty",
            "filters": [
              {
                "id": "311143",
                "name": "Дом",
                "furl": "dom"
              },
              {
                "id": "111143",
                "name": "Магазин",
                "furl": "magazin"
              },
              {
                "id": "111137",
                "name": "Витрины",
                "furl": "vitriny"
              },
              {
                "id": "111148",
                "name": "Офис",
                "furl": "ofis"
              },
              {
                "id": "111160",
                "name": "Школа",
                "furl": "shkola"
              }
            ]
          },
          {
            "id": "111133",
            "name": "Куда",
            "furl": "kuda",
            "filters": [
              {
                "id": "111155",
                "name": "На стену",
                "furl": "na-stenu"
              },
              {
                "id": "111138",
                "name": "На дверь",
                "furl": "na-dver"
              },
              {
                "id": "111147",
                "name": "На окна",
                "furl": "na-okna"
              },
              {
                "id": "111154",
                "name": "На стекло",
                "furl": "na-steklo"
              },
              {
                "id": "111139",
                "name": "На зеркало",
                "furl": "na-zerkalo"
              },
              {
                "id": "111151",
                "name": "На пол",
                "furl": "na-pol"
              },
              {
                "id": "111152",
                "name": "На потолок",
                "furl": "na-potolok"
              },
              {
                "id": "111136",
                "name": "В ванную",
                "furl": "v-vannuyu"
              },
              {
                "id": "111150",
                "name": "На плитку",
                "furl": "na-plitku"
              },
              {
                "id": "111141",
                "name": "На кухню",
                "furl": "na-kuhnyu"
              },
              {
                "id": "111134",
                "name": "Банка",
                "furl": "banka"
              },
              {
                "id": "111135",
                "name": "Бутылка",
                "furl": "butylka"
              },
              {
                "id": "111140",
                "name": "На коробку",
                "furl": "na-korobku"
              },
              {
                "id": "111153",
                "name": "Для специй",
                "furl": "dlya-speciy"
              },
              {
                "id": "111144",
                "name": "Мебель",
                "furl": "mebel"
              },
              {
                "id": "111145",
                "name": "Ноутбук",
                "furl": "noutbuk"
              },
              {
                "id": "111149",
                "name": "Пакет",
                "furl": "paket"
              },
              {
                "id": "111157",
                "name": "Упаковка",
                "furl": "upakovka"
              },
              {
                "id": "111158",
                "name": "Холодильник",
                "furl": "holodilnik"
              },
              {
                "id": "111159",
                "name": "Шкаф",
                "furl": "shkaf"
              }
            ]
          },
          {
            "id": "211130",
            "name": "Транспорт",
            "furl": "transport",
            "filters": [
              {
                "id": "111161",
                "name": "Авто",
                "furl": "avto"
              },
              {
                "id": "111163",
                "name": "Мото",
                "furl": "moto"
              },
              {
                "id": "111162",
                "name": "Велосипед",
                "furl": "velosiped"
              },
              {
                "id": "111142",
                "name": "Лодка",
                "furl": "lodka"
              },
              {
                "id": "111156",
                "name": "Такси",
                "furl": "taksi"
              }
            ]
          },
          {
            "id": "111165",
            "name": "Контент",
            "furl": "kontent",
            "filters": [
              {
                "id": "111166",
                "name": "Ведется видеонаблюдение",
                "furl": "vedetsya-videonablyudenie"
              },
              {
                "id": "111167",
                "name": "Логотип",
                "furl": "logotip"
              }
            ]
          }
        ]
    )

    const fetchUrlsPopulated = async () => {
      const reqString = `${ BASE_URL }/tools/ipm/?type=urls&action=all&populate=true`
      const response = await axios.get( reqString )
      urlsPopulated.value = response.data
    }

    const fetchUrls = async () => {
      const reqString = `${ BASE_URL }/tools/ipm/?type=urls&action=all`
      const response = await axios.get( reqString )
      urls.value = response.data
    }

    // Работает только на бою
    // Для работы с localhost нужно настраивать CORS
    const getUrlTitle = async ( url ) => {
      if ( url === '*' ) {
        return 'All pages'
      }

      try {
        const response = await axios.get( `${ BASE_URL }\\${ url }` )
        const matches = response.data.match( /<title>(.*?)<\/title>/ )
        if ( matches && matches.length ) {
          return matches[ 1 ]
        }
      } catch( error ) {
        console.error( error )
      }

      return "Нет тайтла"
    }

    const fillUrlsTitles = () => {
      urls.value.forEach( async ( url ) => {
        const title = await getUrlTitle( url )
        urlsTitles.value[ url ] = title.replace( ' || РЕСПУБЛИКА ЦВЕТА', '' )
      } )
    }

    const getItemsInteractCount = ( items = [] ) => {
      let count = 0;
      items.forEach( ( item ) => {
        count += item?.interacts
      } )
      return count
    }

    const goToNews = ( urlId ) => {
      router.push( `news/${ urlId }` )
    }

    onMounted( async () => {
      await fetchUrlsPopulated()
      await fetchUrls()
      await fillUrlsTitles()
    } )

    return {
      // vars
      urlsPopulated,
      urls,
      urlsTitles,
      filters,

      // methods
      goToNews,
      filterActiveItems,
      filterItemsByType,
      getItemsInteractCount,
    }
  }
}
</script>
