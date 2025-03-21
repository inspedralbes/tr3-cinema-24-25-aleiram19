<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-blue-900 bg-opacity-90">
    <div class="w-full max-w-md space-y-8 bg-blue-900 bg-opacity-80 rounded-xl shadow-xl p-8 sm:p-10 text-white">
      <!-- Header -->
      <div class="text-center">
        <h2 class="text-3xl font-bold mb-2">¡Completa tu compra!</h2>
        <p class="text-gray-400 text-sm">
          Elige cómo deseas continuar con el proceso de tu ticket
        </p>
      </div>

      <!-- Opciones de autenticación -->
      <div class="mt-8 space-y-6">
        <!-- Iniciar sesión -->
        <div>
          <button 
            @click="goToLogin"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-sign-in-alt group-hover:text-blue-400 text-blue-500 transition-colors"></i>
            </span>
            Iniciar Sesión
          </button>
          <p class="text-xs text-center mt-2 text-gray-400">Accede a tu cuenta para obtener beneficios exclusivos</p>
        </div>

        <!-- Separador -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-700"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-blue-900 text-gray-500">o</span>
          </div>
        </div>

        <!-- Continuar como invitado -->
        <div>
          <button 
            @click="goToGuest"
            class="group relative w-full flex justify-center py-3 px-4 border border-gray-700 rounded-lg text-white font-medium bg-transparent hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-user group-hover:text-blue-400 text-gray-500 transition-colors"></i>
            </span>
            Continuar como Invitado
          </button>
          <p class="text-xs text-center mt-2 text-gray-400">Sin registro, solo necesitamos algunos datos básicos</p>
        </div>
      </div>

      <!-- Continuar con redes sociales -->
      <div class="space-y-3 mt-6">
        <button 
          type="button"
          class="w-full flex items-center justify-center gap-3 px-4 py-2 border border-gray-700 rounded-lg shadow-sm text-gray-900 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
        >
          <i class="fab fa-google text-red-500"></i>
          <span>Continuar con Google</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const goToLogin = () => {
  router.push('/login');
};

const goToGuest = () => {
  router.push('/guest-checkout');
};
</script>
