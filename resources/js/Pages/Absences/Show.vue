<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails de l'absence</h1>
            <p class="mt-2 text-gray-600">Informations détaillées sur l'absence</p>
          </div>
          <div class="flex items-center space-x-4">
            <button
              v-if="canEdit"
              @click="editAbsence"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Modifier
            </button>
            <button
              @click="goBack"
              class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Retour
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenu -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Informations principales -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">
                Absence de {{ absence.etudiant.name }}
              </h2>
              <p class="text-sm text-gray-600">{{ absence.etudiant.email }}</p>
            </div>
            <div class="text-right">
              <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getTypeClass(absence.type)">
                {{ getTypeLabel(absence.type) }}
              </span>
              <div class="mt-1">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(absence.justifiee)">
                  {{ absence.justifiee ? 'Justifiée' : 'Non justifiée' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Détails -->
        <div class="px-6 py-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations de base -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
              <dl class="space-y-3">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Matière</dt>
                  <dd class="text-sm text-gray-900">{{ absence.matiere.nom }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Date et heure</dt>
                  <dd class="text-sm text-gray-900">{{ formatDateTime(absence.date_absence) }}</dd>
                </div>
                <div v-if="absence.type === 'retard' && absence.duree_retard">
                  <dt class="text-sm font-medium text-gray-500">Durée du retard</dt>
                  <dd class="text-sm text-gray-900">{{ absence.duree_retard }} minutes</dd>
                </div>
                <div v-if="absence.motif">
                  <dt class="text-sm font-medium text-gray-500">Motif</dt>
                  <dd class="text-sm text-gray-900">{{ absence.motif }}</dd>
                </div>
              </dl>
            </div>

            <!-- Informations de justification -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Justification</h3>
              <dl class="space-y-3">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Statut</dt>
                  <dd class="text-sm text-gray-900">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(absence.justifiee)">
                      {{ absence.justifiee ? 'Justifiée' : 'Non justifiée' }}
                    </span>
                  </dd>
                </div>
                <div v-if="absence.justification">
                  <dt class="text-sm font-medium text-gray-500">Justification</dt>
                  <dd class="text-sm text-gray-900">{{ absence.justification }}</dd>
                </div>
                <div v-if="absence.professeur">
                  <dt class="text-sm font-medium text-gray-500">Professeur</dt>
                  <dd class="text-sm text-gray-900">{{ absence.professeur.name }}</dd>
                </div>
                <div v-if="absence.assistant">
                  <dt class="text-sm font-medium text-gray-500">Assistant</dt>
                  <dd class="text-sm text-gray-900">{{ absence.assistant.name }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">
                Créée le {{ formatDate(absence.created_at) }}
                <span v-if="absence.updated_at !== absence.created_at">
                  • Modifiée le {{ formatDate(absence.updated_at) }}
                </span>
              </div>
              <div class="flex items-center space-x-4">
                <button
                  v-if="canJustify && !absence.justifiee"
                  @click="justifyAbsence"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
                >
                  Marquer comme justifiée
                </button>
                <button
                  v-if="canJustify && absence.justifiee"
                  @click="unjustifyAbsence"
                  class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors"
                >
                  Marquer comme non justifiée
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de justification -->
    <div v-if="showJustifyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Justifier l'absence</h3>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Justification <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="justificationText"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Détails de la justification..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showJustifyModal = false"
              class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="confirmJustify"
              :disabled="!justificationText.trim()"
              class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Justifier
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  absence: Object,
  canEdit: Boolean,
  canJustify: Boolean,
});

const showJustifyModal = ref(false);
const justificationText = ref('');

const getTypeLabel = (type) => {
  const labels = {
    'absence': 'Absence',
    'retard': 'Retard',
    'sortie_anticipée': 'Sortie anticipée',
  };
  return labels[type] || type;
};

const getTypeClass = (type) => {
  const classes = {
    'absence': 'bg-red-100 text-red-800',
    'retard': 'bg-orange-100 text-orange-800',
    'sortie_anticipée': 'bg-yellow-100 text-yellow-800',
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

const getStatusClass = (justifiee) => {
  return justifiee 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800';
};

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const editAbsence = () => {
  router.visit(`/absences/${props.absence.id}/edit`);
};

const goBack = () => {
  router.visit('/absences');
};

const justifyAbsence = () => {
  showJustifyModal.value = true;
};

const confirmJustify = () => {
  if (justificationText.value.trim()) {
    router.post(`/absences/${props.absence.id}/justifier`, {
      justification: justificationText.value
    });
    showJustifyModal.value = false;
    justificationText.value = '';
  }
};

const unjustifyAbsence = () => {
  if (confirm('Êtes-vous sûr de vouloir marquer cette absence comme non justifiée ?')) {
    router.post(`/absences/${props.absence.id}/non-justifier`);
  }
};
</script>
