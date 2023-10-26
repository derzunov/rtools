<template>
  <!-- Modal trigger -->
  <span data-bs-toggle="modal" :data-bs-target="`#js_modal_universal_${ modalId }`">
    <slot name="trigger" ></slot>
  </span>
  <!-- Modal -->
  <div class="modal fade" ref="modalRef" :id="`js_modal_universal_${ modalId }`" tabindex="-1" :aria-labelledby="`js_modal_universal_label_${ modalId }`" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
<!--      <div class="modal-content" @keyup.enter="action">-->

        <div class="modal-header">
          <h5 class="modal-title" :id="`js_modal_universal_label_${ modalId }`" v-html="title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <slot></slot>
        </div>

        <div class="modal-footer">
          <button v-if="actionButtonText" @click.prevent="action" type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ actionButtonText }}</button>
          <button v-if="!cancelButtonText" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <button v-if="cancelButtonText" type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ cancelButtonText }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import { ref, onMounted } from 'vue'

export default {
  name: "ModalUniversal",
  props: {
    modalId: String,
    title: String,
    actionButtonText: String,
    cancelButtonText: String,
    action: Function,
  },
  setup() {

    const modalRef = ref( null )

    const show = function () {
      const modal = new window.bootstrap.Modal( modalRef.value )
      modal.show()
    }

    const hide = function () {
      const modal = new window.bootstrap.Modal( modalRef.value )
      modal.hide()
    }

    onMounted( () => {
      // show()
    } )

    return {
      modalRef,

      show,
      hide,
    }
  },
}
</script>

<style scoped>

</style>
