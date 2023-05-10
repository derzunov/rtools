<template>
  <center>
    <div class="home" style="padding: 20px 0 0 0">
      <!--    <img alt="Vue logo" src="../assets/logo.png">-->
      <img alt="Vue logo" src="../assets/logo-2.jpg">
      <BlankComponent msg="Welcome to Admin panel"/>
    </div>
  </center>
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
