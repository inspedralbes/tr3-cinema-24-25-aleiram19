/**
 * Middleware para verificar si el usuario está autenticado
 */
export default defineNuxtRouteMiddleware((to, from) => {
  // Aquí deberías verificar si el usuario está autenticado
  // Por ahora, simplemente asumiremos que lo está para el desarrollo
  const isAuthenticated = true; // Esto debería venir de un store o servicio
  
  if (!isAuthenticated) {
    // Si no está autenticado, redirigir a login con un parámetro de redirección
    return navigateTo(`/login?redirect=${encodeURIComponent(to.fullPath)}`);
  }
});