<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier une présence</h1>
            <p class="mt-2 text-gray-600">Mettez à jour les informations de présence</p>
          </div>
          <Link
            :href="route('professeur.presences.index')"
            class="text-gray-700 hover:text-gray-900 flex items-center"
          >
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la liste
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form @submit.prevent="submit">
          <div class="px-4 py-5 sm:p-6">
            <!-- Messages d'erreur généraux -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">
                    Veuillez corriger les erreurs suivantes avant de continuer :
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                      <li v-for="(error, key) in form.errors" :key="key">
                        {{ error }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
              <!-- Informations sur l'étudiant (lecture seule) -->
              <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Étudiant</h3>
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-600 font-medium">
                      {{ getInitials(presence.etudiant.nom, presence.etudiant.prenom) }}
                    </span>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ presence.etudiant.nom }} {{ presence.etudiant.prenom }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ presence.etudiant.numero_etudiant }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ presence.classe.nom }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Informations sur la matière (lecture seule) -->
              <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Matière</h3>
                <div class="text-sm text-gray-900">
                  {{ presence.matiere.nom }}
                </div>
                <div class="mt-1 text-sm text-gray-500">
                  Enseigné par {{ presence.professeur.nom }} {{ presence.professeur.prenom }}
                </div>
              </div>

              <!-- Date et heure de la séance -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="date_seance" class="block text-sm font-medium text-gray-700">Date de la séance *</label>
                  <input
                    type="date"
                    id="date_seance"
                    v-model="form.date_seance"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': form.errors.date_seance }"
                  />
                  <p v-if="form.errors.date_seance" class="mt-1 text-sm text-red-600">
                    {{ form.errors.date_seance }}
                  </p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="heure_debut" class="block text-sm font-medium text-gray-700">Début *</label>
                    <input
                      type="time"
                      id="heure_debut"
                      v-model="form.heure_debut"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.heure_debut }"
                    />
                    <p v-if="form.errors.heure_debut" class="mt-1 text-sm text-red-600">
                      {{ form.errors.heure_debut }}
                    </p>
                  </div>
                  <div>
                    <label for="heure_fin" class="block text-sm font-medium text-gray-700">Fin *</label>
                    <input
                      type="time"
                      id="heure_fin"
                      v-model="form.heure_fin"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.heure_fin }"
                    />
                    <p v-if="form.errors.heure_fin" class="mt-1 text-sm text-red-600">
                      {{ form.errors.heure_fin }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Statut de présence -->
              <div>
                <label for="statut" class="block text-sm font-medium text-gray-700">Statut *</label>
                <select
                  id="statut"
                  v-model="form.statut"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.statut }"
                  @change="handleStatutChange"
                >
                  <option v-for="(label, value) in statuts" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <p v-if="form.errors.statut" class="mt-1 text-sm text-red-600">
                  {{ form.errors.statut }}
                </p>
              </div>

              <!-- Durée du retard (conditionnel) -->
              <div v-if="form.statut === 'retard'">
                <label for="duree_retard" class="block text-sm font-medium text-gray-700">Durée du retard (en minutes) *</label>
                <input
                  type="number"
                  id="duree_retard"
                  v-model="form.duree_retard"
                  min="1"
                  max="240"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.duree_retard }"
                />
                <p v-if="form.errors.duree_retard" class="mt-1 text-sm text-red-600">
                  {{ form.errors.duree_retard }}
                </p>
              </div>

              <!-- Justificatif (conditionnel) -->
              <div v-if="form.statut === 'justifie'">
                <label for="justificatif" class="block text-sm font-medium text-gray-700">Justificatif</label>
                <input
                  type="text"
                  id="justificatif"
                  v-model="form.justificatif"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.justificatif }"
                  placeholder="Type de justificatif (ex: Certificat médical, Motif familial...)"
                />
                <p v-if="form.errors.justificatif" class="mt-1 text-sm text-red-600">
                  {{ form.errors.justificatif }}
                </p>
              </div>

              <!-- Commentaire -->
              <div>
                <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
                <textarea
                  id="commentaire"
                  v-model="form.commentaire"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.commentaire }"
                  placeholder="Informations complémentaires (optionnel)"
                ></textarea>
                <p v-if="form.errors.commentaire" class="mt-1 text-sm text-red-600">
                  {{ form.errors.commentaire }}
                </p>
              </div>

              <!-- Historique des modifications -->
              <div v-if="presence.historique && presence.historique.length > 0" class="mt-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Historique des modifications</h3>
                <div class="bg-gray-50 rounded-md p-3 text-sm">
                  <div v-for="(modif, index) in presence.historique" :key="index" class="mb-2 pb-2 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0">
                    <div class="flex justify-between">
                      <span class="font-medium">
                        {{ formatDate(modif.date) }} à {{ formatTime(modif.date) }}
                      </span>
                      <span class="text-gray-500 text-xs">
                        {{ modif.utilisateur }}
                      </span>
                    </div>
                    <div v-if="modif.modifications" class="mt-1 text-xs text-gray-600">
                      <div v-for="(value, field) in modif.modifications" :key="field" class="flex">
                        <span class="font-medium w-1/3">{{ getFieldLabel(field) }}:</span>
                        <span class="flex-1">
                          <span v-if="field === 'statut'">
                            {{ getStatusLabel(value.old) }} → {{ getStatusLabel(value.new) }}
                          </span>
                          <span v-else>
                            {{ value.old || '-' }} → {{ value.new || '-' }}
                          </span>
                        </span>
                      </div>
                    </div>
                    <div v-if="modif.commentaire" class="mt-1 text-xs text-gray-500 italic">
                      {{ modif.commentaire }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pied de page du formulaire -->
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <Link
              :href="route('professeur.presences.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3"
            >
              Annuler
            </Link>
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Mise à jour...' : 'Mettre à jour la présence' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

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

// Initialisation du formulaire avec les données de la présence
const form = useForm({
  date_seance: props.presence.date_seance.split('T')[0],
  heure_debut: props.presence.heure_debut,
  heure_fin: props.presence.heure_fin,
  statut: props.presence.statut,
  duree_retard: props.presence.duree_retard || 5,
  justificatif: props.presence.justificatif || '',
  commentaire: props.presence.commentaire || ''
});

// Gérer le changement de statut
const handleStatutChange = () => {
  // Réinitialiser les champs conditionnels lorsque le statut change
  if (form.statut !== 'retard') {
    form.duree_retard = 5; // Valeur par défaut
  }
  
  if (form.statut !== 'justifie') {
    form.justificatif = '';
  }
};

// Soumettre le formulaire
const submit = () => {
  form.put(route('professeur.presences.update', props.presence.id));
};

// Obtenir les initiales d'un nom et prénom
const getInitials = (nom, prenom) => {
  return `${prenom ? prenom.charAt(0) : ''}${nom ? nom.charAt(0) : ''}`.toUpperCase();
};

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
</script>
