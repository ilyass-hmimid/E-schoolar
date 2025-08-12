<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const passwordField = ref(null);
const showPassword = ref(false);
const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
            passwordField.value?.focus();
        },
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <AuthLayout title="Connexion">
        <!-- Message de statut -->
        <div 
            v-if="status" 
            class="mb-6 p-4 rounded-md bg-green-50 text-green-700 text-sm"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div>
                <label 
                    for="email" 
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Adresse email
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="email"
                        :class="{
                            'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': form.errors.email,
                            'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500': !form.errors.email
                        }"
                        class="block w-full pl-10 pr-3 py-2 border rounded-md shadow-sm appearance-none focus:outline-none sm:text-sm"
                        placeholder="votre@email.com"
                    >
                </div>
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Mot de passe -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label 
                        for="password" 
                        class="block text-sm font-medium text-gray-700"
                    >
                        Mot de passe
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-indigo-600 hover:text-indigo-500"
                    >
                        Mot de passe oublié ?
                    </Link>
                </div>
                
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        ref="passwordField"
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        :class="{
                            'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': form.errors.password,
                            'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500': !form.errors.password
                        }"
                        class="block w-full pl-10 pr-10 py-2 border rounded-md shadow-sm appearance-none focus:outline-none sm:text-sm"
                        placeholder="••••••••"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button 
                            type="button"
                            @click="togglePassword"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none"
                        >
                            <svg v-if="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Se souvenir de moi -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Se souvenir de moi
                    </label>
                </div>
            </div>

            <!-- Bouton de connexion -->
            <div>
                <button
                    type="submit"
                    :disabled="form.processing || isLoading"
                    :class="{
                        'opacity-75 cursor-not-allowed': form.processing || isLoading,
                        'bg-indigo-600 hover:bg-indigo-700': !form.processing && !isLoading
                    }"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ isLoading ? 'Connexion en cours...' : 'Se connecter' }}</span>
                </button>
            </div>

            <!-- Lien vers l'inscription -->
            <div class="text-center text-sm text-gray-500">
                Vous n'avez pas de compte ?
                <Link 
                    v-if="route().has('register')" 
                    :href="route('register')" 
                    class="font-medium text-indigo-600 hover:text-indigo-500"
                >
                    Créer un compte
                </Link>
                <span v-else class="text-gray-400">
                    Contactez l'administrateur
                </span>
            </div>
        </form>
    </AuthLayout>
</template>
