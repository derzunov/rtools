<template>
  <div class="row ipm-top-menu">
    <div class="col-md-8">
      <h5>
        <router-link style="color: black" to="/" title="На главную">
          <font-awesome-icon :icon="['fas', 'house']" />
        </router-link>

        / Калькуляторы
      </h5>
    </div>
  </div>

  <div class="calculators">
    <StandsComponent
        :standWidth="1200"
        :standHeight="900"
        productName="Информационный стенд"
        productDescription="Описание продукта"
    />
  </div>

</template>

<script>
import StandsComponent from '@/components/StandsComponent.vue'
export default {
  name: "CalculatorsView",
  components: {
    StandsComponent
  },
}
</script>

<style scoped>

</style>
