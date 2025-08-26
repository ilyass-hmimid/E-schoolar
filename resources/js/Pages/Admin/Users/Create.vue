<template>
  <AppLayout title="Ajouter un Utilisateur">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter un Nouvel Utilisateur
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <InputLabel for="name" value="Nom complet" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Email -->
              <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.email" class="mt-2" />
              </div>

              <!-- Password -->
              <div>
                <InputLabel for="password" value="Mot de passe" />
                <TextInput
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.password" class="mt-2" />
              </div>

              <!-- Confirm Password -->
              <div>
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
              </div>

              <!-- Role -->
              <div>
                <InputLabel for="role" value="Rôle" />
                <select
                  id="role"
                  v-model="form.role"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  required
                >
                  <option value="">Sélectionnez un rôle</option>
                  <option v-for="role in roles" :key="role.value" :value="role.value">
                    {{ role.label }}
                  </option>
                </select>
                <InputError :message="form.errors.role" class="mt-2" />
              </div>

              <!-- Phone -->
              <div>
                <InputLabel for="phone" value="Téléphone" />
                <TextInput
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
              </div>

              <!-- Address -->
              <div>
                <InputLabel for="address" value="Adresse" />
                <textarea
                  id="address"
                  v-model="form.address"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  rows="3"
                ></textarea>
                <InputError :message="form.errors.address" class="mt-2" />
              </div>

              <!-- Niveau -->
              <div>
                <InputLabel for="niveau_id" value="Niveau" />
                <select
                  id="niveau_id"
                  v-model="form.niveau_id"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                  <option value="">Sélectionnez un niveau</option>
                  <option v-for="niveau in niveaux" :key="niveau.id" :value="niveau.id">
                    {{ niveau.nom }}
                  </option>
                </select>
                <InputError :message="form.errors.niveau_id" class="mt-2" />
              </div>

              <!-- Filiere -->
              <div>
                <InputLabel for="filiere_id" value="Filière" />
                <select
                  id="filiere_id"
                  v-model="form.filiere_id"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                  <option value="">Sélectionnez une filière</option>
                  <option v-for="filiere in filieres" :key="filiere.id" :value="filiere.id">
                    {{ filiere.nom }}
                  </option>
                </select>
                <InputError :message="form.errors.filiere_id" class="mt-2" />
              </div>

              <!-- Somme à Payer -->
              <div>
                <InputLabel for="somme_a_payer" value="Somme à Payer" />
                <TextInput
                  id="somme_a_payer"
                  v-model="form.somme_a_payer"
                  type="number"
                  min="0"
                  step="0.01"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.somme_a_payer" class="mt-2" />
              </div>

              <!-- Date de Début -->
              <div>
                <InputLabel for="date_debut" value="Date de Début" />
                <TextInput
                  id="date_debut"
                  v-model="form.date_debut"
                  type="date"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.date_debut" class="mt-2" />
              </div>

              <!-- Statut -->
              <div class="flex items-center">
                <input
                  id="is_active"
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <InputLabel for="is_active" value="Compte Actif" class="ml-2" />
                <InputError :message="form.errors.is_active" class="mt-2" />
              </div>
            </div>

            <div class="flex items-center justify-end mt-6">
              <Link
                :href="route('admin.users.index')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Annuler
              </Link>
              <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Créer l'utilisateur
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
import { Link } from '@inertiajs/vue3';

defineProps({
  roles: {
    type: Array,
    required: true,
  },
  niveaux: {
    type: Array,
    required: true,
  },
  filieres: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  phone: '',
  address: '',
  niveau_id: '',
  filiere_id: '',
  somme_a_payer: 0,
  date_debut: '',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.users.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
