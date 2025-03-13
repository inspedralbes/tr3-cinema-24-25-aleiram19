// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  css: [
    'bootstrap/dist/css/bootstrap.min.css',
    '~/assets/css/main.css'
  ],
  plugins: [
    { src: '~/plugins/bootstrap.client.js', mode: 'client' }
  ],
  modules: [
    '@nuxtjs/tailwindcss'
  ],
  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: '~/tailwind.config.js',
    exposeConfig: false,
    injectPosition: 0,
    viewer: true,
  },
  app: {
    baseURL: '/',
    buildAssetsDir: '/_nuxt/'
  },
  nitro: {
    devProxy: {
      '/img': {
        target: 'http://localhost:3000/img',
        changeOrigin: true
      }
    }
  },
  vite: {
    server: {
      watch: {
        usePolling: true
      },
      host: '0.0.0.0',
      strictPort: false,
      hmr: {
        host: '0.0.0.0',
        clientPort: 24678,
        port: 24678
      }
    }
  }
})
