<template>
    <div class="min-h-screen bg-navy-900 py-8">
      <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Asientos</h1>
  
        <div class="bg-white rounded-lg p-8">
          <!-- Pantalla -->
          <div class="w-full h-8 bg-gray-300 rounded mb-12 text-center text-sm">PANTALLA</div>
  
          <!-- Asientos -->
          <div class="grid grid-cols-10 gap-2 mb-8">
            <template v-for="row in 8" :key="row">
              <template v-for="seat in 10" :key="`${row}-${seat}`">
                <button
                  @click="toggleSeat(`${row}-${seat}`)"
                  :class="[
                    'w-8 h-8 rounded',
                    selectedSeats.includes(`${row}-${seat}`) ? 'bg-navy-600 text-white' : 'bg-gray-200',
                    occupiedSeats.includes(`${row}-${seat}`) ? 'bg-red-500 cursor-not-allowed' : 'hover:bg-navy-400'
                  ]"
                  :disabled="occupiedSeats.includes(`${row}-${seat}`)"
                >
                  {{ seat }}
                </button>
              </template>
            </template>
          </div>
  
          <!-- Leyenda -->
          <div class="flex justify-center space-x-8 mb-8">
            <div class="flex items-center">
              <div class="w-4 h-4 bg-gray-200 rounded mr-2"></div>
              <span>Disponible</span>
            </div>
            <div class="flex items-center">
              <div class="w-4 h-4 bg-navy-600 rounded mr-2"></div>
              <span>Seleccionado</span>
            </div>
            <div class="flex items-center">
              <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
              <span>Ocupado</span>
            </div>
          </div>
  
          <!-- Resumen y botón de confirmación -->
          <div class="text-center">
            <p class="mb-4">Asientos seleccionados: {{ selectedSeats.length }}</p>
            <p class="mb-4">Total: ${{ selectedSeats.length * 10 }}</p>
            <button
              @click="confirmSelection"
              class="bg-navy-600 text-white px-6 py-2 rounded hover:bg-navy-700"
              :disabled="selectedSeats.length === 0"
            >
              Confirmar selección
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const selectedSeats = ref([]);
  const occupiedSeats = ref(['1-2', '1-3', '4-5', '4-6']); // Asientos ya ocupados
  
  const toggleSeat = (seatId) => {
    if (occupiedSeats.value.includes(seatId)) return;
    
    const index = selectedSeats.value.indexOf(seatId);
    if (index === -1) {
      selectedSeats.value.push(seatId);
    } else {
      selectedSeats.value.splice(index, 1);
    }
  };
  
  const confirmSelection = () => {
    // Aquí iría la lógica para confirmar la selección
    console.log('Asientos confirmados:', selectedSeats.value);
  };
  </script>