<template>
  <!-- Modal trigger -->
  <span data-bs-toggle="modal" :data-bs-target="`#js_uneversal_modal_${ modalId }`">
    <slot name="trigger" ></slot>
  </span>
  <!-- Modal -->
  <div class="modal fade" :id="`js_uneversal_modal_${ modalId }`" tabindex="-1" :aria-labelledby="`js_uneversal_modal_label_${ modalId }`" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" :id="`js_uneversal_modal_label_${ modalId }`">{{ title }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <slot></slot>
        </div>

        <div class="modal-footer">
          <button @click.prevent="action" type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ actionButtonText }}</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ModalUniversal",
  props: {
    modalId: String,
    title: String,
    actionButtonText: String,
    action: Function,
  },
}
</script>

<style scoped>

</style>
