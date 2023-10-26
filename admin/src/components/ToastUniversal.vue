<template>
<!--  <button type="button" class="btn btn-primary" :id="`js_toast_universal_trigger_${ toastId }`">Показать тост</button>-->

  <div :class="`toast-container position-fixed bottom-0 end-0 p-3 ${ toastClassNames }`" style="z-index: 11">
    <div :id="`js_toast_universal_${ toastId }`" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <img width="32" src="https://r-color.ru/favicon.ico" class="rounded me-2" alt="">
        <strong class="me-auto">₽Tools</strong>
        <small>Несколько секунд назад</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        <slot>Default toast message.</slot>
      </div>
    </div>
  </div>
</template>

<script>

import { onMounted } from 'vue'

export default {
  name: "ToastUniversal",
  props: {
    toastId: String,
    toastClassNames: String,
  },
  mounted() {

    const toastTrigger = document.getElementById( `js_toast_universal_trigger_${ this.toastId }` )
    const toastUniversal = document.getElementById( `js_toast_universal_${ this.toastId }` )

    if ( toastTrigger ) {
      toastTrigger.addEventListener( 'click', () => {
        const toast = new window.bootstrap.Toast( toastUniversal )
        toast.show()
      })
    }
  },

  setup( props ) {

    const show = function () {
      const toastUniversal = document.getElementById( `js_toast_universal_${ props.toastId }` )
      const toast = new window.bootstrap.Toast( toastUniversal )
      toast.show()
    }

    const hide = function () {
      const toastUniversal = document.getElementById( `js_toast_universal_${ props.toastId }` )
      const toast = new window.bootstrap.Toast( toastUniversal )
      toast.hide()
    }

    onMounted( () => {
      // TODO: Навести порядок с инстанирванием window.bootstrap.Toast
    } )

    return {
      show,
      hide,
    }
  }
}
</script>

<style scoped>

</style>
