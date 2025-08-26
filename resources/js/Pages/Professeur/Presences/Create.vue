<template>
  <div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Ajouter une présence</h1>
            <p class="mt-2 text-gray-600">Enregistrez la présence d'un étudiant</p>
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
            <div class="grid grid-cols-1 gap-6">
              <!-- Sélection de la matière -->
              <div>
                <label for="matiere_id" class="block text-sm font-medium text-gray-700">Matière *</label>
                <select
                  id="matiere_id"
                  v-model="form.matiere_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.matiere_id }"
                  @change="loadClasses"
                >
                  <option value="">Sélectionnez une matière</option>
                  <option v-for="matiere in matieres" :key="matiere.id" :value="matiere.id">
                    {{ matiere.nom }}
                  </option>
                </select>
                <p v-if="form.errors.matiere_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.matiere_id }}
                </p>
              </div>

              <!-- Sélection de la classe -->
              <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe *</label>
                <select
                  id="classe_id"
                  v-model="form.classe_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.classe_id }"
                  :disabled="!form.matiere_id"
                  @change="loadEtudiants"
                >
                  <option value="">Sélectionnez d'abord une matière</option>
                  <option v-for="classe in classes" :key="classe.id" :value="classe.id">
                    {{ classe.nom }}
                  </option>
                </select>
                <p v-if="form.errors.classe_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.classe_id }}
                </p>
              </div>

              <!-- Sélection de l'étudiant -->
              <div>
                <label for="etudiant_id" class="block text-sm font-medium text-gray-700">Étudiant *</label>
                <select
                  id="etudiant_id"
                  v-model="form.etudiant_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.etudiant_id }"
                  :disabled="!form.classe_id"
                >
                  <option value="">Sélectionnez un étudiant</option>
                  <option v-for="etudiant in etudiants" :key="etudiant.id" :value="etudiant.id">
                    {{ etudiant.nom }} {{ etudiant.prenom }} ({{ etudiant.numero_etudiant }})
                  </option>
                </select>
                <p v-if="form.errors.etudiant_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.etudiant_id }}
                </p>
              </div>

              <!-- Date de la séance -->
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

              <!-- Heure de début et de fin -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début *</label>
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
                  <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin *</label>
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
                  <option value="">Sélectionnez un statut</option>
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
              {{ form.processing ? 'Enregistrement...' : 'Enregistrer la présence' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  matieres: {
    type: Array,
    default: () => []
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
const classes = ref([]);
const etudiants = ref([]);
const loading = ref(false);

// Initialisation du formulaire
const form = useForm({
  matiere_id: '',
  classe_id: '',
  etudiant_id: '',
  date_seance: new Date().toISOString().split('T')[0], // Date du jour par défaut
  heure_debut: '08:00',
  heure_fin: '09:00',
  statut: 'present',
  duree_retard: 5,
  justificatif: '',
  commentaire: ''
});

// Charger les classes associées à la matière sélectionnée
const loadClasses = async () => {
  if (!form.matiere_id) {
    classes.value = [];
    form.classe_id = '';
    form.etudiant_id = '';
    etudiants.value = [];
    return;
  }
  
  try {
    loading.value = true;
    const response = await axios.get(route('professeur.api.classes.by-matiere', { matiere: form.matiere_id }));
    classes.value = response.data;
    
    // Si une seule classe est disponible, la sélectionner automatiquement
    if (classes.value.length === 1) {
      form.classe_id = classes.value[0].id;
      await loadEtudiants();
    } else {
      form.classe_id = '';
      form.etudiant_id = '';
      etudiants.value = [];
    }
  } catch (error) {
    console.error('Erreur lors du chargement des classes:', error);
    classes.value = [];
    form.classe_id = '';
    form.etudiant_id = '';
    etudiants.value = [];
  } finally {
    loading.value = false;
  }
};

// Charger les étudiants de la classe sélectionnée
const loadEtudiants = async () => {
  if (!form.matiere_id || !form.classe_id) {
    form.etudiant_id = '';
    etudiants.value = [];
    return;
  }
  
  try {
    loading.value = true;
    const response = await axios.get(route('professeur.api.etudiants.by-classe-matiere'), {
      params: {
        classe_id: form.classe_id,
        matiere_id: form.matiere_id
      }
    });
    etudiants.value = response.data;
    
    // Si un seul étudiant est disponible, le sélectionner automatiquement
    if (etudiants.value.length === 1) {
      form.etudiant_id = etudiants.value[0].id;
    } else {
      form.etudiant_id = '';
    }
  } catch (error) {
    console.error('Erreur lors du chargement des étudiants:', error);
    form.etudiant_id = '';
    etudiants.value = [];
  } finally {
    loading.value = false;
  }
};

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
  form.post(route('professeur.presences.store'), {
    onSuccess: () => {
      // Réinitialiser le formulaire après une soumission réussie
      form.reset();
      form.date_seance = new Date().toISOString().split('T')[0];
      form.heure_debut = '08:00';
      form.heure_fin = '09:00';
      form.statut = 'present';
      form.duree_retard = 5;
    }
  });
};

// Charger les classes si une matière est présélectionnée (par exemple, via un paramètre d'URL)
onMounted(() => {
  if (form.matiere_id) {
    loadClasses();
  }
});
</script>
