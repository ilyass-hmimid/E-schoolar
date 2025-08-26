<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Modifier la filière : {{ filiere.nom }}
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2">
              <!-- Nom de la filière -->
              <div>
                <InputLabel for="nom" value="Nom de la filière" />
                <TextInput
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  class="block w-full mt-1"
                  required
                />
                <InputError :message="form.errors.nom" class="mt-2" />
              </div>

              <!-- Abréviation -->
              <div>
                <InputLabel for="abreviation" value="Abréviation (optionnel)" />
                <TextInput
                  id="abreviation"
                  v-model="form.abreviation"
                  type="text"
                  class="block w-full mt-1"
                  maxlength="10"
                />
                <p class="mt-1 text-sm text-gray-500">
                  Ex: SM pour Sciences Mathématiques
                </p>
                <InputError :message="form.errors.abreviation" class="mt-2" />
              </div>

              <!-- Niveau -->
              <div>
                <InputLabel for="niveau_id" value="Niveau" />
                <select
                  id="niveau_id"
                  v-model="form.niveau_id"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Sélectionnez un niveau</option>
                  <option 
                    v-for="niveau in niveaux" 
                    :key="niveau.id" 
                    :value="niveau.id"
                    :selected="niveau.id === filiere.niveau_id"
                  >
                    {{ niveau.nom }}
                  </option>
                </select>
                <InputError :message="form.errors.niveau_id" class="mt-2" />
              </div>

              <!-- Statut -->
              <div>
                <InputLabel for="est_actif" value="Statut" />
                <select
                  id="est_actif"
                  v-model="form.est_actif"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option :value="true" :selected="filiere.est_actif">Actif</option>
                  <option :value="false" :selected="!filiere.est_actif">Inactif</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Une filière inactive ne sera pas visible dans les listes de sélection
                </p>
                <InputError :message="form.errors.est_actif" class="mt-2" />
              </div>

              <!-- Matières -->
              <div class="sm:col-span-2">
                <InputLabel value="Matières" />
                <div class="grid grid-cols-1 gap-2 mt-2 sm:grid-cols-2 md:grid-cols-3">
                  <div 
                    v-for="matiere in matieres" 
                    :key="matiere.id"
                    class="flex items-start"
                  >
                    <div class="flex items-center h-5">
                      <input
                        :id="`matiere-${matiere.id}`"
                        v-model="form.matieres"
                        type="checkbox"
                        :value="matiere.id"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label :for="`matiere-${matiere.id}`" class="font-medium text-gray-700">
                        {{ matiere.nom }}
                      </label>
                      <p class="text-gray-500">
                        {{ matiere.prix }} DH
                      </p>
                    </div>
                  </div>
                </div>
                <InputError :message="form.errors.matieres" class="mt-2" />
              </div>

              <!-- Description -->
              <div class="sm:col-span-2">
                <InputLabel for="description" value="Description (optionnel)" />
                <TextArea
                  id="description"
                  v-model="form.description"
                  class="block w-full mt-1"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>
            </div>

            <div class="flex items-center justify-end mt-6">
              <Link
                :href="route('admin.filieres.index')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <PrimaryButton
                type="submit"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Mettre à jour
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
  filiere: Object,
  niveaux: Array,
  matieres: Array,
});

const form = useForm({
  nom: props.filiere.nom,
  abreviation: props.filiere.abreviation || '',
  niveau_id: props.filiere.niveau_id,
  description: props.filiere.description || '',
  est_actif: props.filiere.est_actif,
  matieres: props.filiere.matieres?.map(m => m.id) || [],
});

const submit = () => {
  form.put(route('admin.filieres.update', props.filiere.id));
};
</script>
