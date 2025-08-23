<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle absence</h1>
            <p class="mt-2 text-gray-600">Enregistrer une absence ou un retard</p>
          </div>
          <button
            @click="goBack"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
          >
            Retour
          </button>
        </div>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Élève -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Élève <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.etudiant_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Sélectionner un élève</option>
                <option
                  v-for="eleve in eleves"
                  :key="eleve.id"
                  :value="eleve.id"
                >
                  {{ eleve.name }} ({{ eleve.email }})
                </option>
              </select>
            </div>

            <!-- Matière -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Matière <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.matiere_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Sélectionner une matière</option>
                <option
                  v-for="matiere in matieres"
                  :key="matiere.id"
                  :value="matiere.id"
                >
                  {{ matiere.nom }}
                </option>
              </select>
            </div>

            <!-- Type d'absence -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.type"
                @change="onTypeChange"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Sélectionner un type</option>
                <option
                  v-for="(label, value) in types_absence"
                  :key="value"
                  :value="value"
                >
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Durée du retard (si applicable) -->
            <div v-if="form.type === 'retard'">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Durée du retard (minutes)
              </label>
              <input
                v-model="form.duree_retard"
                type="number"
                min="1"
                max="480"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ex: 15"
              />
            </div>

            <!-- Date et heure -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date et heure <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.date_absence"
                type="datetime-local"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>

            <!-- Motif -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Motif
              </label>
              <textarea
                v-model="form.motif"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Motif de l'absence ou du retard..."
              ></textarea>
            </div>

            <!-- Justifiée -->
            <div>
              <label class="flex items-center">
                <input
                  v-model="form.justifiee"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-700">Absence justifiée</span>
              </label>
            </div>

            <!-- Justification (si justifiée) -->
            <div v-if="form.justifiee" class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Justification
              </label>
              <textarea
                v-model="form.justification"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Détails de la justification..."
              ></textarea>
            </div>
          </div>

          <!-- Résumé -->
          <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé de l'absence</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-700">Élève:</span>
                <span class="ml-2 text-gray-900">{{ selectedEleve?.name || 'Non sélectionné' }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700">Matière:</span>
                <span class="ml-2 text-gray-900">{{ selectedMatiere?.nom || 'Non sélectionnée' }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700">Type:</span>
                <span class="ml-2 text-gray-900">{{ types_absence[form.type] || 'Non sélectionné' }}</span>
              </div>
              <div v-if="form.type === 'retard' && form.duree_retard">
                <span class="font-medium text-gray-700">Durée:</span>
                <span class="ml-2 text-gray-900">{{ form.duree_retard }} minutes</span>
              </div>
              <div>
                <span class="font-medium text-gray-700">Date:</span>
                <span class="ml-2 text-gray-900">{{ formatDateTime(form.date_absence) }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700">Statut:</span>
                <span class="ml-2 text-gray-900">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(form.justifiee)">
                    {{ form.justifiee ? 'Justifiée' : 'Non justifiée' }}
                  </span>
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-8 flex justify-end space-x-4">
            <button
              type="button"
              @click="goBack"
              class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="!isFormValid"
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Enregistrer l'absence
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  eleves: Array,
  matieres: Array,
  types_absence: Object,
});

const form = reactive({
  etudiant_id: '',
  matiere_id: '',
  type: '',
  duree_retard: '',
  date_absence: new Date().toISOString().slice(0, 16),
  motif: '',
  justifiee: false,
  justification: '',
});

const selectedEleve = computed(() => {
  return props.eleves.find(e => e.id == form.etudiant_id);
});

const selectedMatiere = computed(() => {
  return props.matieres.find(m => m.id == form.matiere_id);
});

const isFormValid = computed(() => {
  return form.etudiant_id && 
         form.matiere_id && 
         form.type && 
         form.date_absence &&
         (!form.justifiee || (form.justifiee && form.justification));
});

const onTypeChange = () => {
  // Réinitialiser la durée du retard si le type change
  if (form.type !== 'retard') {
    form.duree_retard = '';
  }
};

const submitForm = () => {
  if (!isFormValid.value) return;

  const formData = {
    etudiant_id: form.etudiant_id,
    matiere_id: form.matiere_id,
    type: form.type,
    duree_retard: form.duree_retard || null,
    date_absence: form.date_absence,
    motif: form.motif,
    justifiee: form.justifiee,
    justification: form.justification || null,
  };

  router.post('/absences', formData);
};

const goBack = () => {
  router.visit('/absences');
};

const getStatusClass = (justifiee) => {
  return justifiee 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800';
};

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'Non définie';
  return new Date(dateTimeString).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
