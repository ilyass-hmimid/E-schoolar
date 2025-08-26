<template>
  <AppLayout title="Calcul des salaires">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Calcul des salaires
        </h2>
        <Link 
          :href="route('admin.salaires.index')" 
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <ArrowLeftIcon class="w-5 h-5 mr-2 -ml-1" />
          Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="mb-8">
              <h3 class="text-lg font-medium">Paramètres de calcul</h3>
              <p class="mt-1 text-sm text-gray-500">
                Sélectionnez le mois pour lequel vous souhaitez calculer les salaires.
              </p>
            </div>

            <div class="max-w-md space-y-6">
              <div>
                <label for="mois_periode" class="block text-sm font-medium text-gray-700">
                  Mois de paie
                </label>
                <div class="mt-1">
                  <input 
                    type="month" 
                    id="mois_periode" 
                    v-model="form.mois_periode" 
                    :max="currentMonth"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <p v-if="form.errors.mois_periode" class="mt-1 text-sm text-red-600">
                  {{ form.errors.mois_periode }}
                </p>
              </div>

              <div v-if="lastCalculationDate" class="p-4 bg-yellow-50 rounded-md">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <ExclamationIcon class="w-5 h-5 text-yellow-400" />
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                      Dernier calcul effectué
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                      <p>
                        Un calcul a déjà été effectué pour ce mois le {{ formatDate(lastCalculationDate) }}.
                      </p>
                      <p class="mt-1">
                        Le recalcul écrasera les données existantes pour ce mois.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex items-center pt-6 space-x-4">
                <button
                  type="button"
                  @click="calculerSalaires"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <CalculatorIcon class="w-5 h-5 mr-2 -ml-1" />
                  {{ form.processing ? 'Calcul en cours...' : 'Calculer les salaires' }}
                </button>

                <Link 
                  :href="route('admin.salaires.index')" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Annuler
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  ArrowLeftIcon, 
  CalculatorIcon,
  ExclamationIcon 
} from '@heroicons/vue/outline';

const props = defineProps({
  lastCalculationDate: {
    type: String,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

// Formulaires
const form = useForm({
  mois_periode: new Date().toISOString().slice(0, 7), // Format YYYY-MM
});

// Mois en cours au format YYYY-MM
const currentMonth = computed(() => {
  return new Date().toISOString().slice(0, 7);
});

// Formater une date
const formatDate = (dateString) => {
  if (!dateString) return '';
  
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Calculer les salaires
const calculerSalaires = () => {
  router.post(route('admin.salaires.calculer'), {
    mois_periode: form.mois_periode
  }, {
    onSuccess: () => {
      // Rediriger vers la liste des salaires avec un message de succès
      router.visit(route('admin.salaires.index', { 
        mois_periode: form.mois_periode 
      }), {
        only: ['salaires', 'filters'],
        onSuccess: () => {
          // Afficher un message de succès via Inertia
          // (le composant parent gérera l'affichage du message)
        }
      });
    },
    onError: (errors) => {
      // Les erreurs seront affichées automatiquement via le système de validation
    }
  });
};

// Mettre à jour la date de dernier calcul si le mois change
const updateLastCalculationDate = async () => {
  if (!form.mois_periode) return;
  
  try {
    const response = await fetch(route('admin.salaires.dernier-calcul', {
      mois_periode: form.mois_periode
    }));
    
    if (response.ok) {
      const data = await response.json();
      lastCalculationDate.value = data.last_calculation_date;
    }
  } catch (error) {
    console.error('Erreur lors de la récupération du dernier calcul:', error);
  }
};

// Écouter les changements sur le mois sélectionné
watch(() => form.mois_periode, (newVal, oldVal) => {
  if (newVal && newVal !== oldVal) {
    updateLastCalculationDate();
  }
});

// Initialiser avec le mois actuel
onMounted(() => {
  updateLastCalculationDate();
});
</script>
