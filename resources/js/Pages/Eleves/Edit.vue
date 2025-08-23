<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Modifier l'élève
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Mettez à jour les informations de l'élève.
              </p>
            </div>
            <div class="flex space-x-2">
              <Link 
                :href="route('eleves.show', eleve.id)" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Voir
              </Link>
            </div>
          </div>
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
              
              <div>
                <label class="flex items-center">
                  <input 
                    type="checkbox" 
                    v-model="form.is_active"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  >
                  <span class="ml-2 text-sm text-gray-600">Compte actif</span>
                </label>
                <p v-if="form.errors.is_active" class="mt-1 text-sm text-red-600">
                  {{ form.errors.is_active }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Section Mise à jour du mot de passe -->
          <div class="mb-8">
            <h4 class="text-md font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
              Mise à jour du mot de passe
            </h4>
            <p class="text-sm text-gray-500 mb-4">
              Laissez ces champs vides si vous ne souhaitez pas modifier le mot de passe.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                  Nouveau mot de passe
                </label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                  {{ form.errors.password }}
                </p>
              </div>
              
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                  Confirmer le nouveau mot de passe
                </label>
                <input
                  type="password"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>
          
          <!-- Boutons d'action -->
          <div class="flex justify-between pt-5 border-t border-gray-200">
            <button
              type="button"
              @click="confirmDelete"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Supprimer cet élève
            </button>
            
            <div class="space-x-3">
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
                {{ form.processing ? 'Mise à jour en cours...' : 'Mettre à jour' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Confirmer la suppression
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer définitivement cet élève ? Cette action est irréversible.
      </template>
      <template #footer>
        <button 
          @click="showDeleteModal = false" 
          class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Annuler
        </button>
        <button 
          @click="deleteEleve" 
          class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          Supprimer définitivement
        </button>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  eleve: {
    type: Object,
    required: true
  },
  niveaux: {
    type: Array,
    required: true
  },
  filieres: {
    type: Array,
    required: true
  }
});

const showDeleteModal = ref(false);

const form = useForm({
  // Informations personnelles
  name: props.eleve.name,
  email: props.eleve.email,
  phone: props.eleve.phone || '',
  address: props.eleve.address || '',
  date_naissance: props.eleve.date_naissance || '',
  lieu_naissance: props.eleve.lieu_naissance || '',
  
  // Informations scolaires
  niveau_id: props.eleve.niveau_id,
  filiere_id: props.eleve.filiere_id,
  date_inscription: props.eleve.date_inscription ? formatDate(props.eleve.date_inscription) : '',
  somme_a_payer: props.eleve.somme_a_payer || 0,
  is_active: props.eleve.is_active,
  
  // Mot de passe (vide par défaut)
  password: '',
  password_confirmation: ''
});

const submit = () => {
  form.put(route('eleves.update', props.eleve.id));
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteEleve = () => {
  router.delete(route('eleves.destroy', props.eleve.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
    }
  });
};

// Formater la date au format YYYY-MM-DD pour l'input date
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toISOString().split('T')[0];
};
</script>
