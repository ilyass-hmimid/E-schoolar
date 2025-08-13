const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

/** @type {import('tailwindcss').Config} */
module.exports = {
    // Désactiver le préfligge pour éviter les conflits avec AdminLTE
    corePlugins: {
        preflight: false,
    },
    
    // Important: spécifier un sélecteur de conteneur pour éviter les conflits
    important: '#app',
    
    // Configuration du content pour inclure tous les fichiers nécessaires
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/admin-lte/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],
    
    // Désactiver la purge CSS en développement
    safelist: [
        // Couleurs de base
        'bg-white', 'text-white', 'border-white',
        'bg-gray-50', 'text-gray-50', 'border-gray-50',
        'bg-gray-100', 'text-gray-100', 'border-gray-100',
        'bg-gray-200', 'text-gray-200', 'border-gray-200',
        'bg-gray-300', 'text-gray-300', 'border-gray-300',
        'bg-gray-400', 'text-gray-400', 'border-gray-400',
        'bg-gray-500', 'text-gray-500', 'border-gray-500',
        'bg-gray-600', 'text-gray-600', 'border-gray-600',
        'bg-gray-700', 'text-gray-700', 'border-gray-700',
        'bg-gray-800', 'text-gray-800', 'border-gray-800',
        'bg-gray-900', 'text-gray-900', 'border-gray-900',
        
        // Couleurs primaires
        'bg-primary-50', 'text-primary-50', 'border-primary-50',
        'bg-primary-100', 'text-primary-100', 'border-primary-100',
        'bg-primary-200', 'text-primary-200', 'border-primary-200',
        'bg-primary-300', 'text-primary-300', 'border-primary-300',
        'bg-primary-400', 'text-primary-400', 'border-primary-400',
        'bg-primary-500', 'text-primary-500', 'border-primary-500',
        'bg-primary-600', 'text-primary-600', 'border-primary-600',
        'bg-primary-700', 'text-primary-700', 'border-primary-700',
        'bg-primary-800', 'text-primary-800', 'border-primary-800',
        'bg-primary-900', 'text-primary-900', 'border-primary-900',
        
        // États de survol et focus
        'hover:bg-gray-50', 'hover:text-gray-50', 'hover:border-gray-50',
        'hover:bg-gray-100', 'hover:text-gray-100', 'hover:border-gray-100',
        'hover:bg-primary-500', 'hover:text-primary-500', 'hover:border-primary-500',
        'hover:bg-primary-600', 'hover:text-primary-600', 'hover:border-primary-600',
        'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-primary-500',
        'focus:border-primary-500', 'focus:outline-none',
        
        // Classes utilitaires
        'shadow-sm', 'shadow', 'shadow-md', 'shadow-lg', 'shadow-xl',
        'rounded', 'rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-full',
        'border', 'border-2', 'border-4', 'border-8',
        'p-1', 'p-2', 'p-3', 'p-4', 'p-5', 'p-6', 'p-8', 'p-10',
        'm-1', 'm-2', 'm-3', 'm-4', 'm-5', 'm-6', 'm-8', 'm-10',
        'w-full', 'w-auto', 'h-full', 'h-auto',
        'flex', 'flex-row', 'flex-col', 'items-center', 'justify-center', 'space-x-2', 'space-y-2',
        'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl', 'text-4xl',
        'font-light', 'font-normal', 'font-medium', 'font-semibold', 'font-bold',
        'transition-all', 'duration-200', 'ease-in-out',
        'opacity-0', 'opacity-25', 'opacity-50', 'opacity-75', 'opacity-100',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                secondary: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#7c3aed',
                    700: '#6d28d9',
                    800: '#5b21b6',
                    900: '#4c1d95',
                },
                success: {
                    50: '#f0fdf4',
                    500: '#10b981',
                    700: '#047857',
                },
                warning: {
                    50: '#fffbeb',
                    500: '#f59e0b',
                    700: '#b45309',
                },
                danger: {
                    50: '#fef2f2',
                    500: '#ef4444',
                    700: '#b91c1c',
                },
                gray: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
            },
            boxShadow: {
                'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
            },
            borderRadius: {
                'xl': '1rem',
                '2xl': '1.5rem',
            },
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            },
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
