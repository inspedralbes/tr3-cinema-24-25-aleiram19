import { useAuthStore } from '~/stores/auth';

/**
 * Middleware para verificar si el usuario está autenticado
 */
export default defineNuxtRouteMiddleware(async (to, from) => {
  // Verificar si el usuario está autenticado usando el store
  const authStore = useAuthStore();
  
  // Si hay un token pero no tenemos datos del usuario, intentamos obtenerlos
  const token = localStorage.getItem('token');
  if (token && !authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      console.error('Error al cargar datos del usuario en el middleware:', error);
    }
  }
  
  // Ahora verificamos si está autenticado después de intentar cargar el usuario
  if (!authStore.isAuthenticated) {
    // Si no está autenticado, redirigir a login con un parámetro de redirección
    const redirectPath = to.fullPath;
    console.log('Redirigiendo a login con parámetro redirect:', redirectPath);
    return navigateTo(`/login?redirect=${encodeURIComponent(redirectPath)}`);
  }
});