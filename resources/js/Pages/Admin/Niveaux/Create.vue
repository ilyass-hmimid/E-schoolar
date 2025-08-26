<template>
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Ajouter un niveau
      </h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2">
              <!-- Nom du niveau -->
              <div>
                <InputLabel for="nom" value="Nom du niveau" />
                <TextInput
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  class="block w-full mt-1"
                  required
                  autofocus
                />
                <InputError :message="form.errors.nom" class="mt-2" />
              </div>

              <!-- Type de niveau -->
              <div>
                <InputLabel for="type" value="Type de niveau" />
                <select
                  id="type"
                  v-model="form.type"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Sélectionnez un type</option>
                  <option value="primaire">Primaire</option>
                  <option value="college">Collège</option>
                  <option value="tronc_commun">Tronc Commun</option>
                  <option value="premiere_bac">1ère Bac</option>
                  <option value="deuxieme_bac">2ème Bac</option>
                </select>
                <InputError :message="form.errors.type" class="mt-2" />
              </div>

              <!-- Ordre d'affichage -->
              <div>
                <InputLabel for="ordre" value="Ordre d'affichage" />
                <TextInput
                  id="ordre"
                  v-model="form.ordre"
                  type="number"
                  min="1"
                  class="block w-full mt-1"
                  required
                />
                <p class="mt-1 text-sm text-gray-500">
                  Définit l'ordre d'affichage du niveau (1 = premier)
                </p>
                <InputError :message="form.errors.ordre" class="mt-2" />
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
                :href="route('admin.niveaux.index')"
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
                Enregistrer
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

const form = useForm({
  nom: '',
  type: '',
  ordre: 1,
  description: '',
});

const submit = () => {
  form.post(route('admin.niveaux.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>
