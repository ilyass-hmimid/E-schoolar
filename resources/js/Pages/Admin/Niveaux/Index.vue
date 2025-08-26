<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Gestion des Niveaux
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="flex justify-end mb-6">
            <Link
              :href="route('admin.niveaux.create')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <PlusIcon class="w-5 h-5 mr-2 -ml-1" />
              Ajouter un niveau
            </Link>
          </div>

          <!-- Liste des niveaux -->
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
                    Type
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                  >
                    Ordre d'affichage
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="niveau in niveaux.data" :key="niveau.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ niveau.nom }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                      :class="{
                        'bg-green-100 text-green-800': niveau.type === 'primaire',
                        'bg-blue-100 text-blue-800': niveau.type === 'college',
                        'bg-purple-100 text-purple-800': niveau.type === 'tronc_commun',
                        'bg-yellow-100 text-yellow-800': niveau.type === 'premiere_bac',
                        'bg-red-100 text-red-800': niveau.type === 'deuxieme_bac'
                      }"
                    >
                      {{ getTypeLabel(niveau.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">
                      {{ niveau.ordre }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                    <Link
                      :href="route('admin.niveaux.edit', niveau.id)"
                      class="mr-3 text-indigo-600 hover:text-indigo-900"
                    >
                      Modifier
                    </Link>
                    <button
                      @click="confirmDelete(niveau)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr v-if="niveaux.data.length === 0">
                  <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    Aucun niveau trouvé
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination :links="niveaux.links" class="mt-4" />
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer le niveau
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer ce niveau ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deleteNiveau"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { PlusIcon } from '@heroicons/vue/outline';

const props = defineProps({
  niveaux: Object,
});

const showDeleteModal = ref(false);
const selectedNiveau = ref(null);

const deleteForm = useForm({});

const confirmDelete = (niveau) => {
  selectedNiveau.value = niveau;
  showDeleteModal.value = true;
};

const deleteNiveau = () => {
  deleteForm.delete(route('admin.niveaux.destroy', selectedNiveau.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};

const getTypeLabel = (type) => {
  const types = {
    primaire: 'Primaire',
    college: 'Collège',
    tronc_commun: 'Tronc Commun',
    premiere_bac: '1ère Bac',
    deuxieme_bac: '2ème Bac'
  };
  return types[type] || type;
};
</script>
