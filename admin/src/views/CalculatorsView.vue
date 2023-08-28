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
    <StickersCalculator
        :standWidth="60"
        :standHeight="50"
        productName="Наклейки"
        productDescription="Описание продукта"
    />
    <StandsCalculator
        :standWidth="1200"
        :standHeight="900"
        productName="Информационный стенд"
        productDescription="Описание продукта"
    />
  </div>

</template>

<script>
import StandsCalculator from '@/calculators/StandsCalculator.vue'
import StickersCalculator from '@/calculators/StickersCalculator.vue'
export default {
  name: "CalculatorsView",
  components: {
    StandsCalculator,
    StickersCalculator,
  },
}
</script>

<style scoped>

</style>
