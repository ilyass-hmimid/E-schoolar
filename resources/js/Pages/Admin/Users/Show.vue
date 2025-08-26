<template>
  <AppLayout :title="`Détails de l'utilisateur - ${user.name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Détails de l'utilisateur : {{ user.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex flex-col md:flex-row gap-8">
            <!-- User Avatar -->
            <div class="w-full md:w-1/3 lg:w-1/4">
              <div class="bg-gray-100 rounded-lg p-4 flex flex-col items-center">
                <img 
                  :src="user.avatar || '/images/default-avatar.png'" 
                  :alt="user.name" 
                  class="w-32 h-32 rounded-full object-cover mb-4"
                >
                <h3 class="text-xl font-semibold text-gray-900">{{ user.name }}</h3>
                <span 
                  class="mt-2 px-3 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': user.role === 1,
                    'bg-blue-100 text-blue-800': user.role === 2,
                    'bg-yellow-100 text-yellow-800': user.role === 3,
                    'bg-purple-100 text-purple-800': user.role === 4
                  }"
                >
                  {{ user.role_label }}
                </span>
                <span 
                  class="mt-2 px-3 py-1 rounded-full text-xs font-medium"
                  :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ user.is_active ? 'Actif' : 'Inactif' }}
                </span>
              </div>

              <div class="mt-6 space-y-4">
                <Link 
                  :href="route('admin.users.edit', user.id)" 
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                  </svg>
                  Modifier le profil
                </Link>
                
                <button 
                  @click="confirmUserDeletion"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  Supprimer l'utilisateur
                </button>
              </div>
            </div>

            <!-- User Details -->
            <div class="w-full md:w-2/3 lg:w-3/4">
              <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Informations personnelles
                  </h3>
                </div>
                <div class="border-t border-gray-200">
                  <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        Nom complet
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ user.name }}
                      </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        Adresse email
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ user.email }}
                      </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        Téléphone
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ user.phone || 'Non renseigné' }}
                      </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        Adresse
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ user.address || 'Non renseignée' }}
                      </dd>
                    </div>
                    
                    <!-- Student Specific Info -->
                    <template v-if="user.role === 4">
                      <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                          Niveau
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ user.niveau || 'Non défini' }}
                        </dd>
                      </div>
                      <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                          Filière
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ user.filiere || 'Non définie' }}
                        </dd>
                      </div>
                      <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                          Somme à payer
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ user.somme_a_payer ? `${user.somme_a_payer} DH` : 'Non définie' }}
                        </dd>
                      </div>
                      <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                          Date de début
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ user.date_debut || 'Non définie' }}
                        </dd>
                      </div>
                    </template>
                    
                    <!-- Teacher Specific Info -->
                    <template v-if="user.role === 2">
                      <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                          Spécialité
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                          {{ user.specialite || 'Non définie' }}
                        </dd>
                      </div>
                    </template>
                    
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">
                        Date d'inscription
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ user.created_at }}
                      </dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>
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
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const confirmingUserDeletion = ref(false);
const form = useForm({});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
};

const deleteUser = () => {
  form.delete(route('admin.users.destroy', props.user.id), {
    preserveScroll: true,
    onSuccess: () => {
      confirmingUserDeletion.value = false;
    },
  });
};
</script>
