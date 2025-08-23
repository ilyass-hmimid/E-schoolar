<template>
  <div v-if="error" class="fixed inset-0 bg-red-600 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md mx-4">
      <div class="flex items-center mb-4">
        <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-xl font-semibold text-gray-900">Erreur</h2>
      </div>
      <p class="text-gray-600 mb-6">{{ error }}</p>
      <div class="flex space-x-3">
        <button
          @click="retry"
          class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
        >
          Réessayer
        </button>
        <button
          @click="clearError"
          class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
        >
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const error = ref(null);

const retry = () => {
  window.location.reload();
};

const clearError = () => {
  error.value = null;
};

// Expose methods for global use
defineExpose({
  setError: (err) => {
    error.value = err;
  },
  clearError
});
</script>
