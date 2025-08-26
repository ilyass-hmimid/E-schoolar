<template>
  <AppLayout :title="`Modifier le paiement #${paiement.id}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <Link :href="route('paiements.index')" class="text-blue-600 hover:text-blue-900">
            Paiements
          </Link>
          <span class="text-gray-500"> / </span>
          Modifier le paiement #{{ paiement.id }}
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations du paiement -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium text-gray-900">Informations du paiement</h3>
                  
                  <div>
                    <InputLabel for="eleve_id" value="Élève *" />
                    <select
                      id="eleve_id"
                      v-model="form.eleve_id"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      required
                      :disabled="!$page.props.auth.user.isAdmin"
                    >
                      <option value="">Sélectionner un élève</option>
                      <option 
                        v-for="eleve in eleves" 
                        :key="eleve.id" 
                        :value="eleve.id"
                      >
                        {{ eleve.name }} ({{ eleve.classe?.nom || 'Non affecté' }})
                      </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.eleve_id" />
                  </div>

                  <div>
                    <InputLabel for="date_paiement" value="Date de paiement *" />>
                    <TextInput
                      id="date_paiement"
                      type="date"
                      class="mt-1 block w-full"
                      v-model="form.date_paiement"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.date_paiement" />
                  </div>

                  <div>
                    <InputLabel for="montant" value="Montant (DH) *" />
                    <TextInput
                      id="montant"
                      type="number"
                      min="0"
                      step="0.01"
                      class="mt-1 block w-full"
                      v-model="form.montant"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.montant" />
                  </div>

                  <div>
                    <InputLabel for="mode_paiement" value="Mode de paiement *" />
                    <select
                      id="mode_paiement"
                      v-model="form.mode_paiement"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      required
                    >
                      <option value="especes">Espèces</option>
                      <option value="cheque">Chèque</option>
                      <option value="virement">Virement bancaire</option>
                      <option value="carte">Carte bancaire</option>
                      <option value="autre">Autre</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.mode_paiement" />
                  </div>
                </div>

                <!-- Période et statut -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium text-gray-900">Période et statut</h3>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <InputLabel for="mois" value="Mois *" />
                      <select
                        id="mois"
                        v-model="form.mois"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                      >
                        <option v-for="(mois, index) in moisList" :key="index" :value="index + 1">
                          {{ mois }}
                        </option>
                      </select>
                      <InputError class="mt-2" :message="form.errors.mois" />
                    </div>
                    
                    <div>
                      <InputLabel for="annee" value="Année *" />
                      <TextInput
                        id="annee"
                        type="number"
                        min="2020"
                        :max="new Date().getFullYear() + 1"
                        class="mt-1 block w-full"
                        v-model="form.annee"
                        required
                      />
                      <InputError class="mt-2" :message="form.errors.annee" />
                    </div>
                  </div>

                  <div>
                    <InputLabel for="statut" value="Statut *" />
                    <select
                      id="statut"
                      v-model="form.statut"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                      required
                    >
                      <option value="paye">Payé</option>
                      <option value="en_attente">En attente</option>
                      <option value="en_retard">En retard</option>
                      <option value="annule">Annulé</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.statut" />
                  </div>

                  <div>
                    <InputLabel for="reference" value="Référence" />
                    <TextInput
                      id="reference"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.reference"
                      placeholder="Numéro de chèque, référence virement, etc."
                    />
                    <InputError class="mt-2" :message="form.errors.reference" />
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div class="mt-6">
                <InputLabel for="notes" value="Notes" />
                <textarea
                  id="notes"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  v-model="form.notes"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.notes" />
              </div>

              <!-- Pièces jointes -->
              <div class="mt-6">
                <InputLabel value="Pièces jointes" />
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg
                      class="mx-auto h-12 w-12 text-gray-400"
                      stroke="currentColor"
                      fill="none"
                      viewBox="0 0 48 48"
                      aria-hidden="true"
                    >
                      <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                      >
                        <span>Téléverser un fichier</span>
                        <input
                          id="file-upload"
                          name="file-upload"
                          type="file"
                          class="sr-only"
                          @change="handleFileUpload"
                          multiple
                        />
                      </label>
                      <p class="pl-1">ou glisser-déposer</p>
                    </div>
                    <p class="text-xs text-gray-500">
                      PNG, JPG, PDF jusqu'à 10MB
                    </p>
                  </div>
                </div>
                
                <!-- Liste des pièces jointes -->
                <div v-if="paiement.documents && paiement.documents.length > 0" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Pièces jointes actuelles</h4>
                  <ul class="space-y-2">
                    <li 
                      v-for="document in paiement.documents" 
                      :key="document.id"
                      class="flex items-center justify-between p-2 bg-gray-50 rounded-md"
                    >
                      <div class="flex items-center">
                        <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                        <span class="text-sm text-gray-700">{{ document.nom }}</span>
                      </div>
                      <div class="flex space-x-2">
                        <a 
                          :href="route('documents.download', document.id)" 
                          class="text-indigo-600 hover:text-indigo-900"
                          title="Télécharger"
                        >
                          <i class="fas fa-download"></i>
                        </a>
                        <button 
                          type="button" 
                          @click="deleteDocument(document.id)"
                          class="text-red-600 hover:text-red-900"
                          title="Supprimer"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between">
                  <DangerButton 
                    type="button"
                    @click="confirmDelete"
                    class="bg-red-600 hover:bg-red-700"
                  >
                    Supprimer ce paiement
                  </DangerButton>
                  <div>
                    <Link 
                      :href="route('paiements.index')" 
                      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                    >
                      Annuler
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                      Enregistrer les modifications
                    </PrimaryButton>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation de suppression -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Supprimer le paiement
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton 
          class="ml-2" 
          @click="deletePaiement"
          :class="{ 'opacity-25': deleteProcessing }"
          :disabled="deleteProcessing"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  paiement: {
    type: Object,
    required: true
  },
  eleves: {
    type: Array,
    default: () => []
  },
  moisList: {
    type: Array,
    default: () => [
      'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
      'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ]
  }
});

const showDeleteModal = ref(false);
const deleteProcessing = ref(false);

const form = useForm({
  eleve_id: props.paiement.eleve_id,
  date_paiement: props.paiement.date_paiement.split('T')[0],
  montant: props.paiement.montant,
  mode_paiement: props.paiement.mode_paiement,
  mois: props.paiement.mois,
  annee: props.paiement.annee,
  statut: props.paiement.statut,
  reference: props.paiement.reference || '',
  notes: props.paiement.notes || '',
  documents: []
});

function submit() {
  form.put(route('paiements.update', props.paiement.id), {
    onSuccess: () => {
      // Gérer le succès (redirection ou message)
    },
  });
}

function handleFileUpload(event) {
  const files = event.target.files;
  if (files.length > 0) {
    for (let i = 0; i < files.length; i++) {
      form.documents.push(files[i]);
    }
  }
}

function deleteDocument(documentId) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
    router.delete(route('documents.destroy', documentId), {
      onSuccess: () => {
        // Mettre à jour la liste des documents après suppression
        const index = props.paiement.documents.findIndex(doc => doc.id === documentId);
        if (index > -1) {
          props.paiement.documents.splice(index, 1);
        }
      }
    });
  }
}

function confirmDelete() {
  showDeleteModal.value = true;
}

function deletePaiement() {
  deleteProcessing.value = true;
  
  router.delete(route('paiements.destroy', props.paiement.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      deleteProcessing.value = false;
    },
    onError: () => {
      deleteProcessing.value = false;
    }
  });
}
</script>
