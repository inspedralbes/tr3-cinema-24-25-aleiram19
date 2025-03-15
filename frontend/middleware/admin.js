/**
 * Middleware para verificar si el usuario tiene rol de administrador
 */
export default defineNuxtRouteMiddleware((to, from) => {
  // Aquí deberías verificar si el usuario tiene permisos de administrador
  // Por ahora, simplemente asumiremos que lo tiene para el desarrollo
  const isAdmin = true; // Esto debería venir de un store o servicio
  
  if (!isAdmin) {
    // Si no tiene permisos, redirigir a la página inicial con un mensaje
    return navigateTo('/?error=unauthorized');
  }
});