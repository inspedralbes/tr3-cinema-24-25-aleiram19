// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  css: [
    'bootstrap/dist/css/bootstrap.min.css',
    '~/assets/css/main.css'
  ],
  plugins: ['~/plugins/bootstrap.client.js'],
  postcss: {
    plugins: {
      '@tailwindcss/postcss': {},
      autoprefixer: {},
    },
  },
})