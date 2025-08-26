<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Détails du pack : {{ pack.nom }}
          </h2>
          <div class="flex items-center mt-2 space-x-2 text-sm text-gray-500">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="pack.est_actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
              {{ pack.est_actif ? 'Actif' : 'Inactif' }}
            </span>
            <span v-if="pack.est_populaire" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
              Mise en avant
            </span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ formatPackType(pack.type) }}
            </span>
          </div>
        </div>
        <div class="space-x-2">
          <Link
            :href="route('admin.packs.edit', pack.id)"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Modifier
          </Link>
          <Link
            :href="route('admin.packs.index')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Retour à la liste
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Informations principales -->
          <div class="lg:col-span-2">
            <div class="p-6 bg-white rounded-lg shadow">
              <div class="pb-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Détails du pack
                </h3>
              </div>
              <div class="mt-6">
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Nom du pack
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                      {{ pack.nom }}
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Type de pack
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                      {{ formatPackType(pack.type) }}
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Prix normal
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                      {{ pack.prix }} DH
                    </dd>
                  </div>
                  <div v-if="pack.prix_promo" class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Prix promotionnel
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-red-600">
                      {{ pack.prix_promo }} DH
                      <span class="ml-2 text-sm font-normal text-gray-500 line-through">
                        {{ pack.prix }} DH
                      </span>
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Durée
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                      {{ pack.duree_jours }} jours
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Statut
                    </dt>
                    <dd class="mt-1">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="pack.est_actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ pack.est_actif ? 'Actif' : 'Inactif' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Description -->
              <div class="pt-5 mt-5 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">
                  Description
                </h3>
                <div class="mt-2 prose-sm text-gray-700" v-html="pack.description || 'Aucune description fournie'">
                </div>
              </div>
            </div>

            <!-- Statistiques du pack -->
            <div class="p-6 mt-6 bg-white rounded-lg shadow">
              <div class="pb-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Statistiques
                </h3>
              </div>
              <div class="mt-6">
                <dl class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-3">
                  <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Nombre de ventes
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                      {{ stats.nombre_ventes || 0 }}
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Revenu total
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600">
                      {{ (stats.revenu_total || 0).toFixed(2) }} DH
                    </dd>
                  </div>
                  <div class="px-4 py-5 overflow-hidden bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Taux d'utilisation
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-600">
                      {{ (stats.taux_utilisation || 0) }}%
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>

          <!-- Actions et historique -->
          <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="p-6 bg-white rounded-lg shadow">
              <div class="pb-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Actions rapides
                </h3>
              </div>
              <div class="mt-6 space-y-4">
                <button
                  @click="toggleStatus"
                  class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  {{ pack.est_actif ? 'Désactiver le pack' : 'Activer le pack' }}
                </button>
                <button
                  @click="togglePopularity"
                  class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-yellow-500 border border-transparent rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                >
                  {{ pack.est_populaire ? 'Retirer de la page d\'accueil' : 'Mettre en avant' }}
                </button>
                <Link
                  :href="route('admin.packs.duplicate', pack.id)"
                  class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <DocumentDuplicateIcon class="w-5 h-5 mr-2 text-gray-500" />
                  Dupliquer ce pack
                </Link>
              </div>
            </div>

            <!-- Dernières ventes -->
            <div class="p-6 bg-white rounded-lg shadow">
              <div class="pb-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Dernières ventes
                </h3>
              </div>
              <div class="flow-root mt-6">
                <ul class="-my-5 divide-y divide-gray-200">
                  <li v-for="vente in dernieresVentes" :key="vente.id" class="py-4">
                    <div class="flex items-center space-x-4">
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ vente.client_nom }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ formatDate(vente.date_vente) }}
                        </p>
                      </div>
                      <div class="inline-flex items-center text-base font-semibold text-gray-900">
                        {{ vente.montant }} DH
                      </div>
                    </div>
                  </li>
                  <li v-if="dernieresVentes.length === 0" class="py-4 text-sm text-center text-gray-500">
                    Aucune vente récente
                  </li>
                </ul>
              </div>
              <div class="mt-6">
                <Link
                  :href="route('admin.ventes.index', { pack_id: pack.id })"
                  class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-indigo-600 bg-white border border-indigo-600 rounded-md hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Voir toutes les ventes
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { DuplicateIcon as DocumentDuplicateIcon } from '@heroicons/vue/outline';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  pack: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    default: () => ({
      nombre_ventes: 0,
      revenu_total: 0,
      taux_utilisation: 0
    })
  },
  dernieresVentes: {
    type: Array,
    default: () => []
  }
});

// Types de packs
const packTypes = {
  'cours': 'Cours',
  'abonnement': 'Abonnement',
  'formation': 'Formation',
  'autre': 'Autre'
};

const form = useForm({
  est_actif: props.pack.est_actif,
  est_populaire: props.pack.est_populaire
});

const formatPackType = (type) => {
  return packTypes[type] || type;
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

const toggleStatus = () => {
  form.est_actif = !form.est_actif;
  form.put(route('admin.packs.toggle-status', props.pack.id), {
    preserveScroll: true
  });
};

const togglePopularity = () => {
  form.est_populaire = !form.est_populaire;
  form.put(route('admin.packs.toggle-popularity', props.pack.id), {
    preserveScroll: true
  });
};
</script>
