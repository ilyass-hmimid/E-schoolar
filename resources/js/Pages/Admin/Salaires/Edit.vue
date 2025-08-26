<template>
  <AppLayout :title="`Modifier le salaire - ${salaire.professeur.name}`">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Modifier le salaire
        </h2>
        <Link 
          :href="route('admin.salaires.show', salaire.id)" 
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <ArrowLeftIcon class="w-5 h-5 mr-2 -ml-1" />
          Retour aux détails
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="updateSalaire">
              <div class="space-y-6">
                <!-- Informations générales -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-sm font-medium text-gray-500 uppercase">Informations générales</h3>
                  <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                    <div>
                      <label for="professeur_id" class="block text-sm font-medium text-gray-700">
                        Professeur
                      </label>
                      <select 
                        id="professeur_id" 
                        v-model="form.professeur_id"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :disabled="true"
                      >
                        <option v-for="prof in professeurs" :key="prof.id" :value="prof.id">
                          {{ prof.name }}
                        </option>
                      </select>
                      <p v-if="form.errors.professeur_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.professeur_id }}
                      </p>
                    </div>
                    
                    <div>
                      <label for="matiere_id" class="block text-sm font-medium text-gray-700">
                        Matière
                      </label>
                      <select 
                        id="matiere_id" 
                        v-model="form.matiere_id"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :disabled="true"
                      >
                        <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                          {{ matiere.nom }}
                        </option>
                      </select>
                      <p v-if="form.errors.matiere_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.matiere_id }}
                      </p>
                    </div>
                    
                    <div>
                      <label for="mois_periode" class="block text-sm font-medium text-gray-700">
                        Période
                      </label>
                      <input 
                        type="month" 
                        id="mois_periode" 
                        v-model="form.mois_periode" 
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :disabled="true"
                      />
                      <p v-if="form.errors.mois_periode" class="mt-1 text-sm text-red-600">
                        {{ form.errors.mois_periode }}
                      </p>
                    </div>
                    
                    <div>
                      <label for="nombre_eleves" class="block text-sm font-medium text-gray-700">
                        Nombre d'élèves
                      </label>
                      <input 
                        type="number" 
                        id="nombre_eleves" 
                        v-model.number="form.nombre_eleves"
                        min="1"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                      <p v-if="form.errors.nombre_eleves" class="mt-1 text-sm text-red-600">
                        {{ form.errors.nombre_eleves }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- Détails du calcul -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-sm font-medium text-gray-500 uppercase">Détails du calcul</h3>
                  <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                    <div>
                      <label for="prix_unitaire" class="block text-sm font-medium text-gray-700">
                        Prix unitaire (DH)
                      </label>
                      <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500 sm:text-sm"> DH </span>
                        </div>
                        <input 
                          type="number" 
                          id="prix_unitaire" 
                          v-model.number="form.prix_unitaire"
                          step="0.01"
                          min="0"
                          class="block w-full pl-12 pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <span class="text-gray-500 sm:text-sm"> /élève </span>
                        </div>
                      </div>
                      <p v-if="form.errors.prix_unitaire" class="mt-1 text-sm text-red-600">
                        {{ form.errors.prix_unitaire }}
                      </p>
                    </div>
                    
                    <div>
                      <label for="commission_prof" class="block text-sm font-medium text-gray-700">
                        Commission professeur (%)
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
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <span class="text-gray-500 sm:text-sm" id="price-currency"> % </span>
                        </div>
                      </div>
                      <p v-if="form.errors.commission_prof" class="mt-1 text-sm text-red-600">
                        {{ form.errors.commission_prof }}
                      </p>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        Montant brut
                      </label>
                      <div class="px-3 py-2 mt-1 text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-md">
                        {{ formatCurrency(montantBrut) }}
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        Montant commission
                      </label>
                      <div class="px-3 py-2 mt-1 text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-md">
                        -{{ formatCurrency(montantCommission) }}
                      </div>
                    </div>
                    
                    <div class="col-span-2 pt-2 mt-2 border-t border-gray-200">
                      <div class="flex justify-between">
                        <label class="text-base font-medium text-gray-900">
                          Montant net
                        </label>
                        <div class="text-base font-bold text-gray-900">
                          {{ formatCurrency(montantNet) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Commentaires -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-sm font-medium text-gray-500 uppercase">Commentaires</h3>
                  <div class="mt-4">
                    <label for="commentaires" class="block text-sm font-medium text-gray-700">
                      Notes internes
                    </label>
                    <div class="mt-1">
                      <textarea 
                        id="commentaires" 
                        v-model="form.commentaires"
                        rows="3"
                        class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Ajoutez des notes ou des commentaires..."
                      ></textarea>
                    </div>
                    <p v-if="form.errors.commentaires" class="mt-1 text-sm text-red-600">
                      {{ form.errors.commentaires }}
                    </p>
                  </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-end pt-5">
                  <Link 
                    :href="route('admin.salaires.show', salaire.id)" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Annuler
                  </Link>
                  <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <template v-if="form.processing">
                      <svg class="w-5 h-5 mr-2 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Enregistrement...
                    </template>
                    <template v-else>
                      <CheckIcon class="w-5 h-5 mr-2 -ml-1" />
                      Enregistrer les modifications
                    </template>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon, CheckIcon } from '@heroicons/vue/outline';

const props = defineProps({
  salaire: {
    type: Object,
    required: true
  },
  professeurs: {
    type: Array,
    default: () => []
  },
  matieres: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

// Initialisation du formulaire avec les données du salaire
const form = useForm({
  professeur_id: props.salaire.professeur_id,
  matiere_id: props.salaire.matiere_id,
  mois_periode: props.salaire.mois_periode,
  nombre_eleves: props.salaire.nombre_eleves,
  prix_unitaire: parseFloat(props.salaire.prix_unitaire).toFixed(2),
  commission_prof: parseFloat(props.salaire.commission_prof).toFixed(2),
  commentaires: props.salaire.commentaires || ''
});

// Calcul des montants
const montantBrut = computed(() => {
  return (form.nombre_eleves * parseFloat(form.prix_unitaire || 0));
});

const montantCommission = computed(() => {
  return montantBrut.value * (parseFloat(form.commission_prof || 0) / 100);
});

const montantNet = computed(() => {
  return montantBrut.value - montantCommission.value;
});

// Formater une valeur monétaire
const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', { 
    style: 'currency', 
    currency: 'MAD',
    minimumFractionDigits: 2
  }).format(value);
};

// Mettre à jour le formulaire
const updateSalaire = () => {
  form.put(route('admin.salaires.update', props.salaire.id), {
    onSuccess: () => {
      // Rediriger vers la page de détail avec un message de succès
      router.visit(route('admin.salaires.show', props.salaire.id), {
        only: ['salaire'],
        onSuccess: () => {
          // Le message de succès sera géré par le contrôleur
        }
      });
    }
  });
};

// Mettre à jour les calculs lorsque les champs changent
watch([
  () => form.nombre_eleves,
  () => form.prix_unitaire,
  () => form.commission_prof
], () => {
  // Forcer la mise à jour des valeurs numériques
  form.nombre_eleves = parseInt(form.nombre_eleves) || 0;
  form.prix_unitaire = parseFloat(form.prix_unitaire) || 0;
  form.commission_prof = parseFloat(form.commission_prof) || 0;
  
  // Limiter la commission à 100%
  if (form.commission_prof > 100) {
    form.commission_prof = 100;
  }
  
  // S'assurer que les valeurs sont positives
  if (form.nombre_eleves < 0) form.nombre_eleves = 0;
  if (form.prix_unitaire < 0) form.prix_unitaire = 0;
  if (form.commission_prof < 0) form.commission_prof = 0;
});
</script>
