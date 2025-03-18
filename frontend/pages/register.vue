<template>
    <div class="min-h-screen bg-navy-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-xl">
        <div>
          <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Crear una cuenta
          </h2>
          <p class="mt-2 text-center text-sm text-gray-600">
            Regístrate para disfrutar de beneficios exclusivos
          </p>
        </div>

        <!-- Alerta de error -->
        <div v-if="authStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ authStore.error }}</span>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
          <div class="rounded-md shadow-sm -space-y-px">
            <div>
              <label for="name" class="sr-only">Nombre</label>
              <input id="name" v-model="form.name" type="text" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Nombre">
            </div>
            <div>
              <label for="name" class="sr-only">Apellido</label>
              <input id="name" v-model="form.last_name" type="text" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Apellido">
            </div>
            <div>
              <label for="email-address" class="sr-only">Email</label>
              <input id="email-address" v-model="form.email" type="email" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Email">
            </div>
            <div>
              <label for="password" class="sr-only">Contraseña</label>
              <input id="password" v-model="form.password" type="password" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Contraseña">
            </div>
            <div>
              <label for="password_confirmation" class="sr-only">Confirmar Contraseña</label>
              <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Confirmar Contraseña">
            </div>
          </div>

          <div class="flex items-center">
            <input id="terms" v-model="form.acceptTerms" type="checkbox" required
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              Acepto los <a href="#" class="text-indigo-600 hover:text-indigo-500">Términos y Condiciones</a>
            </label>
          </div>

          <div>
            <button type="submit"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500"
              :disabled="!isFormValid || authStore.loading">
              <span v-if="authStore.loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              Registrarse
            </button>
          </div>

          <div class="text-sm text-center">
            <p>
              ¿Ya tienes una cuenta? 
              <NuxtLink to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                Inicia sesión
              </NuxtLink>
            </p>
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

  const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    acceptTerms: false,
    last_name: ''
  });

  const isFormValid = computed(() => {
    return form.value.name && 
           form.value.email && 
           form.value.password && 
           form.value.password === form.value.password_confirmation &&
           form.value.acceptTerms;
  });
  
  const handleSubmit = async () => {
    if (!isFormValid.value) return;

    const success = await authStore.register({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      last_name: form.value.last_name
    });
    
    if (success) {
      router.push('/');
    }
  };
  </script>