<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mes Notes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Résumé des notes -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Moyenne Générale</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ moyenneGenerale }}/20</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Matières</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ matieres.length }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Dernière Note</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ derniereNote }}/20</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Évolution</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ evolution }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes par matière -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notes par Matière</h3>
          </div>
          <div class="overflow-hidden">
            <div v-for="matiere in matieres" :key="matiere.id" class="border-b border-gray-200 last:border-b-0">
              <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                  <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ matiere.nom }}</h4>
                    <p class="text-sm text-gray-500">Professeur: {{ matiere.professeur }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ matiere.moyenne }}/20</p>
                    <p class="text-sm text-gray-500">{{ matiere.notes.length }} note(s)</p>
                  </div>
                </div>

                <!-- Graphique des notes -->
                <div class="mb-4">
                  <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-blue-600 h-2 rounded-full" 
                        :style="{ width: (matiere.moyenne / 20) * 100 + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-500">{{ Math.round((matiere.moyenne / 20) * 100) }}%</span>
                  </div>
                </div>

                <!-- Détail des notes -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div v-for="note in matiere.notes.slice(0, 4)" :key="note.id" 
                       class="bg-gray-50 p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-600">{{ note.type }}</span>
                      <span class="text-lg font-semibold" :class="getNoteClass(note.valeur)">
                        {{ note.valeur }}/20
                      </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDate(note.date) }}</p>
                  </div>
                </div>

                <div v-if="matiere.notes.length > 4" class="mt-4 text-center">
                  <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Voir toutes les notes ({{ matiere.notes.length }})
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Graphique d'évolution -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Évolution des Notes</h3>
          </div>
          <div class="p-6">
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
              <p class="text-gray-500">Graphique d'évolution des notes</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Données simulées
const matieres = ref([
  {
    id: 1,
    nom: 'Mathématiques',
    professeur: 'M. Dupont',
    moyenne: 16.5,
    notes: [
      { id: 1, type: 'Contrôle', valeur: 18, date: '2024-01-15' },
      { id: 2, type: 'Devoir', valeur: 15, date: '2024-01-10' },
      { id: 3, type: 'Interro', valeur: 17, date: '2024-01-05' },
      { id: 4, type: 'Contrôle', valeur: 16, date: '2023-12-20' }
    ]
  },
  {
    id: 2,
    nom: 'Physique',
    professeur: 'Mme Martin',
    moyenne: 14.2,
    notes: [
      { id: 5, type: 'Contrôle', valeur: 14, date: '2024-01-12' },
      { id: 6, type: 'TP', valeur: 16, date: '2024-01-08' },
      { id: 7, type: 'Devoir', valeur: 13, date: '2024-01-03' },
      { id: 8, type: 'Contrôle', valeur: 14, date: '2023-12-18' }
    ]
  },
  {
    id: 3,
    nom: 'Français',
    professeur: 'Mme Bernard',
    moyenne: 15.8,
    notes: [
      { id: 9, type: 'Dissertation', valeur: 16, date: '2024-01-14' },
      { id: 10, type: 'Commentaire', valeur: 15, date: '2024-01-09' },
      { id: 11, type: 'Oral', valeur: 17, date: '2024-01-04' },
      { id: 12, type: 'Contrôle', valeur: 15, date: '2023-12-22' }
    ]
  }
]);

const moyenneGenerale = computed(() => {
  const total = matieres.value.reduce((sum, matiere) => sum + matiere.moyenne, 0);
  return (total / matieres.value.length).toFixed(1);
});

const derniereNote = computed(() => {
  let maxDate = new Date(0);
  let derniereNote = 0;
  
  matieres.value.forEach(matiere => {
    matiere.notes.forEach(note => {
      const noteDate = new Date(note.date);
      if (noteDate > maxDate) {
        maxDate = noteDate;
        derniereNote = note.valeur;
      }
    });
  });
  
  return derniereNote;
});

const evolution = computed(() => {
  const moyenne = parseFloat(moyenneGenerale.value);
  if (moyenne >= 16) return '+2.3';
  if (moyenne >= 14) return '+1.1';
  if (moyenne >= 12) return '-0.5';
  return '-1.2';
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};

const getNoteClass = (note) => {
  if (note >= 16) return 'text-green-600';
  if (note >= 14) return 'text-blue-600';
  if (note >= 12) return 'text-yellow-600';
  return 'text-red-600';
};
</script>
