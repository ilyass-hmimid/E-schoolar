<script setup>
import { ref, onMounted } from 'vue';
import { useHead } from '@vueuse/head';

const props = defineProps({
    title: {
        type: String,
        default: 'Connexion'
    },
});

const currentYear = ref(new Date().getFullYear());

// Mise à jour du titre de la page
useHead({
    title: props.title,
    meta: [
        { name: 'description', content: 'Page d\'authentification Allo Tawjih' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1.0' },
    ]
});
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <!-- Logo et titre -->
        <div class="w-full sm:max-w-md px-6 py-4">
            <div class="flex justify-center">
                <router-link to="/" class="flex flex-col items-center">
                    <img 
                        src="/images/logo.png" 
                        alt="Allo Tawjih Logo" 
                        class="h-16 w-auto mb-2"
                    />
                    <span class="text-2xl font-bold text-indigo-600">
                        Allo Tawjih
                    </span>
                </router-link>
            </div>
        </div>

        <!-- Carte de formulaire -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>

        <!-- Pied de page -->
        <div class="w-full sm:max-w-md px-6 py-4 text-center text-sm text-gray-500">
            &copy; {{ currentYear }} Allo Tawjih. Tous droits réservés.
        </div>
    </div>
</template>
