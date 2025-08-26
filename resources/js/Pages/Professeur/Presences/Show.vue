<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails de la présence</h1>
            <p class="mt-2 text-gray-600">Consultez les informations détaillées de cette présence</p>
          </div>
          <div class="flex space-x-3">
            <Link
              :href="route('professeur.presences.edit', presence.id)"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              Modifier
            </Link>
            <Link
              :href="route('professeur.presences.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              Retour à la liste
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">
              Fiche de présence
            </h2>
            <span 
              class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="statusBadgeClass"
            >
              {{ statusLabel }}
            </span>
          </div>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Enregistrée le {{ formatDate(presence.created_at) }} à {{ formatTime(presence.created_at) }}
          </p>
        </div>

        <div class="border-t border-gray-200">
          <dl>
            <!-- Informations sur l'étudiant -->
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Étudiant
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                    <span class="text-gray-600 font-medium">
                      {{ getInitials(presence.etudiant.nom, presence.etudiant.prenom) }}
                    </span>
                  </div>
                  <div>
                    <div class="font-medium">
                      {{ presence.etudiant.nom }} {{ presence.etudiant.prenom }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ presence.etudiant.numero_etudiant }} • {{ presence.classe.nom }}
                    </div>
                  </div>
                </div>
              </dd>
            </div>

            <!-- Informations sur la matière -->
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Matière
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="font-medium">{{ presence.matiere.nom }}</div>
                <div class="text-sm text-gray-500">
                  Enseigné par {{ presence.professeur.nom }} {{ presence.professeur.prenom }}
                </div>
              </dd>
            </div>

            <!-- Détails de la séance -->
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Séance
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </div>
                    <div class="mt-1">
                      {{ formatDate(presence.date_seance) }}
                    </div>
                  </div>
                  <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Heure de début
                    </div>
                    <div class="mt-1">
                      {{ presence.heure_debut }}
                    </div>
                  </div>
                  <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Heure de fin
                    </div>
                    <div class="mt-1">
                      {{ presence.heure_fin }}
                    </div>
                  </div>
                </div>
              </dd>
            </div>

            <!-- Détails du statut -->
            <div v-if="presence.statut !== 'present'" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Détails
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div v-if="presence.statut === 'retard'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Durée du retard
                    </div>
                    <div class="mt-1">
                      {{ presence.duree_retard }} minutes
                    </div>
                  </div>
                  <div v-if="presence.justificatif">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Justificatif
                    </div>
                    <div class="mt-1">
                      {{ presence.justificatif }}
                    </div>
                  </div>
                </div>
                <div v-else-if="presence.statut === 'justifie' && presence.justificatif">
                  <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Justificatif
                  </div>
                  <div class="mt-1">
                    {{ presence.justificatif }}
                  </div>
                </div>
                <div v-else-if="presence.statut === 'absent'">
                  <div class="text-sm text-gray-500 italic">
                    Aucun justificatif fourni
                  </div>
                </div>
              </dd>
            </div>

            <!-- Commentaire -->
            <div v-if="presence.commentaire" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Commentaire
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="prose max-w-none">
                  {{ presence.commentaire }}
                </div>
              </dd>
            </div>

            <!-- Métadonnées -->
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Métadonnées
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Créée le
                    </div>
                    <div class="mt-1">
                      {{ formatDate(presence.created_at) }} à {{ formatTime(presence.created_at) }}
                    </div>
                  </div>
                  <div v-if="presence.updated_at !== presence.created_at">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Mise à jour
                    </div>
                    <div class="mt-1">
                      {{ formatDate(presence.updated_at) }} à {{ formatTime(presence.updated_at) }}
                    </div>
                  </div>
                  <div v-if="presence.deleted_at">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Supprimée le
                    </div>
                    <div class="mt-1 text-red-600">
                      {{ formatDate(presence.deleted_at) }} à {{ formatTime(presence.deleted_at) }}
                    </div>
                  </div>
                </div>
              </dd>
            </div>

            <!-- Historique des modifications -->
            <div v-if="presence.historique && presence.historique.length > 0" class="bg-white border-t border-gray-200 px-4 py-5 sm:px-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des modifications</h3>
              <div class="flow-root">
                <ul class="-mb-8">
                  <li v-for="(modif, index) in presence.historique" :key="index">
                    <div class="relative pb-8">
                      <span v-if="index !== presence.historique.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                      <div class="relative flex space-x-3">
                        <div>
                          <span 
                            class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                            :class="modif.type === 'creation' ? 'bg-green-500' : modif.type === 'modification' ? 'bg-blue-500' : 'bg-yellow-500'"
                          >
                            <svg v-if="modif.type === 'creation'" class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else-if="modif.type === 'modification'" class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            <svg v-else class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                          <div>
                            <p class="text-sm text-gray-500">
                              <span class="font-medium text-gray-900">{{ modif.utilisateur }}</span>
                              {{ getActionText(modif) }}
                            </p>
                            <div v-if="modif.modifications && Object.keys(modif.modifications).length > 0" class="mt-1 text-xs bg-gray-50 p-2 rounded">
                              <div v-for="(value, field) in modif.modifications" :key="field" class="mb-1 last:mb-0">
                                <span class="font-medium">{{ getFieldLabel(field) }}:</span>
                                <span v-if="field === 'statut'" class="ml-1">
                                  <span class="text-red-500 line-through">{{ getStatusLabel(value.old) }}</span>
                                  <span class="mx-1">→</span>
                                  <span class="text-green-600">{{ getStatusLabel(value.new) }}</span>
                                </span>
                                <span v-else class="ml-1">
                                  <span class="text-red-500 line-through">{{ value.old || '-' }}</span>
                                  <span class="mx-1">→</span>
                                  <span class="text-green-600">{{ value.new || '-' }}</span>
                                </span>
                              </div>
                            </div>
                            <p v-if="modif.commentaire" class="mt-1 text-xs text-gray-500 italic">
                              {{ modif.commentaire }}
                            </p>
                          </div>
                          <div class="text-right text-sm whitespace-nowrap text-gray-500">
                            <time :datetime="modif.date">
                              {{ formatRelativeTime(modif.date) }}
                            </time>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </dl>
        </div>

        <!-- Actions -->
        <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200 flex justify-between">
          <div>
            <Link
              :href="route('professeur.presences.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              Retour à la liste
            </Link>
          </div>
          <div class="space-x-3">
            <Link
              :href="route('professeur.presences.edit', presence.id)"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              Modifier
            </Link>
            <button
              type="button"
              @click="confirmDelete"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              :disabled="deleting"
            >
              <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              {{ deleting ? 'Suppression...' : 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="closeModal">
      <template #title>
        Supprimer la présence
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer cette entrée de présence ? Cette action est irréversible.
      </template>
      <template #footer>
        <button
          type="button"
          class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          @click="closeModal"
          :disabled="deleting"
        >
          Annuler
        </button>
        <button
          type="button"
          class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
          @click="deletePresence"
          :disabled="deleting"
        >
          <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ deleting ? 'Suppression...' : 'Supprimer définitivement' }}
        </button>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  presence: {
    type: Object,
    required: true
  },
  statuts: {
    type: Object,
    default: () => ({
      present: 'Présent',
      absent: 'Absent',
      retard: 'Retard',
      justifie: 'Absence justifiée'
    })
  }
});

// État local
const showDeleteModal = ref(false);
const deleting = ref(false);

// Libellé du statut
const statusLabel = computed(() => {
  return props.statuts[props.presence.statut] || props.presence.statut;
});

// Classe CSS pour le badge de statut
const statusBadgeClass = computed(() => {
  const classes = {
    present: 'bg-green-100 text-green-800',
    absent: 'bg-red-100 text-red-800',
    retard: 'bg-yellow-100 text-yellow-800',
    justifie: 'bg-blue-100 text-blue-800'
  };
  return classes[props.presence.statut] || 'bg-gray-100 text-gray-800';
});

// Formater une date au format JJ/MM/AAAA
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Formater une heure au format HH:MM
const formatTime = (dateTimeString) => {
  if (!dateTimeString) return '';
  return new Date(dateTimeString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
};

// Formater la date en temps relatif (il y a...)
const formatRelativeTime = (dateTimeString) => {
  if (!dateTimeString) return '';
  
  const date = new Date(dateTimeString);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);
  
  const intervals = {
    année: 31536000,
    mois: 2592000,
    semaine: 604800,
    jour: 86400,
    heure: 3600,
    minute: 60,
    seconde: 1
  };
  
  for (const [unit, seconds] of Object.entries(intervals)) {
    const interval = Math.floor(diffInSeconds / seconds);
    
    if (interval >= 1) {
      return interval === 1 
        ? `il y a ${interval} ${unit}`
        : `il y a ${interval} ${unit}s`;
    }
  }
  
  return 'à l\'instant';
};

// Obtenir le libellé d'un statut
const getStatusLabel = (status) => {
  return props.statuts[status] || status;
};

// Obtenir le libellé d'un champ pour l'affichage dans l'historique
const getFieldLabel = (field) => {
  const labels = {
    'date_seance': 'Date',
    'heure_debut': 'Heure de début',
    'heure_fin': 'Heure de fin',
    'statut': 'Statut',
    'duree_retard': 'Durée du retard',
    'justificatif': 'Justificatif',
    'commentaire': 'Commentaire'
  };
  return labels[field] || field;
};

// Obtenir le texte d'action pour l'historique
const getActionText = (modif) => {
  if (modif.type === 'creation') return 'a créé cette entrée de présence';
  if (modif.type === 'modification') return 'a modifié cette présence';
  if (modif.type === 'suppression') return 'a supprimé cette présence';
  return 'a effectué une action';
};

// Obtenir les initiales d'un nom et prénom
const getInitials = (nom, prenom) => {
  return `${prenom ? prenom.charAt(0) : ''}${nom ? nom.charAt(0) : ''}`.toUpperCase();
};

// Confirmer la suppression
const confirmDelete = () => {
  showDeleteModal.value = true;
};

// Fermer la modale
const closeModal = () => {
  if (!deleting.value) {
    showDeleteModal.value = false;
  }
};

// Supprimer la présence
const deletePresence = () => {
  deleting.value = true;
  
  router.delete(route('professeur.presences.destroy', props.presence.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      router.visit(route('professeur.presences.index'), {
        onFinish: () => {
          // Afficher un message de succès
          // (à implémenter avec un système de notifications si nécessaire)
        }
      });
    },
    onError: () => {
      alert('Une erreur est survenue lors de la suppression de la présence');
    },
    onFinish: () => {
      deleting.value = false;
    }
  });
};
</script>
