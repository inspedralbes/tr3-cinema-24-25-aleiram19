// Plugin para personalizar el comportamiento del router
export default defineNuxtPlugin((nuxtApp) => {
  const router = useRouter();
  
  // Establecer la página de inicio como la ruta por defecto
  router.options.routes.forEach(route => {
    if (route.path === '/') {
      route.redirect = '/';
    }
  });
  
  // Redirigir a la página de inicio si la ruta es '/screenings' y estamos cargando la app por primera vez
  nuxtApp.hook('app:mounted', () => {
    if (window.location.pathname === '/screenings' && !localStorage.getItem('hasVisitedBefore')) {
      // Guardar un indicador para evitar redirecciones en navegaciones futuras
      localStorage.setItem('hasVisitedBefore', 'true');
      router.push('/');
    }
  });
});