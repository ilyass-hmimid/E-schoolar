<template>
  <div class="space-y-6">
    <!-- En-tête avec bouton de retour -->
    <div class="flex items-center justify-between">
      <div>
        <Link 
          :href="route('eleve.absences.index')" 
          class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
        >
          <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          Retour à la liste des absences
        </Link>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Détails de l'absence</h1>
      </div>
      
      <div v-if="!absence.est_justifiee" class="flex space-x-3">
        <Link 
          :href="route('eleve.absences.justifier', absence.id)" 
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          Justifier cette absence
        </Link>
      </div>
    </div>

    <!-- Carte des détails de l'absence -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Informations sur l'absence
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Détails de l'absence du {{ formatDate(absence.date_absence) }}
        </p>
      </div>
      <div class="border-t border-gray-200">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Matière
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ absence.enseignement.matiere.nom }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Professeur
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ absence.enseignement.professeur.name }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Date et heure
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ formatDate(absence.date_absence) }} de {{ formatHeure(absence.enseignement.date_debut) }} à {{ formatHeure(absence.enseignement.date_fin) }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Statut
            </dt>
            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="{
                  'bg-green-100 text-green-800': absence.est_justifiee,
                  'bg-yellow-100 text-yellow-800': !absence.est_justifiee
                }"
              >
                {{ absence.est_justifiee ? 'Justifiée' : 'Non justifiée' }}
              </span>
            </dd>
          </div>
          <div v-if="absence.est_justifiee" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Justificatif
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2">
                  {{ absence.justificatif_fichier || 'Aucun fichier joint' }}
                </span>
                <a 
                  v-if="absence.justificatif_fichier" 
                  :href="route('eleve.absences.justificatif', absence.id)" 
                  class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                  target="_blank"
                >
                  Télécharger
                </a>
              </div>
            </dd>
          </div>
          <div v-if="absence.est_justifiee" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Commentaire
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
              {{ absence.commentaire || 'Aucun commentaire' }}
            </dd>
          </div>
          <div v-if="!absence.est_justifiee" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Actions
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <p class="text-gray-500 mb-3">
                Cette absence n'est pas encore justifiée. Vous pouvez fournir un justificatif pour la justifier.
              </p>
              <Link 
                :href="route('eleve.absences.justifier', absence.id)" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Justifier cette absence
              </Link>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
  absence: {
    type: Object,
    required: true
  }
});

// Formater une date en français
const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'PPP', { locale: fr });
};

// Formater une heure
const formatHeure = (dateTimeString) => {
  if (!dateTimeString) return '';
  return format(new Date(dateTimeString), 'HH:mm');
};
</script>
