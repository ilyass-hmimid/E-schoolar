<template>
  <AppLayout title="Gestion des Utilisateurs">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des Utilisateurs
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Liste des Utilisateurs</h3>
            <Link 
              :href="route('admin.users.create')" 
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Ajouter un Utilisateur
            </Link>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nom
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rôle
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img class="h-10 w-10 rounded-full" :src="user.avatar || '/images/default-avatar.png'" :alt="user.name">
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ user.name }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                      :class="{
                        'bg-green-100 text-green-800': user.role === 1,
                        'bg-blue-100 text-blue-800': user.role === 2,
                        'bg-yellow-100 text-yellow-800': user.role === 3,
                        'bg-purple-100 text-purple-800': user.role === 4
                      }"
                    >
                      {{ user.role_label }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="user.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      Actif
                    </span>
                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                      Inactif
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                      Modifier
                    </Link>
                    <button @click="confirmUserDeletion(user)" class="text-red-600 hover:text-red-900">
                      Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <ConfirmationModal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
      <template #title>
        Supprimer l'utilisateur
      </template>

      <template #content>
        Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
      </template>

      <template #footer>
        <SecondaryButton @click="confirmingUserDeletion = false">
          Annuler
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteUser"
        >
          Supprimer l'utilisateur
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

const props = defineProps({
  users: {
    type: Array,
    required: true,
  },
});

const confirmingUserDeletion = ref(false);
const userToDelete = ref(null);

const form = useForm({});

const confirmUserDeletion = (user) => {
  userToDelete.value = user;
  confirmingUserDeletion.value = true;
};

const deleteUser = () => {
  if (!userToDelete.value) return;
  
  form.delete(route('admin.users.destroy', userToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      confirmingUserDeletion.value = false;
      userToDelete.value = null;
    },
  });
};
</script>
