module.exports = {
  plugins: {
    'postcss-import': {},
    'tailwindcss/nesting': {},
    tailwindcss: {
      config: './tailwind.config.js',
    },
    autoprefixer: {
      flexbox: 'no-2009',
      grid: 'autoplace',
    },
    ...(process.env.NODE_ENV === 'production' 
      ? {
          '@fullhuman/postcss-purgecss': {
            content: [
              './resources/views/**/*.blade.php',
              './resources/js/**/*.vue',
              './resources/js/**/*.js',
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
