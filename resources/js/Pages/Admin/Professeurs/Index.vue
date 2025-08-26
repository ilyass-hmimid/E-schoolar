<template>
  <AppLayout title="Gestion des Professeurs">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Liste des Professeurs
        </h2>
        <Link 
          :href="route('admin.professeurs.create')" 
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition"
        >
          Ajouter un Professeur
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Filtres et recherche -->
            <div class="mb-6">
              <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                  <input
                    type="text"
                    v-model="search"
                    placeholder="Rechercher un professeur..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                  />
                </div>
                <div class="w-full md:w-48">
                  <select 
                    v-model="filters.status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                  >
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Tableau des professeurs -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nom & Prénom
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Téléphone
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Matières
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Statut
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="professeur in filteredProfesseurs" :key="professeur.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img class="h-10 w-10 rounded-full" :src="professeur.avatar || '/images/default-avatar.png'" alt="">
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ professeur.name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ professeur.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ professeur.phone || '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <span v-for="(matiere, index) in professeur.matieres" :key="matiere.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                          {{ matiere.nom }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800': professeur.is_active,
                          'bg-red-100 text-red-800': !professeur.is_active
                        }" 
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ professeur.is_active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link 
                          :href="route('admin.professeurs.show', professeur.id)" 
                          class="text-blue-600 hover:text-blue-900 mr-3"
                          title="Voir les détails"
                        >
                          <i class="fas fa-eye"></i>
                        </Link>
                        <Link 
                          :href="route('admin.professeurs.edit', professeur.id)" 
                          class="text-indigo-600 hover:text-indigo-900 mr-3"
                          title="Modifier"
                        >
                          <i class="fas fa-edit"></i>
                        </Link>
                        <button 
                          @click="confirmDelete(professeur)" 
                          class="text-red-600 hover:text-red-900"
                          title="Supprimer"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredProfesseurs.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                      Aucun professeur trouvé
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4" v-if="professeurs.meta && professeurs.meta.total > 10">
              <Pagination :links="professeurs.meta.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer le professeur
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer ce professeur ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton 
          class="ml-2" 
          @click="deleteProfesseur"
          :class="{ 'opacity-25': deleteProcessing }"
          :disabled="deleteProcessing"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  professeurs: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      status: ''
    })
  }
});

const search = ref(props.filters.search || '');
const filters = ref({
  status: props.filters.status || ''
});

const showDeleteModal = ref(false);
const professeurToDelete = ref(null);
const deleteProcessing = ref(false);

const filteredProfesseurs = computed(() => {
  return props.professeurs.data.filter(professeur => {
    const matchesSearch = 
      !search.value || 
      professeur.name.toLowerCase().includes(search.value.toLowerCase()) ||
      professeur.email.toLowerCase().includes(search.value.toLowerCase());
    
    const matchesStatus = 
      !filters.value.status || 
      (filters.value.status === 'actif' && professeur.is_active) ||
      (filters.value.status === 'inactif' && !professeur.is_active);
    
    return matchesSearch && matchesStatus;
  });
});

function confirmDelete(professeur) {
  professeurToDelete.value = professeur;
  showDeleteModal.value = true;
}

function deleteProfesseur() {
  if (!professeurToDelete.value) return;
  
  deleteProcessing.value = true;
  
  router.delete(route('admin.professeurs.destroy', professeurToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      deleteProcessing.value = false;
    },
    onError: () => {
      deleteProcessing.value = false;
    }
  });
}

// Débounce la recherche
watch(search, (value) => {
  router.get(route('admin.professeurs.index'), 
    { search: value, ...filters.value },
    { preserveState: true, replace: true }
  );
});

watch(() => filters.value, (value) => {
  router.get(route('admin.professeurs.index'), 
    { ...value, search: search.value },
    { preserveState: true, replace: true }
  );
}, { deep: true });
</script>
