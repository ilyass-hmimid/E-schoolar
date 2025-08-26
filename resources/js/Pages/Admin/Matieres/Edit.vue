<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Modifier la matière : {{ matiere.nom }}
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2">
              <!-- Nom de la matière -->
              <div>
                <InputLabel for="nom" value="Nom de la matière" />
                <TextInput
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  class="block w-full mt-1"
                  required
                />
                <InputError :message="form.errors.nom" class="mt-2" />
              </div>

              <!-- Prix pour l'élève -->
              <div>
                <InputLabel for="prix" value="Prix pour l'élève (DH)" />
                <TextInput
                  id="prix"
                  v-model="form.prix"
                  type="number"
                  min="0"
                  step="0.01"
                  class="block w-full mt-1"
                  required
                />
                <InputError :message="form.errors.prix" class="mt-2" />
              </div>

              <!-- Prix pour le professeur -->
              <div>
                <InputLabel for="prix_prof" value="Rémunération professeur (DH)" />
                <TextInput
                  id="prix_prof"
                  v-model="form.prix_prof"
                  type="number"
                  min="0"
                  step="0.01"
                  class="block w-full mt-1"
                  required
                />
                <p class="mt-1 text-sm text-gray-500">
                  Montant qui sera versé au professeur par séance
                </p>
                <InputError :message="form.errors.prix_prof" class="mt-2" />
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
                  <option :value="true" :selected="matiere.est_actif">Active</option>
                  <option :value="false" :selected="!matiere.est_actif">Inactive</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Une matière inactive ne sera pas disponible pour la sélection
                </p>
                <InputError :message="form.errors.est_actif" class="mt-2" />
              </div>

              <!-- Niveaux -->
              <div class="sm:col-span-2">
                <InputLabel value="Niveaux concernés" />
                <div class="grid grid-cols-1 gap-2 mt-2 sm:grid-cols-2 md:grid-cols-3">
                  <div 
                    v-for="niveau in niveaux" 
                    :key="niveau.id"
                    class="flex items-start"
                  >
                    <div class="flex items-center h-5">
                      <input
                        :id="`niveau-${niveau.id}`"
                        v-model="form.niveaux"
                        type="checkbox"
                        :value="niveau.id"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label :for="`niveau-${niveau.id}`" class="font-medium text-gray-700">
                        {{ niveau.nom }}
                      </label>
                      <p class="text-gray-500">
                        {{ getTypeLabel(niveau.type) }}
                      </p>
                    </div>
                  </div>
                </div>
                <InputError :message="form.errors.niveaux" class="mt-2" />
              </div>

              <!-- Description -->
              <div class="sm:col-span-2">
                <InputLabel for="description" value="Description (optionnel)" />
                <TextArea
                  id="description"
                  v-model="form.description"
                  class="block w-full mt-1"
                  rows="3"
                  placeholder="Ajoutez une description détaillée de la matière..."
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>
            </div>

            <div class="flex items-center justify-end mt-6">
              <Link
                :href="route('admin.matieres.index')"
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
  matiere: Object,
  niveaux: Array,
});

const form = useForm({
  nom: props.matiere.nom,
  description: props.matiere.description || '',
  prix: parseFloat(props.matiere.prix),
  prix_prof: parseFloat(props.matiere.prix_prof),
  est_actif: props.matiere.est_actif,
  niveaux: props.matiere.niveaux?.map(n => n.id) || [],
});

const submit = () => {
  form.put(route('admin.matieres.update', props.matiere.id));
};

const getTypeLabel = (type) => {
  const types = {
    primaire: 'Primaire',
    college: 'Collège',
    tronc_commun: 'Tronc Commun',
    premiere_bac: '1ère Bac',
    deuxieme_bac: '2ème Bac'
  };
  return types[type] || type;
};
</script>
