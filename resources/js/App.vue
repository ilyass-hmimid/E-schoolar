<template>
  <div id="app">
    <!-- Layout principal -->
    <AuthenticatedLayout v-if="page.props.auth?.user">
      <component :is="page.component" v-bind="page.props" />
    </AuthenticatedLayout>
    
    <!-- Layout public pour login -->
    <div v-else class="min-h-screen bg-gray-50">
      <component :is="page.component" v-bind="page.props" />
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
</script>

<style>
/* Styles de base */
body {
  @apply bg-gray-50 text-gray-900;
  font-family: 'Nunito', system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Styles pour les formulaires */
.form-input,
.form-select,
.form-textarea {
  @apply block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
}

/* Boutons */
.btn {
  @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn-primary {
  @apply bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500;
}

.btn-secondary {
  @apply bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-indigo-500;
}

.btn-danger {
  @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500;
}

/* Alertes */
.alert {
  @apply p-4 rounded-md;
}

.alert-success {
  @apply bg-green-50 text-green-800;
}

.alert-error {
  @apply bg-red-50 text-red-800;
}

.alert-warning {
  @apply bg-yellow-50 text-yellow-800;
}

.alert-info {
  @apply bg-blue-50 text-blue-800;
}

/* Utilitaires */
.container {
  @apply mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl;
}
</style>
