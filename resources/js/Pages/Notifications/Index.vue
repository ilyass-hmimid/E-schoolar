<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-2 text-gray-600">Gestion des notifications et communications</p>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="openSystemNotificationModal"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
              </svg>
              Notification système
            </button>
            <button
              @click="openRappelsModal"
              class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Rappels paiement
            </button>
            <button
              @click="testerConfiguration"
              class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Tester config
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total notifications</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.total_notifications }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Lues</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.notifications_lues }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Non lues</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.notifications_non_lues }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Aujourd'hui</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.notifications_aujourd_hui }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Cette semaine</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.notifications_cette_semaine }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Actions</h3>
            <div class="flex items-center space-x-4">
              <button
                @click="markAllAsRead"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
              >
                Marquer tout comme lu
              </button>
              <button
                @click="refreshNotifications"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
              >
                Actualiser
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des notifications -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
          <div
            v-for="notification in notifications.data"
            :key="notification.id"
            :class="[
              'p-6 hover:bg-gray-50 transition-colors',
              notification.read_at ? 'bg-white' : 'bg-blue-50'
            ]"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getNotificationIconClass(notification.data.type)">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="notification.data.type === 'absence'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      <path v-else-if="notification.data.type === 'payment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19C4.74 3.63 5.5 3.3 6.3 3.3h11.4c.8 0 1.56.33 2.11.89L20.7 6.3c.56.55.89 1.31.89 2.11v11.4c0 .8-.33 1.56-.89 2.11L19.7 22.7c-.55.56-1.31.89-2.11.89H6.3c-.8 0-1.56-.33-2.11-.89L3.3 19.7c-.56-.55-.89-1.31-.89-2.11V8.4c0-.8.33-1.56.89-2.11L4.19 4.19z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2">
                    <h4 class="text-sm font-medium text-gray-900">
                      {{ notification.data.title || 'Notification' }}
                    </h4>
                    <span v-if="!notification.read_at" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                      Nouveau
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1">
                    {{ notification.data.message }}
                  </p>
                  <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span>{{ formatDate(notification.created_at) }}</span>
                    <span v-if="notification.data.type">{{ getNotificationTypeLabel(notification.data.type) }}</span>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  v-if="!notification.read_at"
                  @click="markAsRead(notification.id)"
                  class="text-blue-600 hover:text-blue-900 text-sm"
                >
                  Marquer comme lu
                </button>
                <button
                  @click="deleteNotification(notification.id)"
                  class="text-red-600 hover:text-red-900 text-sm"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="notifications.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="notifications.prev_page_url"
                @click="changePage(notifications.current_page - 1)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Précédent
              </button>
              <button
                v-if="notifications.next_page_url"
                @click="changePage(notifications.current_page + 1)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Suivant
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Affichage de <span class="font-medium">{{ notifications.from }}</span> à <span class="font-medium">{{ notifications.to }}</span> sur <span class="font-medium">{{ notifications.total }}</span> résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    v-for="page in getPageNumbers()"
                    :key="page"
                    @click="changePage(page)"
                    :class="[
                      page === notifications.current_page
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                  >
                    {{ page }}
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Notification Système -->
    <div v-if="showSystemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Notification système</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Titre <span class="text-red-500">*</span>
              </label>
              <input
                v-model="systemForm.titre"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Message <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="systemForm.message"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              ></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type <span class="text-red-500">*</span>
              </label>
              <select
                v-model="systemForm.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="info">Information</option>
                <option value="warning">Avertissement</option>
                <option value="error">Erreur</option>
                <option value="success">Succès</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Destinataires <span class="text-red-500">*</span>
              </label>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input v-model="systemForm.destinataires" type="checkbox" value="admin" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-700">Administrateurs</span>
                </label>
                <label class="flex items-center">
                  <input v-model="systemForm.destinataires" type="checkbox" value="professeur" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-700">Professeurs</span>
                </label>
                <label class="flex items-center">
                  <input v-model="systemForm.destinataires" type="checkbox" value="assistant" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-700">Assistants</span>
                </label>
                <label class="flex items-center">
                  <input v-model="systemForm.destinataires" type="checkbox" value="eleve" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-700">Élèves</span>
                </label>
                <label class="flex items-center">
                  <input v-model="systemForm.destinataires" type="checkbox" value="parent" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-700">Parents</span>
                </label>
              </div>
            </div>
            <div>
              <label class="flex items-center">
                <input v-model="systemForm.envoyer_email" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                <span class="ml-2 text-sm text-gray-700">Envoyer par email</span>
              </label>
            </div>
            <div>
              <label class="flex items-center">
                <input v-model="systemForm.envoyer_sms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                <span class="ml-2 text-sm text-gray-700">Envoyer par SMS</span>
              </label>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button
              @click="showSystemModal = false"
              class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="envoyerNotificationSysteme"
              :disabled="!isSystemFormValid"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Envoyer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Rappels Paiement -->
    <div v-if="showRappelsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Rappels de paiement</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Jours de retard <span class="text-red-500">*</span>
              </label>
              <input
                v-model="rappelsForm.jours_retard"
                type="number"
                min="1"
                max="90"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Message personnalisé
              </label>
              <textarea
                v-model="rappelsForm.message_personnalise"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Message optionnel à ajouter..."
              ></textarea>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button
              @click="showRappelsModal = false"
              class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="envoyerRappelsPaiement"
              :disabled="!rappelsForm.jours_retard"
              class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Envoyer les rappels
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  notifications: Object,
  stats: Object,
});

const showSystemModal = ref(false);
const showRappelsModal = ref(false);

const systemForm = reactive({
  titre: '',
  message: '',
  type: 'info',
  destinataires: [],
  envoyer_email: false,
  envoyer_sms: false,
});

const rappelsForm = reactive({
  jours_retard: 7,
  message_personnalise: '',
});

const isSystemFormValid = computed(() => {
  return systemForm.titre && 
         systemForm.message && 
         systemForm.destinataires.length > 0;
});

const markAsRead = async (id) => {
  try {
    await fetch(`/notifications/${id}/mark-read`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });
    router.reload();
  } catch (error) {
    console.error('Erreur:', error);
  }
};

const markAllAsRead = async () => {
  try {
    await fetch('/notifications/mark-all-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });
    router.reload();
  } catch (error) {
    console.error('Erreur:', error);
  }
};

const deleteNotification = async (id) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) return;
  
  try {
    await fetch(`/notifications/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });
    router.reload();
  } catch (error) {
    console.error('Erreur:', error);
  }
};

const changePage = (page) => {
  router.get('/notifications', { page }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getPageNumbers = () => {
  const pages = [];
  const current = props.notifications.current_page;
  const last = props.notifications.last_page;
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i);
  }
  
  return pages;
};

const openSystemNotificationModal = () => {
  showSystemModal.value = true;
};

const openRappelsModal = () => {
  showRappelsModal.value = true;
};

const envoyerNotificationSysteme = async () => {
  if (!isSystemFormValid.value) return;

  try {
    const response = await fetch('/notifications/systeme', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(systemForm),
    });

    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      showSystemModal.value = false;
      router.reload();
    } else {
      alert('Erreur: ' + result.message);
    }
  } catch (error) {
    alert('Erreur lors de l\'envoi: ' + error.message);
  }
};

const envoyerRappelsPaiement = async () => {
  if (!rappelsForm.jours_retard) return;

  try {
    const response = await fetch('/notifications/rappels-paiement', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(rappelsForm),
    });

    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      showRappelsModal.value = false;
    } else {
      alert('Erreur: ' + result.message);
    }
  } catch (error) {
    alert('Erreur lors de l\'envoi: ' + error.message);
  }
};

const testerConfiguration = async () => {
  try {
    const response = await fetch('/notifications/tester-configuration');
    const result = await response.json();
    
    let message = 'Résultats des tests:\n\n';
    Object.entries(result).forEach(([test, result]) => {
      message += `${test.toUpperCase()}: ${result.success ? '✅' : '❌'} ${result.message}\n`;
    });
    
    alert(message);
  } catch (error) {
    alert('Erreur lors du test: ' + error.message);
  }
};

const refreshNotifications = () => {
  router.reload();
};

const getNotificationIconClass = (type) => {
  const classes = {
    'absence': 'bg-red-500',
    'payment': 'bg-green-500',
    'system': 'bg-blue-500',
    'warning': 'bg-yellow-500',
    'error': 'bg-red-500',
    'success': 'bg-green-500',
    'info': 'bg-blue-500',
  };
  return classes[type] || 'bg-gray-500';
};

const getNotificationTypeLabel = (type) => {
  const labels = {
    'absence': 'Absence',
    'payment': 'Paiement',
    'system': 'Système',
    'warning': 'Avertissement',
    'error': 'Erreur',
    'success': 'Succès',
    'info': 'Information',
  };
  return labels[type] || type;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
