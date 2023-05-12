<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <p>
      На этой странице будем отображажать дашборд всех подсистем
    </p>
    <h3>IPM</h3>
    <ul>
      <li><router-link class="" to="/ipm/add">Добавить</router-link></li>
      <li><router-link class="" to="/ipm">Список</router-link></li>
      <li><router-link class="" to="/variants/edit">Варианты отображения</router-link></li>
    </ul>
    <h3>Prices</h3>
    <ul>
      <li>
        <router-link class="" to="/prices/add">Добавить прайс</router-link>
      </li>
      <li>
        <router-link class="" to="/prices">Список прайсов</router-link>
      </li>
    </ul>
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
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
