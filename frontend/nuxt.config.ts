// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  css: [
    'bootstrap/dist/css/bootstrap.min.css',
    '~/assets/css/main.css'
  ],
  plugins: [
    { src: '~/plugins/bootstrap.client.js', mode: 'client' },
    { src: '~/plugins/router.js', mode: 'client' },
    { src: '~/plugins/toast.js', mode: 'client' }
  ],
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt'
  ],
  nitro: {
    devProxy: {
      '/api': {
        target: 'http://localhost:8000/api',
        changeOrigin: true,
        prependPath: false,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  },
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
  // Desactivar SSR para ejecutar solo en modo cliente (SPA)
  ssr: false,
  // Configuración de vite simplificada para resolver el problema de conexión
  vite: {
    define: {
      global: 'globalThis'
    },
    server: {
      watch: {
        usePolling: true
      }
      // Se elimina la configuración de host para que Docker la maneje
    }
  },
  // Configuración para asegurar que la página inicial se cargue correctamente
  pages: true,
  router: {
    options: {
      strict: false
    }
  },
  // Configuración de runtimeConfig para variables de entorno
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL || 'http://localhost:8000/api'
    }
  }
})
