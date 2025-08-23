<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Ajouter un nouvel élève
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Remplissez les informations ci-dessous pour inscrire un nouvel élève.
          </p>
        </div>
        
        <form @submit.prevent="submit" class="p-6">
          <!-- Section Informations personnelles -->
          <div class="mb-8">
            <h4 class="text-md font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
              Informations personnelles
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                  Nom complet <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Adresse email <span class="text-red-500">*</span>
                </label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                  {{ form.errors.email }}
                </p>
              </div>
              
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                  Téléphone
                </label>
                <input
                  type="tel"
                  id="phone"
                  v-model="form.phone"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                  {{ form.errors.phone }}
                </p>
              </div>
              
              <div>
                <label for="address" class="block text-sm font-medium text-gray-700">
                  Adresse
                </label>
                <input
                  type="text"
                  id="address"
                  v-model="form.address"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                  {{ form.errors.address }}
                </p>
              </div>
              
              <div>
                <label for="date_naissance" class="block text-sm font-medium text-gray-700">
                  Date de naissance
                </label>
                <input
                  type="date"
                  id="date_naissance"
                  v-model="form.date_naissance"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <p v-if="form.errors.date_naissance" class="mt-1 text-sm text-red-600">
                  {{ form.errors.date_naissance }}
                </p>
              </div>
              
              <div>
                <label for="lieu_naissance" class="block text-sm font-medium text-gray-700">
                  Lieu de naissance
                </label>
                <input
                  type="text"
                  id="lieu_naissance"
                  v-model="form.lieu_naissance"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <p v-if="form.errors.lieu_naissance" class="mt-1 text-sm text-red-600">
                  {{ form.errors.lieu_naissance }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Section Scolarité -->
          <div class="mb-8">
            <h4 class="text-md font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
              Informations scolaires
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="niveau_id" class="block text-sm font-medium text-gray-700">
                  Niveau <span class="text-red-500">*</span>
                </label>
                <select
                  id="niveau_id"
                  v-model="form.niveau_id"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Sélectionner un niveau</option>
                  <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
                    {{ niveau.nom }}
                  </option>
                </select>
                <p v-if="form.errors.niveau_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.niveau_id }}
                </p>
              </div>
              
              <div>
                <label for="filiere_id" class="block text-sm font-medium text-gray-700">
                  Filière <span class="text-red-500">*</span>
                </label>
                <select
                  id="filiere_id"
                  v-model="form.filiere_id"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Sélectionner une filière</option>
                  <option v-for="filiere in filieres" :key="filiere.id" :value="filiere.id">
                    {{ filiere.nom }}
                  </option>
                </select>
                <p v-if="form.errors.filiere_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.filiere_id }}
                </p>
              </div>
              
              <div>
                <label for="date_inscription" class="block text-sm font-medium text-gray-700">
                  Date d'inscription <span class="text-red-500">*</span>
                </label>
                <input
                  type="date"
                  id="date_inscription"
                  v-model="form.date_inscription"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <p v-if="form.errors.date_inscription" class="mt-1 text-sm text-red-600">
                  {{ form.errors.date_inscription }}
                </p>
              </div>
              
              <div>
                <label for="somme_a_payer" class="block text-sm font-medium text-gray-700">
                  Montant à payer (MAD) <span class="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  id="somme_a_payer"
                  v-model="form.somme_a_payer"
                  min="0"
                  step="0.01"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <p v-if="form.errors.somme_a_payer" class="mt-1 text-sm text-red-600">
                  {{ form.errors.somme_a_payer }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Section Compte utilisateur -->
          <div class="mb-8">
            <h4 class="text-md font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
              Paramètres du compte
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                  Mot de passe <span class="text-red-500">*</span>
                </label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                  {{ form.errors.password }}
                </p>
              </div>
              
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                  Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <input
                  type="password"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>
            </div>
          </div>
          
          <!-- Boutons d'action -->
          <div class="flex justify-end space-x-3 pt-5 border-t border-gray-200">
            <Link 
              :href="route('eleves.index')" 
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Annuler
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Création en cours...' : 'Créer l\'élève' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
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
  // Informations personnelles
  name: '',
  email: '',
  phone: '',
  address: '',
  date_naissance: '',
  lieu_naissance: '',
  
  // Informations scolaires
  niveau_id: '',
  filiere_id: '',
  date_inscription: new Date().toISOString().split('T')[0], // Date du jour par défaut
  somme_a_payer: 0,
  
  // Compte utilisateur
  password: '',
  password_confirmation: ''
});

const submit = () => {
  form.post(route('eleves.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};

// Formater la date au format YYYY-MM-DD pour l'input date
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toISOString().split('T')[0];
};
</script>
