<template>
  <AppLayout title="Configuration des salaires">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Configuration des salaires
        </h2>
        <Link 
          :href="route('admin.salaires.index')" 
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <ArrowLeftIcon class="w-5 h-5 mr-2 -ml-1" />
          Retour aux salaires
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="p-6">
            <!-- En-tête avec bouton d'ajout -->
            <div class="flex flex-col justify-between mb-6 space-y-4 sm:flex-row sm:items-center sm:space-y-0">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Configurations des salaires par matière
                </h3>
                <p class="max-w-2xl mt-1 text-sm text-gray-500">
                  Gérez les paramètres de rémunération pour chaque matière enseignée.
                </p>
              </div>
              
              <button
                @click="openCreateModal"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="w-5 h-5 mr-2 -ml-1" />
                Ajouter une configuration
              </button>
            </div>
            
            <!-- Liste des configurations -->
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                  <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Matière
                          </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Prix unitaire
                          </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Commission
                          </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Prix min
                          </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Prix max
                          </th>
                          <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                            Statut
                          </th>
                          <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="configurations.data.length === 0">
                          <td colspan="7" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                            Aucune configuration trouvée.
                          </td>
                        </tr>
                        
                        <tr v-for="config in configurations.data" :key="config.id" class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              {{ config.matiere.nom }}
                            </div>
                            <div v-if="config.description" class="text-xs text-gray-500 line-clamp-2">
                              {{ config.description }}
                            </div>
                          </td>
                          <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                            {{ formatCurrency(config.prix_unitaire) }}
                          </td>
                          <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                            {{ config.commission_prof }}%
                          </td>
                          <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                            {{ formatCurrency(config.prix_min) }}
                          </td>
                          <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                            {{ formatCurrency(config.prix_max) }}
                          </td>
                          <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                            <span :class="getStatusBadgeClass(config.est_actif)">
                              {{ config.est_actif ? 'Actif' : 'Inactif' }}
                            </span>
                          </td>
                          <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <button
                              @click="editConfig(config)"
                              class="text-indigo-600 hover:text-indigo-900"
                              title="Modifier"
                            >
                              <PencilIcon class="w-5 h-5" />
                            </button>
                            <button
                              @click="confirmDelete(config)"
                              class="ml-3 text-red-600 hover:text-red-900"
                              title="Supprimer"
                            >
                              <TrashIcon class="w-5 h-5" />
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <!-- Pagination -->
                  <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-gray-700">
                      Affichage de <span class="font-medium">{{ configurations.from || 0 }}</span> à 
                      <span class="font-medium">{{ configurations.to || 0 }}</span> sur 
                      <span class="font-medium">{{ configurations.total }}</span> configurations
                    </div>
                    
                    <Pagination :links="configurations.links" />
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Matières non configurées -->
            <div v-if="matieresSansConfig.length > 0" class="mt-10">
              <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">
                Matières sans configuration
              </h3>
              
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div 
                  v-for="matiere in matieresSansConfig" 
                  :key="matiere.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <span class="text-sm font-medium text-gray-900">
                    {{ matiere.nom }}
                  </span>
                  <button
                    @click="createConfigForMatiere(matiere)"
                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de création/édition -->
    <Modal :show="showModal" @close="closeModal" maxWidth="2xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          {{ isEditing ? 'Modifier la configuration' : 'Nouvelle configuration' }}
        </h2>
        
        <form @submit.prevent="isEditing ? updateConfig() : createConfig()" class="mt-6 space-y-6">
          <!-- Matière -->
          <div>
            <label for="matiere_id" class="block text-sm font-medium text-gray-700">
              Matière <span class="text-red-500">*</span>
            </label>
            <select
              id="matiere_id"
              v-model="form.matiere_id"
              :disabled="isEditing"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              :class="{ 'bg-gray-100': isEditing }"
              required
            >
              <option value="">Sélectionnez une matière</option>
              <option 
                v-for="matiere in availableMatieres" 
                :key="matiere.id" 
                :value="matiere.id"
              >
                {{ matiere.nom }}
              </option>
            </select>
            <p v-if="form.errors.matiere_id" class="mt-1 text-sm text-red-600">
              {{ form.errors.matiere_id }}
            </p>
          </div>
          
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Prix unitaire -->
            <div>
              <label for="prix_unitaire" class="block text-sm font-medium text-gray-700">
                Prix unitaire (DH) <span class="text-red-500">*</span>
              </label>
              <div class="relative mt-1 rounded-md shadow-sm">
                <input
                  type="number"
                  id="prix_unitaire"
                  v-model.number="form.prix_unitaire"
                  step="0.01"
                  min="0"
                  class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"> DH </span>
                </div>
              </div>
              <p v-if="form.errors.prix_unitaire" class="mt-1 text-sm text-red-600">
                {{ form.errors.prix_unitaire }}
              </p>
            </div>
            
            <!-- Commission -->
            <div>
              <label for="commission_prof" class="block text-sm font-medium text-gray-700">
                Commission professeur (%) <span class="text-red-500">*</span>
              </label>
              <div class="relative mt-1 rounded-md shadow-sm">
                <input
                  type="number"
                  id="commission_prof"
                  v-model.number="form.commission_prof"
                  step="0.01"
                  min="0"
                  max="100"
                  class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"> % </span>
                </div>
              </div>
              <p v-if="form.errors.commission_prof" class="mt-1 text-sm text-red-600">
                {{ form.errors.commission_prof }}
              </p>
            </div>
            
            <!-- Prix min -->
            <div>
              <label for="prix_min" class="block text-sm font-medium text-gray-700">
                Prix minimum (DH)
              </label>
              <div class="relative mt-1 rounded-md shadow-sm">
                <input
                  type="number"
                  id="prix_min"
                  v-model.number="form.prix_min"
                  step="0.01"
                  min="0"
                  class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"> DH </span>
                </div>
              </div>
              <p v-if="form.errors.prix_min" class="mt-1 text-sm text-red-600">
                {{ form.errors.prix_min }}
              </p>
            </div>
            
            <!-- Prix max -->
            <div>
              <label for="prix_max" class="block text-sm font-medium text-gray-700">
                Prix maximum (DH)
              </label>
              <div class="relative mt-1 rounded-md shadow-sm">
                <input
                  type="number"
                  id="prix_max"
                  v-model.number="form.prix_max"
                  step="0.01"
                  min="0"
                  class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"> DH </span>
                </div>
              </div>
              <p v-if="form.errors.prix_max" class="mt-1 text-sm text-red-600">
                {{ form.errors.prix_max }}
              </p>
            </div>
          </div>
          
          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">
              Description
            </label>
            <div class="mt-1">
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Description ou notes supplémentaires..."
              ></textarea>
            </div>
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
              {{ form.errors.description }}
            </p>
          </div>
          
          <!-- Statut -->
          <div class="flex items-center">
            <input
              id="est_actif"
              v-model="form.est_actif"
              type="checkbox"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="est_actif" class="block ml-2 text-sm text-gray-900">
              Configuration active
            </label>
            <p v-if="form.errors.est_actif" class="mt-1 text-sm text-red-600">
              {{ form.errors.est_actif }}
            </p>
          </div>
          
          <!-- Actions du formulaire -->
          <div class="flex justify-end pt-6 space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <template v-if="form.processing">
                <svg class="w-5 h-5 mr-2 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enregistrement...
              </template>
              <template v-else>
                {{ isEditing ? 'Mettre à jour' : 'Créer' }}
              </template>
            </button>
          </div>
        </form>
      </div>
    </Modal>
    
    <!-- Modal de confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer la configuration
      </template>
      <template #content>
        <p>Êtes-vous sûr de vouloir supprimer la configuration pour la matière <span class="font-semibold">{{ selectedConfig?.matiere?.nom }}</span> ?</p>
        <p class="mt-2 text-sm text-red-600">Cette action est irréversible.</p>
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton @click="deleteConfig" class="ml-3">
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { 
  ArrowLeftIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon 
} from '@heroicons/vue/outline';

const props = defineProps({
  configurations: {
    type: Object,
    required: true
  },
  matieresSansConfig: {
    type: Array,
    default: () => []
  },
  allMatieres: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

// État des modales
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);

// Configuration sélectionnée
const selectedConfig = ref(null);

// Formulaire
const form = useForm({
  matiere_id: '',
  prix_unitaire: 0,
  commission_prof: 0,
  prix_min: null,
  prix_max: null,
  description: '',
  est_actif: true
});

// Matières disponibles pour la sélection (celles qui n'ont pas encore de configuration)
const availableMatieres = computed(() => {
  if (isEditing.value && selectedConfig.value) {
    // En mode édition, inclure la matière actuelle
    return [
      { id: selectedConfig.value.matiere_id, nom: selectedConfig.value.matiere.nom },
      ...props.matieresSansConfig
    ];
  }
  return props.matieresSansConfig;
});

// Ouvrir la modale de création
const openCreateModal = () => {
  isEditing.value = false;
  form.reset();
  form.est_actif = true;
  showModal.value = true;
};

// Créer une configuration pour une matière spécifique
const createConfigForMatiere = (matiere) => {
  isEditing.value = false;
  form.reset();
  form.matiere_id = matiere.id;
  form.est_actif = true;
  showModal.value = true;
};

// Éditer une configuration existante
const editConfig = (config) => {
  isEditing.value = true;
  selectedConfig.value = config;
  
  // Remplir le formulaire avec les données de la configuration
  form.matiere_id = config.matiere_id;
  form.prix_unitaire = parseFloat(config.prix_unitaire).toFixed(2);
  form.commission_prof = parseFloat(config.commission_prof).toFixed(2);
  form.prix_min = config.prix_min ? parseFloat(config.prix_min).toFixed(2) : null;
  form.prix_max = config.prix_max ? parseFloat(config.prix_max).toFixed(2) : null;
  form.description = config.description || '';
  form.est_actif = config.est_actif;
  
  showModal.value = true;
};

// Confirmer la suppression d'une configuration
const confirmDelete = (config) => {
  selectedConfig.value = config;
  showDeleteModal.value = true;
};

// Fermer la modale
const closeModal = () => {
  showModal.value = false;
  form.clearErrors();
};

// Créer une nouvelle configuration
const createConfig = () => {
  form.post(route('admin.configuration-salaires.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      // Recharger la page pour actualiser les données
      router.reload({ only: ['configurations', 'matieresSansConfig'] });
    },
  });
};

// Mettre à jour une configuration existante
const updateConfig = () => {
  if (!selectedConfig.value) return;
  
  form.put(route('admin.configuration-salaires.update', selectedConfig.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      // Recharger la page pour actualiser les données
      router.reload({ only: ['configurations', 'matieresSansConfig'] });
    },
  });
};

// Supprimer une configuration
const deleteConfig = () => {
  if (!selectedConfig.value) return;
  
  router.delete(route('admin.configuration-salaires.destroy', selectedConfig.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      // Recharger la page pour actualiser les données
      router.reload({ only: ['configurations', 'matieresSansConfig'] });
    },
  });
};

// Formater une valeur monétaire
const formatCurrency = (value) => {
  if (value === null || value === undefined) return '-';
  
  return new Intl.NumberFormat('fr-FR', { 
    style: 'currency', 
    currency: 'MAD',
    minimumFractionDigits: 2
  }).format(value);
};

// Obtenir la classe CSS pour le badge de statut
const getStatusBadgeClass = (isActive) => {
  return {
    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
    'bg-green-100 text-green-800': isActive,
    'bg-red-100 text-red-800': !isActive
  };
};

// Surveiller les changements sur le formulaire pour ajuster les valeurs
watch(() => form.prix_unitaire, (newVal) => {
  if (newVal < 0) form.prix_unitaire = 0;
  if (form.prix_max && newVal > form.prix_max) form.prix_max = parseFloat(newVal);
  if (form.prix_min && newVal < form.prix_min) form.prix_min = parseFloat(newVal);
});

watch(() => form.commission_prof, (newVal) => {
  if (newVal < 0) form.commission_prof = 0;
  if (newVal > 100) form.commission_prof = 100;
});

watch(() => form.prix_min, (newVal, oldVal) => {
  if (newVal === '') {
    form.prix_min = null;
    return;
  }
  
  if (newVal !== null) {
    const val = parseFloat(newVal);
    if (isNaN(val) || val < 0) {
      form.prix_min = oldVal !== null ? oldVal : 0;
    } else {
      form.prix_min = val;
      if (form.prix_unitaire && val > form.prix_unitaire) {
        form.prix_unitaire = val;
      }
      if (form.prix_max && val > form.prix_max) {
        form.prix_max = val;
      }
    }
  }
});

watch(() => form.prix_max, (newVal, oldVal) => {
  if (newVal === '') {
    form.prix_max = null;
    return;
  }
  
  if (newVal !== null) {
    const val = parseFloat(newVal);
    if (isNaN(val) || val < 0) {
      form.prix_max = oldVal !== null ? oldVal : 0;
    } else {
      form.prix_max = val;
      if (form.prix_unitaire && val < form.prix_unitaire) {
        form.prix_unitaire = val;
      }
      if (form.prix_min && val < form.prix_min) {
        form.prix_min = val;
      }
    }
  }
});
</script>
