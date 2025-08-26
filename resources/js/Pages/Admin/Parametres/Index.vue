<template>
  <AdminLayout title="Paramètres">
    <div class="space-y-6">
      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Paramètres généraux</h3>
          
          <form @submit.prevent="submit" class="mt-5 space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label for="nom_ecole" class="block text-sm font-medium text-gray-700">
                  Nom de l'école
                </label>
                <div class="mt-1">
                  <input
                    type="text"
                    id="nom_ecole"
                    v-model="form.nom_ecole"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Le nom qui sera affiché dans l'application
                  </p>
                </div>
              </div>

              <div>
                <label for="email_contact" class="block text-sm font-medium text-gray-700">
                  Email de contact
                </label>
                <div class="mt-1">
                  <input
                    type="email"
                    id="email_contact"
                    v-model="form.email_contact"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Email utilisé pour les notifications
                  </p>
                </div>
              </div>

              <div>
                <label for="telephone_contact" class="block text-sm font-medium text-gray-700">
                  Téléphone de contact
                </label>
                <div class="mt-1">
                  <input
                    type="tel"
                    id="telephone_contact"
                    v-model="form.telephone_contact"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Numéro de téléphone de l'établissement
                  </p>
                </div>
              </div>

              <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700">
                  Adresse
                </label>
                <div class="mt-1">
                  <textarea
                    id="adresse"
                    v-model="form.adresse"
                    rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  ></textarea>
                  <p class="mt-1 text-sm text-gray-500">
                    Adresse complète de l'établissement
                  </p>
                </div>
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrement...
                </span>
                <span v-else>Enregistrer les modifications</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { onMounted } from 'vue';

const props = defineProps({
  parametres: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  nom_ecole: props.parametres.nom_ecole || '',
  email_contact: props.parametres.email_contact || '',
  telephone_contact: props.parametres.telephone_contact || '',
  adresse: props.parametres.adresse || '',
});

const submit = () => {
  form.put(route('admin.parametres.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Optionally show success message
    },
  });
};
</script>
