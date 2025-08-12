<template>
  <modal :show="show" @close="close">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
          <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
            Préférences de notification
          </h3>
          <div class="mt-4">
            <p class="text-sm text-gray-500 mb-4">
              Configurez comment vous souhaitez recevoir les notifications.
            </p>
            
            <!-- Chargement des préférences -->
            <div v-if="loading" class="text-center py-4">
              <svg class="animate-spin h-5 w-5 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            
            <!-- Formulaire des préférences -->
            <form v-else @submit.prevent="savePreferences" class="space-y-6">
              <!-- Canaux de notification -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Canaux de notification</h4>
                <div class="space-y-3">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="email-channel" 
                        v-model="preferences.email" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="email-channel" class="font-medium text-gray-700">Email</label>
                      <p class="text-gray-500">Recevoir les notifications par email</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="database-channel" 
                        v-model="preferences.database" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="database-channel" class="font-medium text-gray-700">Notification interne</label>
                      <p class="text-gray-500">Afficher les notifications dans l'application</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="push-channel" 
                        v-model="preferences.push" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        :disabled="!browserPushSupported"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="push-channel" class="font-medium text-gray-700">Notifications push</label>
                      <p class="text-gray-500">
                        {{ browserPushSupported 
                          ? 'Recevoir des notifications push sur votre appareil' 
                          : 'Les notifications push ne sont pas supportées par votre navigateur' }}
                      </p>
                      <button 
                        v-if="browserPushSupported && !pushSubscription"
                        type="button"
                        @click="requestPushPermission"
                        class="mt-1 text-xs text-indigo-600 hover:text-indigo-800 focus:outline-none"
                      >
                        Activer les notifications push
                      </button>
                      <button 
                        v-else-if="pushSubscription"
                        type="button"
                        @click="unsubscribeFromPush"
                        class="mt-1 text-xs text-red-600 hover:text-red-800 focus:outline-none"
                      >
                        Désactiver les notifications push
                      </button>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="sms-channel" 
                        v-model="preferences.sms" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        :disabled="!user.phone_number"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="sms-channel" class="font-medium text-gray-700">SMS</label>
                      <p class="text-gray-500">
                        {{ user.phone_number 
                          ? `Recevoir des notifications par SMS sur ${user.phone_number}` 
                          : 'Ajoutez un numéro de téléphone dans votre profil pour activer les SMS' }}
                      </p>
                      <inertia-link 
                        v-if="!user.phone_number"
                        :href="route('profile.show')" 
                        class="mt-1 text-xs text-indigo-600 hover:text-indigo-800 focus:outline-none"
                      >
                        Ajouter un numéro de téléphone
                      </inertia-link>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Types de notification -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Types de notification</h4>
                <div class="space-y-3">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="type-absence" 
                        v-model="preferences.types.absence" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="type-absence" class="font-medium text-gray-700">Absences et retards</label>
                      <p class="text-gray-500">Notifications concernant les absences et retards des élèves</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="type-payment" 
                        v-model="preferences.types.payment" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="type-payment" class="font-medium text-gray-700">Paiements</label>
                      <p class="text-gray-500">Notifications concernant les paiements et échéances</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="type-grades" 
                        v-model="preferences.types.grades" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="type-grades" class="font-medium text-gray-700">Notes et évaluations</label>
                      <p class="text-gray-500">Notifications concernant les notes et évaluations</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="type-system" 
                        v-model="preferences.types.system" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="type-system" class="font-medium text-gray-700">Notifications système</label>
                      <p class="text-gray-500">Mises à jour et informations importantes du système</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        id="type-newsletter" 
                        v-model="preferences.types.newsletter" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="type-newsletter" class="font-medium text-gray-700">Newsletter et offres</label>
                      <p class="text-gray-500">Recevoir des offres spéciales et des mises à jour</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Fréquence des notifications -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Fréquence des notifications</h4>
                <div class="space-y-3">
                  <div class="flex items-center">
                    <input 
                      id="frequency-realtime" 
                      v-model="preferences.frequency" 
                      value="realtime" 
                      type="radio" 
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                    >
                    <label for="frequency-realtime" class="ml-3 block text-sm font-medium text-gray-700">
                      En temps réel
                      <p class="text-gray-500 text-xs font-normal">Recevoir les notifications immédiatement</p>
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input 
                      id="frequency-daily" 
                      v-model="preferences.frequency" 
                      value="daily" 
                      type="radio" 
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                    >
                    <label for="frequency-daily" class="ml-3 block text-sm font-medium text-gray-700">
                      Quotidien
                      <p class="text-gray-500 text-xs font-normal">Recevoir un résumé quotidien</p>
                    </label>
                  </div>
                  
                  <div class="flex items-center">
                    <input 
                      id="frequency-weekly" 
                      v-model="preferences.frequency" 
                      value="weekly" 
                      type="radio" 
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                    >
                    <label for="frequency-weekly" class="ml-3 block text-sm font-medium text-gray-700">
                      Hebdomadaire
                      <p class="text-gray-500 text-xs font-normal">Recevoir un résumé hebdomadaire</p>
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Heures de réception -->
              <div v-if="preferences.frequency !== 'realtime'">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Heure de réception</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Heure d'envoi</label>
                    <select 
                      id="time" 
                      v-model="preferences.notification_time" 
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                      <option v-for="hour in 24" :key="hour - 1" :value="`${String(hour - 1).padStart(2, '0')}:00`">
                        {{ `${String(hour - 1).padStart(2, '0')}:00` }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700">Fuseau horaire</label>
                    <select 
                      id="timezone" 
                      v-model="preferences.timezone" 
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                      <option value="Europe/Paris">Europe/Paris (GMT+1)</option>
                      <option value="UTC">UTC/GMT</option>
                      <!-- Autres fuseaux horaires peuvent être ajoutés ici -->
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Boutons d'action -->
              <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button
                  type="button"
                  @click="close"
                  class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="saving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ saving ? 'Enregistrement...' : 'Enregistrer les préférences' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import Modal from '@/Components/Modal.vue';

export default {
  components: {
    Modal,
  },
  
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  
  emits: ['close'],
  
  setup(props, { emit }) {
    const user = usePage().props.value.auth.user;
    const loading = ref(true);
    const saving = ref(false);
    const browserPushSupported = ref('serviceWorker' in navigator && 'PushManager' in window);
    const pushSubscription = ref(null);
    
    // Préférences par défaut
    const defaultPreferences = {
      email: true,
      database: true,
      push: false,
      sms: false,
      frequency: 'realtime',
      notification_time: '09:00',
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Europe/Paris',
      types: {
        absence: true,
        payment: true,
        grades: true,
        system: true,
        newsletter: false,
      },
    };
    
    const preferences = ref(JSON.parse(JSON.stringify(defaultPreferences)));
    
    // Fermer la modal
    const close = () => {
      emit('close');
    };
    
    // Charger les préférences de l'utilisateur
    const loadPreferences = async () => {
      try {
        loading.value = true;
        const response = await axios.get('/api/notifications/preferences');
        
        if (response.data.success && response.data.data) {
          // Fusionner avec les préférences par défaut pour s'assurer que tous les champs sont définis
          preferences.value = {
            ...defaultPreferences,
            ...response.data.data,
            types: {
              ...defaultPreferences.types,
              ...(response.data.data.types || {})
            }
          };
        }
      } catch (error) {
        console.error('Erreur lors du chargement des préférences:', error);
      } finally {
        loading.value = false;
      }
    };
    
    // Sauvegarder les préférences
    const savePreferences = async () => {
      try {
        saving.value = true;
        
        // Mettre à jour les préférences côté serveur
        await axios.put('/api/notifications/preferences', {
          ...preferences.value,
          // S'assurer que les notifications push sont activées/désactivées en conséquence
          push: preferences.value.push && pushSubscription.value !== null,
        });
        
        // Fermer la modal après un court délai pour montrer le succès
        setTimeout(() => {
          close();
        }, 500);
        
        // Afficher un message de succès (vous pourriez utiliser un système de toast)
        // Par exemple: toast.success('Préférences enregistrées avec succès');
      } catch (error) {
        console.error('Erreur lors de la sauvegarde des préférences:', error);
        // Afficher un message d'erreur
        // toast.error('Une erreur est survenue lors de la sauvegarde des préférences');
      } finally {
        saving.value = false;
      }
    };
    
    // Vérifier l'état d'abonnement aux notifications push
    const checkPushSubscription = async () => {
      if (!browserPushSupported.value) return;
      
      try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        pushSubscription.value = subscription;
        
        // Mettre à jour l'état des préférences en fonction de l'abonnement
        if (subscription) {
          preferences.value.push = true;
        }
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'abonnement push:', error);
      }
    };
    
    // Demander la permission pour les notifications push
    const requestPushPermission = async () => {
      if (!browserPushSupported.value) return;
      
      try {
        // Demander la permission
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
          // Enregistrer le service worker s'il n'est pas déjà enregistré
          const registration = await navigator.serviceWorker.register('/sw.js');
          
          // S'abonner aux notifications push
          const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: process.env.MIX_VAPID_PUBLIC_KEY // Vous devez configurer cette clé
          });
          
          // Envoyer l'abonnement au serveur
          await axios.post('/api/push/subscribe', subscription.toJSON());
          
          // Mettre à jour l'état local
          pushSubscription.value = subscription;
          preferences.value.push = true;
        }
      } catch (error) {
        console.error('Erreur lors de la demande de permission push:', error);
        preferences.value.push = false;
      }
    };
    
    // Se désabonner des notifications push
    const unsubscribeFromPush = async () => {
      if (!pushSubscription.value) return;
      
      try {
        // Se désabonner du service de notification push
        await pushSubscription.value.unsubscribe();
        
        // Informer le serveur de la fin de l'abonnement
        await axios.post('/api/push/unsubscribe');
        
        // Mettre à jour l'état local
        pushSubscription.value = null;
        preferences.value.push = false;
      } catch (error) {
        console.error('Erreur lors du désabonnement des notifications push:', error);
      }
    };
    
    // Charger les préférences au montage du composant
    onMounted(() => {
      loadPreferences();
      
      if (browserPushSupported.value) {
        checkPushSubscription();
      }
    });
    
    // Recharger les préférences lorsque la modal est ouverte
    watch(() => props.show, (newVal) => {
      if (newVal) {
        loadPreferences();
        
        if (browserPushSupported.value) {
          checkPushSubscription();
        }
      }
    });
    
    return {
      user,
      loading,
      saving,
      preferences,
      browserPushSupported,
      pushSubscription,
      close,
      savePreferences,
      requestPushPermission,
      unsubscribeFromPush,
    };
  },
};
</script>
