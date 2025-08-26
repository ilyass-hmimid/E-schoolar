<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Mobile header -->
    <div class="lg:hidden">
      <div class="flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3">
        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-600 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <div class="text-lg font-semibold text-gray-900">Allo Tawjih</div>
        <div class="w-6"></div> <!-- Pour l'alignement -->
      </div>
    </div>

    <!-- Main content -->
    <div class="flex flex-col lg:pl-64">
      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto px-4 sm:px-6 md:px-8">
            <!-- Contenu de la page -->
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';

const sidebarOpen = ref(false);
</script>
