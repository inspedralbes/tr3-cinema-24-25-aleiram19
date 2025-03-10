<template>
    <div class="min-h-screen bg-navy-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-xl">
        <div>
          <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Crear una cuenta
          </h2>
        </div>
        <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
          <div class="rounded-md shadow-sm -space-y-px">
            <div>
              <label for="name" class="sr-only">Nombre completo</label>
              <input id="name" v-model="form.name" type="text" required
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Nombre completo">
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
                class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Contraseña">
            </div>
          </div>
  
          <div>
            <button type="submit"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500">
              Registrarse
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  const form = ref({
    name: '',
    email: '',
    password: ''
  });
  
  const handleSubmit = () => {
    // Aquí iría la lógica de registro
    console.log('Form submitted:', form.value);
  };
  </script>