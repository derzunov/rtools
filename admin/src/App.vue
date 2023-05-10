<template>
<!--  <nav>-->
<!--    <router-link to="/ipm/add">Добавить новость</router-link> |-->
<!--    <router-link to="/">URLs & News</router-link> |-->
<!--    <a target="_blank" href="https://r-color.ru/tools/ipm/db/index.json">Все новости JSON</a> |-->
<!--    <router-link to="/about">Документация</router-link>-->
<!--  </nav>-->
  <div class="container">
    <TopMenu/>
    <router-view/>
    <div class="row">
      <div class="col-md-8">
        <nav>
          <a class="_gray" target="_blank" href="https://r-color.ru/tools/ipm/db/index.json">Все новости JSON</a> |
          <router-link class="_gray" to="/about">Документация</router-link> |
          <router-link class="_gray" to="/variants/edit">Варианты отображения</router-link> |
          <router-link class="_gray" to="/prices">Прайс-листы</router-link>
        </nav>
      </div>
      <div class="col-md-4">
<!--    <-- TODO: Переместить в настройки -->
        <nav class="right">
          <a class="_gray" target="_blank" data-bs-toggle="modal" data-bs-target="#exampleModal" href="#">Загрузить 1С номенклатуру</a> |
          <a @click.prevent="reset" class="_gray" href="#">Reset</a>
        </nav>

        <!-- Modal -->
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
import TopMenu from "@/components/TopMenu"

export default {
  components: {
    TopMenu,
  },

  setup() {
    const reset = () => {
      window.location.reload()
      window.localStorage.clear()
      window.sessionStorage.clear()
      window.location.reload()
    }
    return {
      reset,
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
  margin-left: 10px;
}

.b-col-control.b-col-control_close {
  transform: rotate(45deg);
  margin-left: 10px;
}

.b-col-control.b-col-control_colspan {
  height: 24px;
  width: 60px;
  margin: 4px 0 15px 0;

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
