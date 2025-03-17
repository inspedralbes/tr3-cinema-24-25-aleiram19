<template>
  <div class="flex flex-col items-center justify-center h-screen bg-[#051D40] py-8">
    <div class="text-white text-center">
      <div class="animate-pulse mb-8">
        <div class="border-4 border-blue-600/30 border-t-blue-600 rounded-full w-12 h-12 animate-spin mx-auto"></div>
      </div>
      <p class="text-lg mb-2">Redirigiendo a la página de proyecciones...</p>
      <p class="text-sm text-blue-300">Verás los horarios disponibles para seleccionar tu función</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  const route = useRoute();
  // Redirige a la página unificada de selección de película, manteniendo los parámetros
  navigateTo(`/select-movie?id=${route.query.id || ''}`);
});
</script>
