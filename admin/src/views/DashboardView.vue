<template>
  <div class="home center" style="padding: 20px 0 0 0">
    <!--    <img alt="Vue logo" src="../assets/logo.png">-->
    <img class="mb-5" width="320" alt="Vue logo" src="../assets/logo-3.jpg">
    <BlankComponent msg="₽Tools Admin panel"/>
  </div>
</template>

<script>
// @ is an alias to /src
import BlankComponent from '@/components/BlankComponent.vue'

export default {
  name: 'HomeView',
  components: {
    BlankComponent
  }
}
</script>
