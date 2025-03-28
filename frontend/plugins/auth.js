import { useAuthStore } from '~/stores/auth';

export default defineNuxtPlugin(() => {
  // Inicializar el estado de autenticación al cargar la aplicación
  if (process.client) {
    const authStore = useAuthStore();
    authStore.init();
  }
});
