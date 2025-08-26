<template>
  <AppLayout :title="`Modifier ${professeur.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <Link :href="route('admin.professeurs.index')" class="text-blue-600 hover:text-blue-900">
            Professeurs
          </Link>
          <span class="text-gray-500"> / </span>
          Modifier {{ professeur.name }}
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                  
                  <div>
                    <InputLabel for="name" value="Nom complet *" />
                    <TextInput
                      id="name"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.name"
                      required
                      autofocus
                      autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                  </div>

                  <div>
                    <InputLabel for="email" value="Email *" />
                    <TextInput
                      id="email"
                      type="email"
                      class="mt-1 block w-full"
                      v-model="form.email"
                      required
                      autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                  </div>

                  <div>
                    <InputLabel for="phone" value="Téléphone" />
                    <TextInput
                      id="phone"
                      type="tel"
                      class="mt-1 block w-full"
                      v-model="form.phone"
                      autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                  </div>

                  <div>
                    <InputLabel for="address" value="Adresse" />
                    <TextInput
                      id="address"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.address"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                  </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium text-gray-900">Informations professionnelles</h3>
                  
                  <div>
                    <InputLabel for="matieres" value="Matières enseignées *" />
                    <Multiselect
                      v-model="form.matieres"
                      :options="matieresOptions"
                      :multiple="true"
                      :searchable="true"
                      :close-on-select="false"
                      placeholder="Sélectionner les matières"
                      label="nom"
                      track-by="id"
                      class="mt-1"
                    />
                    <InputError class="mt-2" :message="form.errors.matieres" />
                  </div>

                  <div>
                    <InputLabel for="date_embauche" value="Date d'embauche" />
                    <TextInput
                      id="date_embauche"
                      type="date"
                      class="mt-1 block w-full"
                      v-model="form.date_embauche"
                    />
                    <InputError class="mt-2" :message="form.errors.date_embauche" />
                  </div>

                  <div>
                    <InputLabel for="salaire" value="Salaire mensuel (DH)" />
                    <TextInput
                      id="salaire"
                      type="number"
                      min="0"
                      step="0.01"
                      class="mt-1 block w-full"
                      v-model="form.salaire"
                    />
                    <InputError class="mt-2" :message="form.errors.salaire" />
                  </div>

                  <div class="flex items-center">
                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                    <label for="is_active" class="ml-2 text-sm text-gray-600">
                      Compte actif
                    </label>
                  </div>
                </div>
              </div>

              <!-- Section mot de passe -->
              <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Réinitialiser le mot de passe</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <InputLabel for="password" value="Nouveau mot de passe" />
                    <TextInput
                      id="password"
                      type="password"
                      class="mt-1 block w-full"
                      v-model="form.password"
                      autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                  </div>
                  <div>
                    <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                    <TextInput
                      id="password_confirmation"
                      type="password"
                      class="mt-1 block w-full"
                      v-model="form.password_confirmation"
                      autocomplete="new-password"
                    />
                  </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                  Laissez vide pour ne pas modifier le mot de passe.
                </p>
              </div>

              <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-between">
                  <DangerButton 
                    type="button"
                    @click="confirmDelete"
                    class="bg-red-600 hover:bg-red-700"
                  >
                    Supprimer ce professeur
                  </DangerButton>
                  <div>
                    <Link 
                      :href="route('admin.professeurs.index')" 
                      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3"
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
        Supprimer le professeur
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer ce professeur ? Cette action est irréversible.
      </template>
      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Annuler
        </SecondaryButton>
        <DangerButton 
          class="ml-2" 
          @click="deleteProfesseur"
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
import Checkbox from '@/Components/Checkbox.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Multiselect from '@vueform/multiselect';

const props = defineProps({
  professeur: {
    type: Object,
    required: true
  },
  matieres: {
    type: Array,
    default: () => []
  },
  matieresEnseignees: {
    type: Array,
    default: () => []
  }
});

const showDeleteModal = ref(false);
const deleteProcessing = ref(false);

const form = useForm({
  name: props.professeur.name,
  email: props.professeur.email,
  phone: props.professeur.phone || '',
  address: props.professeur.address || '',
  matieres: props.matieresEnseignees.map(m => m.id),
  date_embauche: props.professeur.date_embauche || new Date().toISOString().split('T')[0],
  salaire: props.professeur.salaire || '',
  is_active: props.professeur.is_active,
  password: '',
  password_confirmation: ''
});

const matieresOptions = ref(props.matieres);

function submit() {
  form.put(route('admin.professeurs.update', props.professeur.id), {
    onSuccess: () => {
      form.reset('password', 'password_confirmation');
    },
  });
}

function confirmDelete() {
  showDeleteModal.value = true;
}

function deleteProfesseur() {
  deleteProcessing.value = true;
  
  router.delete(route('admin.professeurs.destroy', props.professeur.id), {
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

<style src="@vueform/multiselect/themes/default.css"></style>
