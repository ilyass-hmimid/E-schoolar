<template>
  <AdminLayout title="Ajouter un élève">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Ajouter un nouvel élève
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Remplissez les informations de l'élève
          </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
          <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
              <div class="space-y-6 sm:space-y-5">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Nom complet <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                      type="text"
                      id="name"
                      v-model="form.name"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                      required
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                      {{ form.errors.name }}
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="email" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Email <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                      type="email"
                      id="email"
                      v-model="form.email"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                      required
                    />
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                      {{ form.errors.email }}
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="phone" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Téléphone
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                      type="tel"
                      id="phone"
                      v-model="form.phone"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                    />
                    <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600">
                      {{ form.errors.phone }}
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="niveau_id" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Niveau <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select
                      id="niveau_id"
                      v-model="form.niveau_id"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                      required
                    >
                      <option value="">Sélectionnez un niveau</option>
                      <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
                        {{ niveau.nom }}
                      </option>
                    </select>
                    <p v-if="form.errors.niveau_id" class="mt-2 text-sm text-red-600">
                      {{ form.errors.niveau_id }}
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="filiere_id" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Filière
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select
                      id="filiere_id"
                      v-model="form.filiere_id"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                    >
                      <option value="">Sélectionnez une filière</option>
                      <option v-for="filiere in filieres" :key="filiere.id" :value="filiere.id">
                        {{ filiere.nom }}
                      </option>
                    </select>
                    <p v-if="form.errors.filiere_id" class="mt-2 text-sm text-red-600">
                      {{ form.errors.filiere_id }}
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="somme_a_payer" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Montant à payer (MAD)
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                      type="number"
                      id="somme_a_payer"
                      v-model.number="form.somme_a_payer"
                      min="0"
                      step="0.01"
                      class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md"
                    />
                    <p v-if="form.errors.somme_a_payer" class="mt-2 text-sm text-red-600">
                      {{ form.errors.somme_a_payer }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="pt-5">
              <div class="flex justify-end">
                <Link
                  :href="route('admin.eleves.index')"
                  class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Annuler
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  <span v-if="form.processing">
                    Enregistrement...
                  </span>
                  <span v-else>
                    Enregistrer
                  </span>
                </button>
              </div>
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
import { Link } from '@inertiajs/vue3';

defineProps({
  niveaux: {
    type: Array,
    required: true
  },
  filieres: {
    type: Array,
    required: true
  }
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
  niveau_id: '',
  filiere_id: '',
  somme_a_payer: 0,
  password: 'password', // Mot de passe par défaut
  password_confirmation: 'password',
  role: 'eleve'
});

const submit = () => {
  form.post(route('admin.eleves.store'), {
    onSuccess: () => {
      form.reset();
    },
    preserveScroll: true
  });
};
</script>
