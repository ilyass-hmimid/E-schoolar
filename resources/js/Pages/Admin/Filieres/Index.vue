<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Gestion des Filières
        </h2>
        <div class="flex space-x-2">
          <select
            v-model="selectedNiveau"
            @change="filterFilieres"
            class="block px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option :value="null">Tous les niveaux</option>
            <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
              {{ niveau.nom }}
            </option>
          </select>
          <Link
            :href="route('admin.filieres.create')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PlusIcon class="w-5 h-5 mr-2 -ml-1" />
            Nouvelle filière
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Liste des filières -->
          <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Nom
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Niveau
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Matières
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
                <tr v-for="filiere in filieres.data" :key="filiere.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ filiere.nom }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ filiere.abreviation || 'Aucune abréviation' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ filiere.niveau.nom }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="matiere in filiere.matieres"
                        :key="matiere.id"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        {{ matiere.nom }}
                      </span>
                      <span v-if="filiere.matieres.length === 0" class="text-sm text-gray-500">
                        Aucune matière
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'bg-green-100 text-green-800': filiere.est_actif,
                        'bg-red-100 text-red-800': !filiere.est_actif
                      }"
                      class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                    >
                      {{ filiere.est_actif ? 'Actif' : 'Inactif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                    <Link
                      :href="route('admin.filieres.edit', filiere.id)"
                      class="mr-3 text-indigo-600 hover:text-indigo-900"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="confirmDelete(filiere)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr v-if="filieres.data.length === 0">
                  <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Aucune filière trouvée
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination :links="filieres.links" class="mt-4" />
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer la filière
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer cette filière ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deleteFiliere"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { PlusIcon } from '@heroicons/vue/outline';

const props = defineProps({
  filieres: Object,
  niveaux: Array,
  filters: Object
});

const showDeleteModal = ref(false);
const selectedFiliere = ref(null);
const selectedNiveau = ref(props.filters.niveau_id || null);

const deleteForm = useForm({});

const confirmDelete = (filiere) => {
  selectedFiliere.value = filiere;
  showDeleteModal.value = true;
};

const deleteFiliere = () => {
  deleteForm.delete(route('admin.filieres.destroy', selectedFiliere.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};

// Filtrer les filières par niveau
const filterFilieres = () => {
  router.get(route('admin.filieres.index'), 
    { niveau_id: selectedNiveau.value },
    { preserveState: true, preserveScroll: true }
  );
};
</script>
