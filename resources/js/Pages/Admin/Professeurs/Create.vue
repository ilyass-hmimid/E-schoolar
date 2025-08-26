<template>
  <AppLayout title="Ajouter un Professeur">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          <Link :href="route('admin.professeurs.index')" class="text-blue-600 hover:text-blue-900">
            Professeurs
          </Link>
          <span class="text-gray-500"> / </span>
          Ajouter un Professeur
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

              <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-end">
                  <Link 
                    :href="route('admin.professeurs.index')" 
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3"
                  >
                    Annuler
                  </Link>
                  <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Enregistrer
                  </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Multiselect from '@vueform/multiselect';

const props = defineProps({
  matieres: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
  address: '',
  matieres: [],
  date_embauche: new Date().toISOString().split('T')[0],
  salaire: '',
  is_active: true,
  password: generatePassword(),
  password_confirmation: ''
});

const matieresOptions = ref(props.matieres);

function generatePassword(length = 12) {
  const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\\:;?><,./-=';
  let password = '';
  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * charset.length);
    password += charset[randomIndex];
  }
  return password;
}

function submit() {
  form.post(route('admin.professeurs.store'), {
    onSuccess: () => {
      // Le mot de passe généré est envoyé dans le formulaire
      // et affiché dans un toast de succès par le contrôleur
    },
  });
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
