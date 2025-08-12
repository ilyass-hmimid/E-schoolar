<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    title: String,
});

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <Head :title="title" />

        <!-- Logo et titre -->
        <div class="w-full sm:max-w-md px-6 py-4">
            <div class="flex justify-center">
                <Link href="/">
                    <img 
                        src="/images/logo.png" 
                        alt="Allo Tawjih Logo" 
                        class="h-16 w-auto"
                        v-if="$page.props.app.logo"
                    />
                    <span v-else class="text-2xl font-bold text-indigo-600">
                        Allo Tawjih
                    </span>
                </Link>
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
