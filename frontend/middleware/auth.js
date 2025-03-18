import { useAuthStore } from '~/stores/auth';

/**
 * Middleware para verificar si el usuario está autenticado
 */
export default defineNuxtRouteMiddleware((to, from) => {
  // Verificar si el usuario está autenticado usando el store
  const authStore = useAuthStore();
  
  if (!authStore.isAuthenticated) {
    // Si no está autenticado, redirigir a login con un parámetro de redirección
    return navigateTo(`/login?redirect=${encodeURIComponent(to.fullPath)}`);
  }
});