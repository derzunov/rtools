<template>
<!--  <nav>-->
<!--    <router-link to="/ipm/add">Добавить новость</router-link> |-->
<!--    <router-link to="/">URLs & News</router-link> |-->
<!--    <a target="_blank" href="https://r-color.ru/tools/ipm/db/index.json">Все новости JSON</a> |-->
<!--    <router-link to="/about">Документация</router-link>-->
<!--  </nav>-->
  <div class="container">
    <TopMenu/>
    <router-view v-if="isLoggedIn" ></router-view>
    <div v-if="!isLoggedIn">

      <h4 class="mt-5 center">Пожалуйста,
        <ModalUniversal :modalId="`authorize-modal`"
          title="Авторизация"
          actionButtonText="Авторизоваться"
          :action="() => { authorize() }"
        >
          <template #trigger>
            <a href="#"
               title="авторизоваться"
               @click.prevent="() => { return }">
              авторизуйтесь
            </a>
          </template>

          <div class="form-outline mb-4">
            <input type="text" id="login"
                   required
                   v-model="login"
                   class="form-control"
                   placeholder="Введите логин"
            />
          </div>

          <!-- Password input -->
          <div class="form-outline mb-3">
            <input type="password" id="password"
                   required
                   v-model="password"
                   class="form-control"
                   placeholder="Введите пароль"
            />
          </div>
        </ModalUniversal>
      </h4>
      <ToastUniversal toastId="login-error"
                      toastClassNames="top-50 start-50 translate-middle"
                      ref="toastErrorRef">
        Неверный логин или пароль
      </ToastUniversal>
<!--      toastClassNames="top-50 start-50 translate-middle" -->
    </div>
    <div class="row">
      <div class="col-md-4">

        <!-- Modal -->
        <!-- TODO: Make it universal and move to component -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form enctype="multipart/form-data" action="/tools/price/index.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Загрузить 1С номенклатуру</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input name="one_s_price" type="file" />
                </div>
                <div class="modal-footer">
                  <input type="submit" class="btn btn-primary" value="Загрузить" />
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import TopMenu from '@/components/TopMenu'
import { ref } from 'vue'
import ModalUniversal from '@/components/ModalUniversal'
import ToastUniversal from '@/components/ToastUniversal'
import md5 from 'blueimp-md5'
export default {
  components: {
    TopMenu,
    ModalUniversal,
    ToastUniversal,
  },
  setup() {
    const SALTED_PASSWORD = 'de3ff570485f8bdabdd41d3c95b47313' // md5 of '555-900+salt'
    const HARDCODED_LOGIN = 'rcolor'
    const isLoggedIn = ref( localStorage.getItem( 'isLoggedIn' ) === 'true' )
    const isError = ref( false )
    const toastErrorRef = ref( null )
    const login = ref( '' )
    const password = ref( '' )

    const authorize = () => {
      // TODO: Сделать нормальную авторизацию
      if ( ( md5( `${ password.value }+salt` ) === SALTED_PASSWORD ) &&
         ( login.value === HARDCODED_LOGIN ) ) {
        isLoggedIn.value = true
        isError.value = false
        toastErrorRef.value.hide()
        localStorage.setItem( 'isLoggedIn', 'true' )
      } else {
        localStorage.setItem( 'isLoggedIn', 'false' )
        isError.value = true
        toastErrorRef.value.show()
      }
    }

    return {
      isLoggedIn,
      isError,
      toastErrorRef,
      login,
      password,

      authorize,
      md5,
    }
  }
}
</script>


<style>

nav {
  padding: 30px 0;
}

nav a {
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}

button {
  margin: 5px 10px 5px 0;
}

._gray {
  color: gray !important;
}

._red {
  color: red !important;
}

.url__active-count {
  color: #000 !important;
  text-decoration: none;
}
.center {
  text-align: center;
}

.right {
  text-align: right;
}
.ipm-top-menu {
  margin-top: 40px;
  margin-bottom: 25px;
}
.edit-control {
  margin: 0 5px;
  cursor: pointer;
}

.edit-control:hover {
  color: #0d6efd;
}
.edit-control_danger:hover {
  color: #dc3545;
}

._nowrap {
  word-break: keep-all;
  white-space: nowrap;
}

.campaign-item_disabled * {
  color: #b2b2b2 !important;
}


.form-row button {
  visibility: hidden;
}
.form-row:hover button {
  visibility: visible;
}

.b-table-input {
  height: 42px;
  border: 1px solid rgb( 185, 190, 185 );
}
.b-table-input .form-control {
  border: none;
  text-align: center;
}

.b-col-control-group {
  display: flex;
  justify-content: right;
}

.b-col-control {
  height: 12px;
  width: 12px;
  font-size: 23px;
  line-height: 12px;
  cursor: pointer;
  margin: 8px 0 15px 0;
  opacity: 0.5;

  visibility: hidden;
}
.b-table-input:hover .b-col-control {
  visibility: visible;
}

.b-col-control.b-col-control_add {
  margin: 9px 0 0px 10px;
}

.b-col-control.b-col-control_close {
  transform: rotate(45deg);
  margin-left: 10px;
  margin: 9px 0 0px 10px;
}

.b-col-control.b-col-control_colspan {
  height: 24px;
  width: 60px;
  margin: 4px 0 2px 0;

  font-size: 16px;
}

.b-price-form-table_wrapper {
  padding: 15px 5px;
  border-radius: 5px;
  background: rgb(100 100 100 / 10%);
}

.b-price-form-table_wrapper_white {
  background: rgb(255, 255, 255);
}
.b-price-form-table {
  width: 100%;
}
.b-price-form-table__row {
  vertical-align: middle;
}

/* Превью прайса */
.b-price-preview {
  padding: 10px;
  box-shadow: 4px 2px 8px 2px rgba(34, 60, 80, 0.2);
}

.b-accordion {}
.b-accordion__arrow {
  display: inline-block;
  margin-right: 5px;
  transition: all 0.2s ease-out;
}

.collapsed .b-accordion__arrow {
  transform: rotateZ(-180deg);
}

</style>
