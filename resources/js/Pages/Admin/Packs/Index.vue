<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Gestion des Packs
        </h2>
        <Link
          :href="route('admin.packs.create')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <PlusIcon class="w-5 h-5 mr-2 -ml-1" />
          Nouveau pack
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Filtres -->
          <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                </div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Rechercher un pack..."
                  class="block w-full py-2 pl-10 pr-3 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  @keyup.enter="filterPacks"
                />
              </div>
            </div>
            
            <div>
              <select
                v-model="filters.type"
                class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="filterPacks"
              >
                <option :value="null">Tous les types</option>
                <option v-for="(label, key) in packTypes" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>
            
            <div class="flex items-center space-x-2">
              <select
                v-model="filters.est_actif"
                class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="filterPacks"
              >
                <option :value="''">Tous les statuts</option>
                <option value="1">Actifs</option>
                <option value="0">Inactifs</option>
              </select>
              
              <button
                @click="resetFilters"
                class="p-2 text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                title="Réinitialiser les filtres"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Statistiques -->
          <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
              <dt class="text-sm font-medium text-gray-500 truncate">Total des packs</dt>
              <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.total }}</dd>
            </div>
            
            <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
              <dt class="text-sm font-medium text-gray-500 truncate">Packs actifs</dt>
              <dd class="mt-1 text-3xl font-semibold text-green-600">{{ stats.actifs }}</dd>
            </div>
            
            <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
              <dt class="text-sm font-medium text-gray-500 truncate">Prix moyen</dt>
              <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ stats.prix_moyen.toFixed(2) }} DH</dd>
            </div>
            
            <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
              <dt class="text-sm font-medium text-gray-500 truncate">Total des ventes</dt>
              <dd class="mt-1 text-3xl font-semibold text-purple-600">{{ stats.total_ventes }} DH</dd>
            </div>
          </div>

          <!-- Liste des packs -->
          <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-50"
                    @click="sortBy('nom')"
                  >
                    <div class="flex items-center">
                      <span>Nom</span>
                      <span v-if="filters.sort_field === 'nom'" class="ml-1">
                        <svg v-if="filters.sort_direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </span>
                    </div>
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Type
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-50"
                    @click="sortBy('prix')"
                  >
                    <div class="flex items-center">
                      <span>Prix (DH)</span>
                      <span v-if="filters.sort_field === 'prix'" class="ml-1">
                        <svg v-if="filters.sort_direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </span>
                    </div>
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-50"
                    @click="sortBy('duree_jours')"
                  >
                    <div class="flex items-center">
                      <span>Durée</span>
                      <span v-if="filters.sort_field === 'duree_jours'" class="ml-1">
                        <svg v-if="filters.sort_direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </span>
                    </div>
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Statut
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="pack in packs.data" :key="pack.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ pack.nom }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ pack.description || 'Aucune description' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                      {{ formatPackType(pack.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ pack.prix }} DH
                    </div>
                    <div v-if="pack.prix_promo" class="text-sm text-red-600">
                      {{ pack.prix_promo }} DH (promo)
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ pack.duree_jours }} jours
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'bg-green-100 text-green-800': pack.est_actif,
                        'bg-red-100 text-red-800': !pack.est_actif
                      }"
                      class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                    >
                      {{ pack.est_actif ? 'Actif' : 'Inactif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                    <Link
                      :href="route('admin.packs.edit', pack.id)"
                      class="mr-3 text-indigo-600 hover:text-indigo-900"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="confirmDelete(pack)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr v-if="packs.data.length === 0">
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Aucun pack trouvé
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination :links="packs.links" class="mt-4" />
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer le pack
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer ce pack ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deletePack"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { PlusIcon, SearchIcon as MagnifyingGlassIcon } from '@heroicons/vue/outline';

const props = defineProps({
  packs: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      type: null,
      est_actif: null,
      sort_field: 'nom',
      sort_direction: 'asc'
    })
  },
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      actifs: 0,
      prix_moyen: 0,
      total_ventes: 0
    })
  }
});

const search = ref(props.filters.search || '');
const filters = ref({
  type: props.filters.type || null,
  est_actif: props.filters.est_actif !== null ? String(props.filters.est_actif) : '',
  sort_field: props.filters.sort_field || 'nom',
  sort_direction: props.filters.sort_direction || 'asc'
});

const showDeleteModal = ref(false);
const selectedPack = ref(null);
const deleteForm = useForm({});

// Types de packs depuis le modèle PHP
const packTypes = {
  'cours': 'Cours',
  'abonnement': 'Abonnement',
  'formation': 'Formation',
  'autre': 'Autre'
};

// Formater le type de pack pour l'affichage
const formatPackType = (type) => {
  return packTypes[type] || type;
};

const confirmDelete = (pack) => {
  selectedPack.value = pack;
  showDeleteModal.value = true;
};

const deletePack = () => {
  deleteForm.delete(route('admin.packs.destroy', selectedPack.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};

// Trier les packs
const sortBy = (field) => {
  const direction = filters.value.sort_field === field && filters.value.sort_direction === 'asc' ? 'desc' : 'asc';
  
  router.get(route('admin.packs.index'), 
    { 
      ...filters.value,
      sort_field: field,
      sort_direction: direction,
      search: search.value,
    },
    {
      preserveState: true,
      replace: true,
      preserveScroll: true,
    }
  );
};

// Filtrer les packs avec debounce
const filterPacks = debounce(() => {
  router.get(route('admin.packs.index'), 
    { 
      ...filters.value,
      search: search.value,
    },
    {
      preserveState: true,
      replace: true,
      preserveScroll: true,
    }
  );
}, 300);

// Réinitialiser les filtres
const resetFilters = () => {
  search.value = '';
  filters.value = {
    type: null,
    est_actif: '',
    sort_field: 'nom',
    sort_direction: 'asc'
  };
  
  router.get(route('admin.packs.index'), {}, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
};

// Observer les changements des filtres
watch(search, () => {
  filterPacks();
});

watch(
  () => filters.value,
  () => {
    filterPacks();
  },
  { deep: true }
);
</script>
