<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Mon profil</h1>
          <p class="mt-2 text-gray-600">Mettez à jour vos informations personnelles</p>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Adresse email</label>
                <input
                  type="email"
                  v-model="form.email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                <input
                  type="tel"
                  v-model="form.phone"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <input
                  type="text"
                  v-model="form.address"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                <input
                  type="password"
                  v-model="form.password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <p class="mt-1 text-xs text-gray-500">Laissez vide pour ne pas modifier</p>
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input
                  type="password"
                  v-model="form.password_confirmation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
              </div>
            </div>

            <div class="mt-8 flex justify-end">
              <Link 
                :href="route('professeur.dashboard')" 
                class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors mr-3"
              >
                Annuler
              </Link>
              <button 
                type="submit" 
                :disabled="form.processing"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                <span v-if="form.processing">Enregistrement...</span>
                <span v-else>Enregistrer les modifications</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
  password: '',
  password_confirmation: ''
});

const submit = () => {
  form.put(route('professeur.profil.update'), {
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
    }
  });
};
</script>
