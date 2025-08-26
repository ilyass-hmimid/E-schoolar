<template>
  <AdminLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Gestion des Élèves
        </h2>
        <Link
          :href="route('admin.eleves.create')"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Ajouter un élève
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Tableau des élèves -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau/Filière</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date d'inscription</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="eleve in eleves" :key="eleve.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <span class="text-blue-600 font-medium">{{ getInitials(eleve.name) }}</span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ eleve.name }}</div>
                          <div class="text-sm text-gray-500">{{ eleve.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ eleve.phone || 'Non renseigné' }}</div>
                      <div class="text-sm text-gray-500">{{ eleve.address || 'Adresse non renseignée' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ eleve.niveau || 'Niveau non défini' }}</div>
                      <div class="text-sm text-gray-500">{{ eleve.filiere || 'Filière non définie' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      {{ eleve.created_at }}
                    </td>
                    <td class="px-6 py-4">
                      <span 
                        :class="{
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                          'bg-green-100 text-green-800': eleve.is_active,
                          'bg-red-100 text-red-800': !eleve.is_active
                        }"
                      >
                        {{ eleve.is_active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                      <Link 
                        :href="route('admin.eleves.edit', eleve.id)" 
                        class="text-blue-600 hover:text-blue-900 mr-4"
                      >
                        Modifier
                      </Link>
                      <button 
                        @click="confirmDelete(eleve)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Supprimer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  eleves: {
    type: Array,
    required: true
  },
  niveaux: {
    type: Array,
    required: true
  },
  filieres: {
    type: Array,
    required: true
  }
});

const getInitials = (name) => {
  return name.split(' ').map(part => part[0]).join('').toUpperCase().substring(0, 2);
};

const confirmDelete = (eleve) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer l'élève ${eleve.name} ?`)) {
    // Implémenter la suppression ici
    console.log('Suppression de', eleve.name);
  }
};
</script>
