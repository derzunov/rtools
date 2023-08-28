<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <p>
      На этой странице будет отображажаться дашборд всех подсистем
    </p>

    <h3>Прайсы</h3>

    <router-link class="" to="/prices/add">Добавить</router-link>
    <span> | </span>
    <router-link class="" to="/prices">Список</router-link>
    <span> | </span>
    <router-link class="" to="/prices/groups/show">Группы</router-link>


    <h3>IPM</h3>
    <router-link class="" to="/ipm/add">Добавить</router-link>
    <span> | </span>
    <router-link class="" to="/ipm">Список</router-link>
    <span> | </span>
    <router-link class="" to="/variants/edit">Варианты отображения</router-link>

    <h3>Семантика фильтров товаров</h3>
    <router-link class="" to="/semantic/add">Добавить</router-link>
  </div>
</template>

<script>
export default {
  name: 'DashboardDetails',
  props: {
    msg: String
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
a {
  color: #42b983;
}

span {
  color: lightgray;
}
</style>
