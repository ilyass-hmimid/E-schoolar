module.exports = {
  plugins: {
    // Désactiver postcss-import pour éviter les problèmes avec AdminLTE
    // 'postcss-import': {},
    // Désactiver tailwindcss/nesting pour éviter les conflits
    // 'tailwindcss/nesting': {},
    
    // Configuration Tailwind simplifiée
    'tailwindcss': {},
    
    // Configuration Autoprefixer
    'autoprefixer': {
      flexbox: 'no-2009',
      grid: 'autoplace',
    },
    
    // Désactiver PurgeCSS en développement pour éviter les problèmes
    ...(process.env.NODE_ENV === 'production' 
      ? {
          '@fullhuman/postcss-purgecss': {
            content: [
              './resources/views/**/*.blade.php',
              './resources/js/**/*.vue',
              './resources/js/**/*.js',
              // Exclure spécifiquement les fichiers AdminLTE
              '!**/node_modules/admin-lte/**/*',
            ],
            // Exclure les classes utilisées dynamiquement
            safelist: [
              /bg-.*/,
              /text-.*/,
              /border-.*/,
              /hover:bg-.*/,
              /hover:text-.*/,
              /focus:ring-.*/,
              /focus:border-.*/,
              /focus:ring-.*/,
              /active:bg-.*/,
              /active:text-.*/,
            ],
            defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
            safelist: [
              /^swiper-/, /^carousel-/, /^modal-/, /^tooltip-/, /^popover-/, /^tippy-/,
              /^v-/, /^vue-/, /^vs-/, /^vs__/, /^flatpickr-/, /^iti/, /^dz-/, /^dropzone/,
              /^sortable/, /^sortable-/, /^ps__/, /^simplebar/, /^notification/, /^alert/,
              /^fade-/, /^show/, /^hide/, /^active/, /^disabled/, /^is-/, /^has-/, /^is_/,
              /^has_/, /^no-/, /^js-/, /^data-/, /^aria-/, /^role/
            ]
          }
        }
      : {})
  }
};
