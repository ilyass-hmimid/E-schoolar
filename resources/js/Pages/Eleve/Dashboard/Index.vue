<template>
  <EleveLayout :title="'Tableau de bord'" :breadcrumbs="[{ title: 'Tableau de bord' }]">
    <div class="space-y-6">
      <!-- En-tête avec bienvenue -->
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">
          Bonjour, {{ user.first_name || user.name }}
        </h1>
        <p class="mt-1 text-gray-600">
          Bienvenue sur votre tableau de bord. Voici un aperçu de votre activité récente.
        </p>
      </div>

      <!-- Cartes de statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Carte Moyenne Générale -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Moyenne générale</p>
                <div class="flex items-baseline">
                  <p class="text-2xl font-bold text-gray-900">{{ calculerMoyenne() }}</p>
                  <p class="ml-1 text-sm text-gray-500">/ 20</p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Sur {{ stats.notes.total }} notes</p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('eleve.notes.index')" class="font-medium text-indigo-600 hover:text-indigo-500">
                Voir toutes les notes
              </Link>
            </div>
          </div>
        </div>

        <!-- Carte Absences -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-red-100 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Absences ce mois</p>
                <div class="flex items-baseline">
                  <p class="text-2xl font-bold text-gray-900">{{ absencesCeMois.length }}</p>
                  <p v-if="absencesNonJustifiees > 0" class="ml-2 text-sm font-medium text-red-600">
                    ({{ absencesNonJustifiees }} non justifiées)
                  </p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Sur {{ stats.absences.total }} au total</p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('eleve.absences.index')" class="font-medium text-indigo-600 hover:text-indigo-500">
                Voir toutes les absences
              </Link>
            </div>
          </div>
        </div>

        <!-- Carte Prochain Cours -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Prochain cours</p>
                <p v-if="prochainesSeances.length > 0" class="text-lg font-bold text-gray-900">
                  {{ formatDate(prochainesSeances[0].date_debut) }}
                </p>
                <p v-else class="text-gray-500">Aucun cours prévu</p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('eleve.emploi-du-temps')" class="font-medium text-indigo-600 hover:text-indigo-500">
                Voir l'emploi du temps
              </Link>
            </div>
          </div>
        </div>

        <!-- Carte Paiements -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-5">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Prochain paiement</p>
                <p v-if="prochainPaiement" class="text-xl font-bold text-gray-900">
                  {{ formatMontant(prochainPaiement.montant) }}
                </p>
                <p v-else class="text-gray-500">Aucun paiement à venir</p>
                <p v-if="prochainPaiement" class="mt-1 text-xs text-gray-500">
                  Échéance: {{ formatDateCourte(prochainPaiement.date_echeance) }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('eleve.paiements.index')" class="font-medium text-indigo-600 hover:text-indigo-500">
                Voir les paiements
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Prochaines séances -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Prochaines séances</h2>
        </div>
        <div class="divide-y divide-gray-200">
          <div v-if="prochainesSeances.length > 0">
            <div v-for="seance in prochainesSeances.slice(0, 5)" :key="seance.id" class="p-4 hover:bg-gray-50">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 p-2 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ seance.matiere.nom }}</div>
                  <div class="text-sm text-gray-500">
                    {{ formatDateComplete(seance.date_debut) }} - {{ formatHeure(seance.date_debut) }}
                  </div>
                  <div class="mt-1 text-sm text-gray-500">
                    {{ seance.salle?.nom || 'Salle non définie' }} • {{ seance.professeur?.name || 'Professeur non défini' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="p-4 text-center text-gray-500">
            Aucune séance à venir
          </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 text-right">
          <Link :href="route('eleve.emploi-du-temps')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Voir l'emploi du temps complet
          </Link>
        </div>
      </div>

      <!-- Dernières notes -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Dernières notes</h2>
        </div>
        <div class="divide-y divide-gray-200">
          <div v-if="derniereNotes.length > 0">
            <div v-for="note in derniereNotes.slice(0, 5)" :key="note.id" class="p-4 hover:bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">{{ note.matiere.nom }}</p>
                  <p class="text-sm text-gray-500">{{ note.type_note }} • {{ formatDateCourte(note.date_evaluation) }}</p>
                </div>
                <div :class="getNoteColorClass(note.valeur)" class="px-3 py-1 rounded-full text-sm font-medium">
                  {{ note.valeur }}/20
                </div>
              </div>
            </div>
          </div>
          <div v-else class="p-4 text-center text-gray-500">
            Aucune note enregistrée
          </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 text-right">
          <Link :href="route('eleve.notes.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Voir toutes les notes
          </Link>
        </div>
      </div>
    </div>
  </EleveLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { format, parseISO, isToday, isTomorrow, isThisYear } from 'date-fns';
import { fr } from 'date-fns/locale';
import axios from 'axios';

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({
      notes: { total: 0 },
      absences: { total: 0 }
    })
  },
  prochainesSeances: {
    type: Array,
    default: () => []
  },
  derniereNotes: {
    type: Array,
    default: () => []
  },
  absences: {
    type: Array,
    default: () => []
  },
  prochainPaiement: {
    type: Object,
    default: null
  },
  notifications: {
    type: Array,
    default: () => []
  }
});

// États réactifs
const loading = ref(false);
const notificationsList = ref(props.notifications || []);
const unreadCount = computed(() => notificationsList.value.filter(n => !n.read_at).length);

// Méthodes utilitaires
const calculerMoyenne = () => {
  try {
    if (!props.derniereNotes || props.derniereNotes.length === 0) return '--';
    const total = props.derniereNotes.reduce((sum, note) => sum + parseFloat(note.valeur), 0);
    return (total / props.derniereNotes.length).toFixed(2);
  } catch (e) {
    console.error('Erreur lors du calcul de la moyenne', e);
    return '--';
  }
};

const formatDate = (dateString) => {
  try {
    if (!dateString) return '';
    return format(parseISO(dateString), 'EEEE d MMMM yyyy', { locale: fr });
  } catch (e) {
    console.error('Erreur de formatage de date', e);
    return dateString;
  }
};

const formatHeure = (dateString) => {
  try {
    if (!dateString) return '';
    return format(parseISO(dateString), 'HH:mm');
  } catch (e) {
    console.error('Erreur de formatage d\'heure', e);
    return dateString;
  }
};

const formatDateCourte = (dateString) => {
  try {
    if (!dateString) return '';
    return format(parseISO(dateString), 'dd/MM/yyyy');
  } catch (e) {
    console.error('Erreur de formatage de date courte', e);
    return dateString;
  }
};

const formatMontant = (montant) => {
  try {
    if (typeof montant !== 'number') {
      montant = parseFloat(montant);
    }
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'MAD',
      minimumFractionDigits: 2
    }).format(montant);
  } catch (e) {
    console.error('Erreur de formatage du montant', e);
    return `${montant} MAD`;
  }
};

const formatDateComplete = (dateString) => {
  try {
    if (!dateString) return '';
    const date = parseISO(dateString);
    
    if (isToday(date)) {
      return 'Aujourd\'hui';
    } else if (isTomorrow(date)) {
      return 'Demain';
    } else {
      return format(date, 'EEEE d MMMM', { locale: fr });
    }
  } catch (e) {
    console.error('Erreur de formatage de date complète', e);
    return dateString;
  }
};

const getNoteColorClass = (note) => {
  const value = parseFloat(note);
  if (isNaN(value)) return 'bg-gray-100 text-gray-800';
  
  if (value >= 16) return 'bg-green-100 text-green-800';
  if (value >= 14) return 'bg-blue-100 text-blue-800';
  if (value >= 12) return 'bg-yellow-100 text-yellow-800';
  if (value >= 10) return 'bg-orange-100 text-orange-800';
  return 'bg-red-100 text-red-800';
};

const absencesCeMois = computed(() => {
  try {
    if (!Array.isArray(props.absences)) return [];
    const now = new Date();
    return props.absences.filter(absence => {
      try {
        const dateAbsence = parseISO(absence.date_absence);
        return dateAbsence.getMonth() === now.getMonth() && 
               dateAbsence.getFullYear() === now.getFullYear();
      } catch (e) {
        console.error('Erreur lors du filtrage des absences', e);
        return false;
      }
    });
  } catch (e) {
    console.error('Erreur dans absencesCeMois', e);
    return [];
  }
});

const absencesNonJustifiees = computed(() => {
  try {
    return absencesCeMois.value.filter(a => !a.est_justifiee).length;
  } catch (e) {
    console.error('Erreur dans absencesNonJustifiees', e);
    return 0;
  }
});

// Gestion des notifications
const markAsRead = async (notificationId) => {
  try {
    await axios.post(route('notifications.read', { notification: notificationId }));
    const index = notificationsList.value.findIndex(n => n.id === notificationId);
    if (index !== -1) {
      notificationsList.value[index].read_at = new Date().toISOString();
    }
  } catch (error) {
    console.error('Erreur lors du marquage comme lu', error);
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post(route('notifications.read-all'));
    notificationsList.value = notificationsList.value.map(n => ({
      ...n,
      read_at: n.read_at || new Date().toISOString()
    }));
  } catch (error) {
    console.error('Erreur lors du marquage tout comme lu', error);
  }
};

const loadMoreNotifications = async () => {
  try {
    const response = await axios.get(route('eleve.notifications.load-more'), {
      params: {
        offset: notificationsList.value.length
      }
    });
    
    if (response.data && response.data.length > 0) {
      notificationsList.value = [...notificationsList.value, ...response.data];
    }
  } catch (error) {
    console.error('Erreur lors du chargement des notifications', error);
  }
};

// Gestion des statuts de paiement
const getPaiementStatusClass = (statut) => {
  const classes = {
    paye: 'bg-green-100 text-green-800',
    en_retard: 'bg-red-100 text-red-800',
    en_attente: 'bg-yellow-100 text-yellow-800',
    annule: 'bg-gray-100 text-gray-800',
    default: 'bg-gray-100 text-gray-800'
  };
  return classes[statut] || classes.default;
};

// Obtenir le libellé d'un statut de paiement
const getPaiementStatusLabel = (statut) => {
  const labels = {
    paye: 'Payé',
    en_attente: 'En attente',
    en_retard: 'En retard',
    annule: 'Annulé',
    default: 'Inconnu'
  };
  return labels[statut] || labels.default;
};

// Hooks de cycle de vie
onMounted(() => {
  console.log('Tableau de bord Élève monté');
  // Charger les notifications non lues au chargement
  if (unreadCount.value > 0) {
    markAllAsRead();
  }
});

// Configuration de la page
const page = usePage();

// Titre de la page
const pageTitle = 'Tableau de bord';

// Métadonnées pour le head
useHead({
  title: pageTitle,
  meta: [
    { name: 'description', content: 'Tableau de bord de l\'élève - Suivi des notes, absences et paiements' }
  ]
});
</script>
