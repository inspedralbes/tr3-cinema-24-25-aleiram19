<template>
  <transition-group tag="div" name="toast" class="fixed top-4 right-4 z-50 space-y-2">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="[
        'max-w-xs p-4 rounded-lg shadow-lg flex items-start transform transition-all duration-300',
        toast.type === 'error' ? 'bg-gradient-to-r from-red-600 to-red-500 text-white' : 
        toast.type === 'success' ? 'bg-gradient-to-r from-green-600 to-green-500 text-white' : 
        'bg-gradient-to-r from-blue-600 to-blue-500 text-white'
      ]"
    >
      <div class="flex-shrink-0 mr-3 mt-0.5">
        <i :class="[
          'fas',
          toast.type === 'error' ? 'fa-times-circle' : 
          toast.type === 'success' ? 'fa-check-circle' : 
          'fa-info-circle'
        ]"></i>
      </div>
      <div class="flex-1">
        <p class="font-medium">{{ toast.message }}</p>
      </div>
      <button 
        @click="removeToast(toast.id)" 
        class="ml-3 flex-shrink-0 text-white opacity-75 hover:opacity-100"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>
  </transition-group>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Estado para mantener todos los toasts activos
const toasts = ref([]);

// Función para agregar un nuevo toast
const addToast = (message, type = 'info', timeout = 5000) => {
  const id = Date.now(); // ID único basado en timestamp
  toasts.value.push({ id, message, type });
  
  // Auto-eliminar después del timeout
  setTimeout(() => {
    removeToast(id);
  }, timeout);
  
  return id;
};

// Función para eliminar un toast por ID
const removeToast = (id) => {
  const index = toasts.value.findIndex(toast => toast.id === id);
  if (index !== -1) {
    toasts.value.splice(index, 1);
  }
};

// Función para mostrar un toast de éxito
const showSuccess = (message, timeout = 5000) => {
  return addToast(message, 'success', timeout);
};

// Función para mostrar un toast de error
const showError = (message, timeout = 5000) => {
  return addToast(message, 'error', timeout);
};

// Función para mostrar un toast informativo
const showInfo = (message, timeout = 5000) => {
  return addToast(message, 'info', timeout);
};

// Exponer métodos para usar desde otros componentes
defineExpose({
  addToast,
  removeToast,
  showSuccess,
  showError,
  showInfo
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
