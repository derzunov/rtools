<template>
  <div class="home">
    <img alt="Vue logo" src="../assets/logo.png">
    <BlankComponent msg="Welcome to Admin panel"/>
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
