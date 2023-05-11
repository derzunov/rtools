<template>
  <nav class="navbar
<!--navbar-expand-lg bg-light-->
navbar-expand-lg
navbar-dark bg-dark
  ">
    <div class="container-fluid">

      <router-link class="navbar-brand mb-0 h2" to="/">
<!--        <img src="https://r-color.ru/logo.png"-->
<!--             alt="Logo"-->
<!--             width="50" height="27"-->
<!--             class="d-inline-block align-text-top"-->
<!--             style="margin: 0 5px 0 0;"-->
<!--        >-->

        ₽Tools
      </router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <!-- Prices -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Цены
            </a>
            <ul class="dropdown-menu">
              <li>
                <router-link class="dropdown-item" to="/prices/add">Добавить прайс</router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/prices">Список прайсов</router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" target="_blank" data-bs-toggle="modal" data-bs-target="#exampleModal" href="#">Загрузить 1С номенклатуру</a>
              </li>
            </ul>
          </li>


          <!-- IPM -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Кампании
            </a>
            <ul class="dropdown-menu">

              <li>
                <router-link class="dropdown-item" to="/ipm/add">Добавить кампанию</router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <router-link class="dropdown-item" to="/ipm">Список кампаний</router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/variants/edit">Варианты отображения</router-link>
              </li>

              <li>
                <a @click.prevent="reset" class="dropdown-item" href="#">Очистить кэш кампаний</a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link py-2 px-0 px-lg-2" aria-current="page" href="/tools/admin/">Заказы</a>
          </li>

          <li class="nav-item">
            <a class="nav-link py-2 px-0 px-lg-2" aria-current="page" href="/tools/admin/">Оплата</a>
          </li>

          <li class="nav-item">
            <a class="nav-link py-2 px-0 px-lg-2" aria-current="page" href="/tools/admin/">Настройки</a>
          </li>

          <li class="nav-item">
            <router-link class="nav-link py-2 px-0 px-lg-2" aria-current="page" to="/about">Документация</router-link>
          </li>

        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "TopMenu",
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
  },
}
</script>

<style scoped>

</style>
