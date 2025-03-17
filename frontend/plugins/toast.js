import { ref, h, render } from 'vue';
import Toast from '~/components/ui/Toast.vue';

export default defineNuxtPlugin((nuxtApp) => {
  // Crear una referencia global para el componente Toast
  const toastRef = ref(null);
  
  // Crear un div y adjuntarlo al DOM para montar el Toast
  const toastContainer = document.createElement('div');
  document.body.appendChild(toastContainer);
  
  // Renderizar el componente Toast en el container
  const toastVNode = h(Toast, { ref: toastRef });
  render(toastVNode, toastContainer);
  
  // Esperar a que Vue haya montado el componente
  nuxtApp.hook('app:mounted', () => {
    // Proporcionar métodos para mostrar toasts desde cualquier parte de la aplicación
    nuxtApp.provide('toast', {
      success: (message, timeout) => toastRef.value?.showSuccess(message, timeout),
      error: (message, timeout) => toastRef.value?.showError(message, timeout),
      info: (message, timeout) => toastRef.value?.showInfo(message, timeout)
    });
  });
});
