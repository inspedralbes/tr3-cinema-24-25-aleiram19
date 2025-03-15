<template>
  <div class="min-h-screen bg-[#051D40] text-white p-8">
    <h1 class="text-3xl font-bold mb-6">Prueba de Conexión API</h1>
    
    <div class="mb-8">
      <div class="mb-4">
        <h2 class="text-xl font-semibold mb-2">Configuración</h2>
        <p><strong>API Base URL:</strong> {{ apiBaseUrl }}</p>
        <p><strong>Test URL:</strong> {{ apiBaseUrl.replace(/\/api\/?$/, '') + '/api/test' }}</p>
      </div>
      
      <button 
        @click="testConnection" 
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        :disabled="loading"
      >
        {{ loading ? 'Probando...' : 'Probar Conexión' }}
      </button>
    </div>
    
    <div v-if="testResult" class="p-4 rounded" :class="testResult.success ? 'bg-green-900' : 'bg-red-900'">
      <h2 class="text-xl font-semibold mb-2">Resultado</h2>
      <p><strong>Estado:</strong> {{ testResult.success ? 'Exitoso' : 'Fallido' }}</p>
      <p><strong>Mensaje:</strong> {{ testResult.message }}</p>
      
      <div v-if="testResult.data" class="mt-4">
        <h3 class="font-semibold">Datos recibidos:</h3>
        <pre class="bg-gray-800 p-4 rounded mt-2 overflow-auto">{{ JSON.stringify(testResult.data, null, 2) }}</pre>
      </div>
      
      <div v-if="testResult.error" class="mt-4">
        <h3 class="font-semibold">Error:</h3>
        <pre class="bg-gray-800 p-4 rounded mt-2 overflow-auto">{{ JSON.stringify(testResult.error, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="mt-8">
      <h2 class="text-xl font-semibold mb-4">Probar Endpoints Específicos</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="endpoint in endpoints" 
          :key="endpoint.url" 
          class="bg-gray-800 p-4 rounded"
        >
          <h3 class="font-semibold mb-2">{{ endpoint.name }}</h3>
          <p class="text-gray-400 mb-2">{{ endpoint.url }}</p>
          <button 
            @click="testEndpoint(endpoint.url)"
            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
            :disabled="endpoint.loading"
          >
            {{ endpoint.loading ? 'Probando...' : 'Probar' }}
          </button>
          
          <div v-if="endpoint.result" class="mt-3">
            <p class="text-sm" :class="endpoint.result.success ? 'text-green-400' : 'text-red-400'">
              {{ endpoint.result.success ? 'Exitoso' : 'Fallido' }}
            </p>
            <p v-if="!endpoint.result.success" class="text-sm text-red-400">
              {{ endpoint.result.message }}
            </p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-8">
      <h2 class="text-xl font-semibold mb-2">Network Info</h2>
      <p><strong>Navegador online:</strong> {{ navigator.onLine ? 'Sí' : 'No' }}</p>
      <p><strong>User Agent:</strong> {{ navigator.userAgent }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRuntimeConfig } from 'nuxt/app';

const config = useRuntimeConfig();
const apiBaseUrl = computed(() => config.public.apiBaseUrl || '/api');

const loading = ref(false);
const testResult = ref(null);

const endpoints = ref([
  { name: 'Test API', url: '/api/test', loading: false, result: null },
  { name: 'Películas', url: '/api/movie', loading: false, result: null },
  { name: 'Géneros', url: '/api/genre', loading: false, result: null },
  { name: 'Sesiones', url: '/api/screening', loading: false, result: null }
]);

const testConnection = async () => {
  loading.value = true;
  testResult.value = null;
  
  try {
    const apiUrl = apiBaseUrl.value.replace(/\/api\/?$/, '') + '/api/test';
    const response = await fetch(apiUrl);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    
    testResult.value = {
      success: true,
      message: 'Conexión exitosa con el backend.',
      data: data
    };
  } catch (error) {
    console.error('Error testing API connection:', error);
    
    testResult.value = {
      success: false,
      message: 'No se pudo conectar con el backend.',
      error: {
        message: error.message,
        stack: error.stack
      }
    };
  } finally {
    loading.value = false;
  }
};

const testEndpoint = async (url) => {
  const endpoint = endpoints.value.find(e => e.url === url);
  if (!endpoint) return;
  
  endpoint.loading = true;
  endpoint.result = null;
  
  try {
    const apiUrl = apiBaseUrl.value.replace(/\/api\/?$/, '') + url;
    const response = await fetch(apiUrl);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    await response.json(); // Solo verificamos que se pueda parsear como JSON
    
    endpoint.result = {
      success: true,
      message: 'Endpoint funcionando correctamente'
    };
  } catch (error) {
    console.error(`Error testing endpoint ${url}:`, error);
    
    endpoint.result = {
      success: false,
      message: error.message
    };
  } finally {
    endpoint.loading = false;
  }
};

onMounted(() => {
  // No hacemos nada automáticamente para evitar sobrecarga de red
});
</script>
