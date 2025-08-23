<template>
  <Modal :show="show" @close="close">
    <div class="px-6 py-4">
      <div class="text-lg font-medium text-gray-900">
        <slot name="title">Confirmer la suppression</slot>
      </div>

      <div class="mt-4 text-sm text-gray-600">
        <slot name="content">
          Êtes-vous sûr de vouloir effectuer cette action ?
        </slot>
      </div>
    </div>

    <div class="px-6 py-4 bg-gray-100 text-right">
      <button
        type="button"
        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3"
        @click="close"
      >
        Annuler
      </button>
      <button
        type="button"
        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        @click="confirm"
      >
        Confirmer
      </button>
    </div>
  </Modal>
</template>

<script setup>
import { defineEmits } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
  emit('close');
};

const confirm = () => {
  emit('confirm');
};
</script>
