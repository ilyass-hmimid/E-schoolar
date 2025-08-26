<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Créer un nouveau pack
        </h2>
        <Link
          :href="route('admin.packs.index')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="space-y-6">
              <!-- Informations de base -->
              <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Informations de base
                </h3>
                <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-4">
                    <label for="nom" class="block text-sm font-medium text-gray-700">
                      Nom du pack <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                      <input
                        type="text"
                        id="nom"
                        v-model="form.nom"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.nom }"
                      />
                      <p v-if="form.errors.nom" class="mt-2 text-sm text-red-600">
                        {{ form.errors.nom }}
                      </p>
                    </div>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700">
                      Type <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                      <select
                        id="type"
                        v-model="form.type"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.type }"
                      >
                        <option v-for="(label, key) in packTypes" :key="key" :value="key">
                          {{ label }}
                        </option>
                      </select>
                      <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.type }}
                      </p>
                    </div>
                  </div>

                  <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                      Description
                    </label>
                    <div class="mt-1">
                      <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.description }"
                      />
                      <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                        {{ form.errors.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tarification -->
              <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Tarification
                </h3>
                <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-2">
                    <label for="prix" class="block text-sm font-medium text-gray-700">
                      Prix (DH) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"> DH </span>
                      </div>
                      <input
                        type="number"
                        id="prix"
                        v-model.number="form.prix"
                        min="0"
                        step="0.01"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 pl-12 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.prix }"
                      />
                    </div>
                    <p v-if="form.errors.prix" class="mt-2 text-sm text-red-600">
                      {{ form.errors.prix }}
                    </p>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="prix_promo" class="block text-sm font-medium text-gray-700">
                      Prix promotionnel (DH)
                    </label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"> DH </span>
                      </div>
                      <input
                        type="number"
                        id="prix_promo"
                        v-model.number="form.prix_promo"
                        min="0"
                        step="0.01"
                        class="block w-full pr-12 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 pl-12 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.prix_promo }"
                      />
                    </div>
                    <p v-if="form.errors.prix_promo" class="mt-2 text-sm text-red-600">
                      {{ form.errors.prix_promo }}
                    </p>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="duree_jours" class="block text-sm font-medium text-gray-700">
                      Durée (jours) <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                      <input
                        type="number"
                        id="duree_jours"
                        v-model.number="form.duree_jours"
                        min="1"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.duree_jours }"
                      />
                      <p v-if="form.errors.duree_jours" class="mt-2 text-sm text-red-600">
                        {{ form.errors.duree_jours }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Options -->
              <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Options
                </h3>
                <div class="mt-6 space-y-4">
                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="est_actif"
                        v-model="form.est_actif"
                        type="checkbox"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="est_actif" class="font-medium text-gray-700">
                        Pack actif
                      </label>
                      <p class="text-gray-500">
                        Ce pack sera visible par les utilisateurs
                      </p>
                    </div>
                  </div>

                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="est_populaire"
                        v-model="form.est_populaire"
                        type="checkbox"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="est_populaire" class="font-medium text-gray-700">
                        Mettre en avant
                      </label>
                      <p class="text-gray-500">
                        Ce pack sera mis en avant sur la page d'accueil
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Boutons de soumission -->
              <div class="flex justify-end px-6 py-3 space-x-3 text-right bg-gray-50 sm:px-6">
                <Link
                  :href="route('admin.packs.index')"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Annuler
                </Link>
                <button
                  type="submit"
                  class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="form.processing"
                  :class="{ 'opacity-50': form.processing }"
                >
                  <span v-if="form.processing">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enregistrement...
                  </span>
                  <span v-else>
                    Créer le pack
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

// Types de packs
const packTypes = {
  'cours': 'Cours',
  'abonnement': 'Abonnement',
  'formation': 'Formation',
  'autre': 'Autre'
};

const form = useForm({
  nom: '',
  description: '',
  type: 'cours',
  prix: 0,
  prix_promo: null,
  duree_jours: 30,
  est_actif: true,
  est_populaire: false
});

const submit = () => {
  form.post(route('admin.packs.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Gérer la réussite (peut-être une notification)
    },
  });
};
</script>
