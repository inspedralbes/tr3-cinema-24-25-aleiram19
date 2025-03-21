<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-blue-900 bg-opacity-90">
    <div class="w-full max-w-md space-y-8 bg-blue-900 bg-opacity-80 rounded-xl shadow-xl p-8 sm:p-10 text-white">
      <!-- Header -->
      <div class="text-center">
        <h2 class="text-3xl font-bold mb-2">
          {{ isLogin ? 'Iniciar Sesión' : 'Crear una cuenta' }}
        </h2>
        <p class="text-gray-400 text-sm">
          {{ isLogin ? 'Ingresa a tu cuenta para disfrutar de beneficios exclusivos' : 'Regístrate para obtener acceso a beneficios exclusivos' }}
        </p>
      </div>

      <!-- Alerta de error -->
      <div v-if="authStore.error" class="bg-red-600 bg-opacity-80 text-white p-3 rounded-lg text-sm">
        <div class="flex items-center space-x-2">
          <i class="fas fa-exclamation-circle"></i>
          <div>
            <p class="font-medium">Error</p>
            <p>{{ authStore.error }}</p>
          </div>
        </div>
        <button @click="authStore.error = null" class="absolute top-2 right-2 text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="mt-8 space-y-6">
        <!-- Nombre (solo en registro) -->
        <div v-if="!isLogin" class="space-y-2">
          <label for="name" class="block text-sm font-medium text-gray-300">Nombre</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              id="name" 
              v-model="form.name" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu nombre" 
              required
            >
          </div>
        </div>
        <!-- Lastname (solo en registro) -->
        <div v-if="!isLogin" class="space-y-2">
          <label for="last_name" class="block text-sm font-medium text-gray-300">Apellido</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              id="last_name" 
              v-model="form.last_name" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu apellido" 
              required
            >
          </div>
        </div>

        <!-- Email -->
        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium text-gray-300">Correo electrónico</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-500"></i>
            </div>
            <input 
              id="email" 
              v-model="form.email" 
              type="email" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu correo electrónico" 
              required
            >
          </div>
        </div>

        <!-- Contraseña -->
        <div class="space-y-2">
          <label for="password" class="block text-sm font-medium text-gray-300">Contraseña</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-500"></i>
            </div>
            <input 
              id="password" 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              class="block w-full pl-10 pr-10 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu contraseña" 
              required
            >
            <button 
              type="button" 
              @click="togglePassword" 
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-blue-500 transition-colors"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>

        <!-- Confirmar Contraseña (solo en registro) -->
        <div v-if="!isLogin" class="space-y-2">
          <label for="confirmPassword" class="block text-sm font-medium text-gray-300">Confirmar contraseña</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-500"></i>
            </div>
            <input 
              id="confirmPassword" 
              v-model="form.confirmPassword" 
              :type="showPassword ? 'text' : 'password'" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Confirma tu contraseña" 
              required
            >
          </div>
          <div v-if="!passwordsMatch && form.confirmPassword" class="text-red-500 text-xs mt-1">
            Las contraseñas no coinciden
          </div>
        </div>

        <!-- Opciones adicionales -->
        <div v-if="isLogin" class="flex items-center justify-between">
          <div class="flex items-center">
            <input 
              id="remember" 
              v-model="form.remember" 
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-300">
              Recordarme
            </label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-500 hover:text-blue-400 transition-colors">
              ¿Olvidaste tu contraseña?
            </a>
          </div>
        </div>

        <!-- Términos y condiciones (solo en registro) -->
        <div v-if="!isLogin" class="space-y-2">
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input 
                id="terms" 
                v-model="form.acceptTerms" 
                type="checkbox" 
                required
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800"
              >
            </div>
            <div class="ml-3 text-sm">
              <label for="terms" class="text-gray-300">
                Acepto los <a href="#" class="text-blue-500 hover:text-blue-400 transition-colors">Términos y Condiciones</a> y la <a href="#" class="text-blue-500 hover:text-blue-400 transition-colors">Política de Privacidad</a>
              </label>
            </div>
          </div>
        </div>

        <!-- Botón de envío -->
        <div>
          <button 
            type="submit" 
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:bg-gray-600 disabled:cursor-not-allowed"
            :disabled="!isFormValid || authStore.loading"
          >
            <span v-if="authStore.loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-spinner fa-spin text-blue-400"></i>
            </span>
            <span v-else class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-sign-in-alt group-hover:text-blue-400 text-blue-500 transition-colors"></i>
            </span>
            {{ isLogin ? 'Iniciar Sesión' : 'Registrarse' }}
          </button>
        </div>

        <!-- Cambiar entre login y registro -->
        <div class="text-center text-sm text-gray-300">
          {{ isLogin ? '¿No tienes una cuenta?' : '¿Ya tienes una cuenta?' }}
          <a href="#" @click.prevent="toggleForm" class="font-medium text-blue-500 hover:text-blue-400 transition-colors">
            {{ isLogin ? 'Regístrate' : 'Inicia sesión' }}
          </a>
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

        <!-- Iniciar sesión con redes sociales -->
        <div class="space-y-3">
          <button 
            type="button"
            class="w-full flex items-center justify-center gap-3 px-4 py-2 border border-gray-700 rounded-lg shadow-sm text-gray-900 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
          >
            <i class="fab fa-google text-red-500"></i>
            <span>Continuar con Google</span>
          </button>

          
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '~/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const { $toast } = useNuxtApp();

const props = defineProps({
  isLoginProp: {
    type: Boolean,
    default: true
  }
});

const isLogin = ref(props.isLoginProp);
const showPassword = ref(false);
const form = ref({
  name: '',
  last_name: '',
  email: '',
  password: '',
  confirmPassword: '',
  remember: false,
  acceptTerms: false
});

const passwordsMatch = computed(() => {
  return form.value.password === form.value.confirmPassword;
});

const isFormValid = computed(() => {
  if (isLogin.value) {
    return form.value.email && form.value.password;
  } else {
    return form.value.name && 
           form.value.last_name &&
           form.value.email && 
           form.value.password && 
           passwordsMatch.value && 
           form.value.acceptTerms;
  }
});

const toggleForm = () => {
  isLogin.value = !isLogin.value;
  // Limpiar formulario al cambiar
  form.value = {
    name: '',
    last_name: '',
    email: '',
    password: '',
    confirmPassword: '',
    remember: false,
    acceptTerms: false
  };
};

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const submitForm = async () => {
  if (!isFormValid.value) return;
  
  if (isLogin.value) {
    // Lógica de inicio de sesión con Pinia
    const success = await authStore.login({
      email: form.value.email,
      password: form.value.password,
      remember: form.value.remember
    });
    
    if (success) {
      $toast.success(`¡Bienvenido de nuevo, ${authStore.user.name}!`);
      router.push('/');
    }
  } else {
    // Verificar que las contraseñas coincidan
    if (form.value.password !== form.value.confirmPassword) {
      authStore.error = 'Las contraseñas no coinciden';
      return;
    }
    
    // Lógica de registro con Pinia
    const success = await authStore.register({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.confirmPassword,
      last_name: form.value.last_name || ''
    });
    
    if (success) {
      $toast.success(`¡Bienvenido a CineXperience, ${form.value.name}! Tu cuenta ha sido creada correctamente.`);
      router.push('/');
    }
  }
};
</script>
