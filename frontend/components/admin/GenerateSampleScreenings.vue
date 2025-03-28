<template>
  <div class="bg-blue-900/50 backdrop-blur-sm rounded-xl p-6 mb-4">
    <h2 class="text-xl font-bold text-white mb-4">Generar Proyecciones de Ejemplo</h2>
    
    <div v-if="loading" class="flex items-center justify-center py-4">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <p class="ml-3 text-blue-300">Generando proyecciones...</p>
    </div>
    
    <div v-else-if="success" class="bg-green-900/40 text-green-300 p-4 rounded-lg mb-4">
      {{ successMessage }}
    </div>
    
    <div v-else-if="error" class="bg-red-900/40 text-red-300 p-4 rounded-lg mb-4">
      {{ errorMessage }}
    </div>
    
    <form @submit.prevent="generateScreenings" class="space-y-4">
      <div>
        <label class="block text-gray-300 mb-2">Películas</label>
        <select
          v-model="selectedMovieIds"
          multiple
          class="w-full bg-blue-950/50 border border-blue-800 rounded-lg p-3 text-white"
        >
          <option v-for="movie in movies" :key="movie.id" :value="movie.id">
            {{ movie.title }}
          </option>
        </select>
        <p class="text-gray-400 text-sm mt-1">Mantén presionado Ctrl para seleccionar múltiples películas</p>
      </div>
      
      <div>
        <label class="block text-gray-300 mb-2">Auditorios</label>
        <select
          v-model="selectedAuditoriumIds"
          multiple
          class="w-full bg-blue-950/50 border border-blue-800 rounded-lg p-3 text-white"
        >
          <option v-for="auditorium in auditoriums" :key="auditorium.id" :value="auditorium.id">
            Sala {{ auditorium.number }}
          </option>
        </select>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-gray-300 mb-2">Fecha inicial</label>
          <input
            type="date"
            v-model="startDate"
            class="w-full bg-blue-950/50 border border-blue-800 rounded-lg p-3 text-white"
            :min="today"
          />
        </div>
        
        <div>
          <label class="block text-gray-300 mb-2">Días a generar</label>
          <input
            type="number"
            v-model="daysToGenerate"
            min="1"
            max="14"
            class="w-full bg-blue-950/50 border border-blue-800 rounded-lg p-3 text-white"
          />
        </div>
      </div>
      
      <div>
        <label class="block text-gray-300 mb-2">Horarios disponibles</label>
        <div class="flex flex-wrap gap-3">
          <label v-for="time in availableTimes" :key="time" class="flex items-center">
            <input
              type="checkbox"
              v-model="selectedTimes"
              :value="time"
              class="mr-2"
            />
            <span class="text-white">{{ time }}</span>
          </label>
        </div>
      </div>
      
      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-500 transition-colors"
        :disabled="!isFormValid || loading"
      >
        Generar Proyecciones
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useMoviesStore } from '@/stores/movies';
import { useScreeningsStore } from '@/stores/screenings';
import axios from 'axios';

// Stores
const moviesStore = useMoviesStore();
const screeningsStore = useScreeningsStore();

// Estado del formulario
const selectedMovieIds = ref([]);
const selectedAuditoriumIds = ref([]);
const startDate = ref(new Date().toISOString().split('T')[0]);
const daysToGenerate = ref(7);
const selectedTimes = ref(['16:00', '18:30', '21:00']);
const availableTimes = ['14:00', '16:00', '18:30', '21:00', '23:30'];

// Estado de la interfaz
const loading = ref(false);
const success = ref(false);
const error = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const movies = ref([]);
const auditoriums = ref([]);

// Fecha de hoy para validación
const today = new Date().toISOString().split('T')[0];

// Validación del formulario
const isFormValid = computed(() => {
  return selectedMovieIds.value.length > 0 &&
         selectedAuditoriumIds.value.length > 0 &&
         startDate.value &&
         daysToGenerate.value > 0 &&
         selectedTimes.value.length > 0;
});

// Cargar datos necesarios
onMounted(async () => {
  try {
    // Cargar películas
    await moviesStore.fetchCurrentMovies();
    movies.value = moviesStore.movies;
    
    // Cargar auditorios (necesitamos crear una función)
    await fetchAuditoriums();
  } catch (err) {
    error.value = true;
    errorMessage.value = 'Error al cargar datos iniciales: ' + err.message;
  }
});

// Función para obtener auditorios
const fetchAuditoriums = async () => {
  try {
    // Aquí usaremos una ruta pública para obtener auditorios
    const response = await axios.get('/api/public/auditoriums');
    auditoriums.value = response.data;
  } catch (err) {
    console.error('Error al cargar auditorios:', err);
    auditoriums.value = [
      { id: 1, number: 1 },
      { id: 2, number: 2 }
    ]; // Datos de respaldo por si falla
  }
};

// Función para generar proyecciones
const generateScreenings = async () => {
  if (!isFormValid.value) return;
  
  loading.value = true;
  success.value = false;
  error.value = false;
  
  try {
    const screeningsData = [];
    const startDateObj = new Date(startDate.value);
    
    // Generar datos para las proyecciones
    for (let day = 0; day < daysToGenerate.value; day++) {
      const currentDate = new Date(startDateObj);
      currentDate.setDate(currentDate.getDate() + day);
      const dateString = currentDate.toISOString().split('T')[0];
      
      // Para cada película, crear proyecciones en los horarios seleccionados
      for (const movieId of selectedMovieIds.value) {
        // Alternar entre los auditorios seleccionados
        const auditoriumIndex = day % selectedAuditoriumIds.value.length;
        const auditoriumId = selectedAuditoriumIds.value[auditoriumIndex];
        
        // Crear una proyección para cada horario seleccionado
        for (const time of selectedTimes.value) {
          screeningsData.push({
            movie_id: movieId,
            auditorium_id: auditoriumId,
            date: dateString,
            time: time,
            is_special: currentDate.getDay() === 3 // Miércoles día del espectador
          });
        }
      }
    }
    
    // Enviar datos de proyecciones al backend para crear
    const response = await axios.post('/api/public/screenings/batch', screeningsData);
    
    // Actualizar store de proyecciones
    await screeningsStore.fetchScreenings();
    
    // Mostrar mensaje de éxito
    success.value = true;
    successMessage.value = `Se han generado ${response.data.created} proyecciones correctamente`;
  } catch (err) {
    console.error('Error al generar proyecciones:', err);
    error.value = true;
    errorMessage.value = 'Error al generar proyecciones: ' + (err.response?.data?.message || err.message);
  } finally {
    loading.value = false;
  }
};
</script>