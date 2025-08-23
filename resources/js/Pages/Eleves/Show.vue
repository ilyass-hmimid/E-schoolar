<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête avec boutons d'action -->
      <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold text-gray-900">
            {{ eleve.name }}
          </h2>
          <div class="mt-2 flex items-center text-sm text-gray-500">
            <span 
              :class="{
                'bg-green-100 text-green-800': eleve.is_active,
                'bg-red-100 text-red-800': !eleve.is_active
              }" 
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
            >
              {{ eleve.is_active ? 'Actif' : 'Inactif' }}
            </span>
          </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
          <Link 
            :href="route('eleves.edit', eleve.id)" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Modifier
          </Link>
          <Link 
            :href="route('eleves.index')" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Retour
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne de gauche - Informations personnelles -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informations personnelles
              </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
              <div class="space-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Email</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.email }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.phone || 'Non renseigné' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.address || 'Non renseignée' }}</dd>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Colonne de droite - Détails de scolarité -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informations de scolarité
              </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Niveau</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.niveau || 'Non défini' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Filière</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ eleve.filiere || 'Non définie' }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Date d'inscription</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ eleve.date_inscription ? formatDate(eleve.date_inscription) : 'Non définie' }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Montant à payer</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ eleve.somme_a_payer ? `${formatCurrency(eleve.somme_a_payer)}` : 'Non défini' }}
                  </dd>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  eleve: {
    type: Object,
    required: true
  }
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-MA', { 
    style: 'currency', 
    currency: 'MAD',
    minimumFractionDigits: 2 
  }).format(amount);
};
</script>
