<template>
  <div class="container py-5">
    <div class="mb-4">
      <NuxtLink :to="`/movies/${screening.movie_id || ''}`" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-2"></i> Volver a la película
      </NuxtLink>
    </div>
    
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Seleccionar Asientos</h3>
          </div>
          
          <div class="card-body">
            <!-- Información de la proyección -->
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-2">Cargando información de la proyección...</p>
            </div>
            
            <div v-else-if="error" class="alert alert-danger">
              {{ error }}
            </div>
            
            <div v-else class="screening-info mb-4">
              <div class="row">
                <div class="col-md-8">
                  <h4>{{ screening.movie?.title || 'Película' }}</h4>
                  <div class="d-flex align-items-center text-muted mb-3">
                    <i class="bi bi-calendar-event me-2"></i>
                    <span class="me-3 badge bg-primary">{{ formatScreeningDate(screening.date_time) }}</span>
                    <i class="bi bi-building me-2"></i>
                    <span>{{ auditoriumName }}</span>
                  </div>
                </div>
                <div class="col-md-4 text-md-end">
                  <div class="price-tag d-inline-block px-3 py-2 rounded bg-light">
                    <small class="d-block text-muted">Precio por entrada</small>
                    <span class="fs-4 fw-bold text-primary">{{ formatPrice(screening.price) }}</span>
                  </div>
                  <div v-if="screening.is_special" class="badge bg-danger mt-2 d-block ms-auto me-0">Día del Espectador</div>
                </div>
              </div>
            </div>
            
            <!-- Aquí iría la selección de asientos -->
            <!-- Este es un componente que puedes desarrollar más adelante -->
            <div class="seat-selection py-3">
              <div class="text-center mb-3">
                <div class="screen-container mb-4">
                  <div class="screen">PANTALLA</div>
                </div>
                
                <!-- Grid de asientos (simplificado) -->
                <div class="seat-grid">
                  <!-- Filas de ejemplo (A-E) -->
                  <div v-for="row in ['A', 'B', 'C', 'D', 'E']" :key="row" class="seat-row mb-2">
                    <div class="row-label me-2">{{ row }}</div>
                    <!-- 10 asientos por fila -->
                    <div 
                      v-for="seat in 10" 
                      :key="`${row}-${seat}`" 
                      class="seat"
                      :class="{ 'seat-occupied': isOccupied(row, seat), 'seat-selected': isSelected(row, seat) }"
                      @click="toggleSeat(row, seat)"
                    >
                      {{ seat }}
                    </div>
                  </div>
                </div>
                
                <!-- Leyenda -->
                <div class="seat-legend d-flex justify-content-center mt-4">
                  <div class="me-4 d-flex align-items-center">
                    <div class="seat-sample"></div>
                    <span class="ms-2">Disponible</span>
                  </div>
                  <div class="me-4 d-flex align-items-center">
                    <div class="seat-sample seat-occupied"></div>
                    <span class="ms-2">Ocupado</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="seat-sample seat-selected"></div>
                    <span class="ms-2">Seleccionado</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Resumen de selección -->
            <div class="selection-summary mt-4 p-3 border rounded bg-light">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-1">Tu selección</h5>
                  <p class="mb-0">
                    <span v-if="selectedSeats.length">
                      {{ selectedSeats.length }} asiento(s): 
                      {{ selectedSeats.map(s => `${s.row}${s.number}`).join(', ') }}
                    </span>
                    <span v-else class="text-muted">Ningún asiento seleccionado</span>
                  </p>
                </div>
                <div class="text-end">
                  <div class="mb-1">Total</div>
                  <div class="fs-4 fw-bold text-primary">{{ formatPrice(totalPrice) }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card-footer">
            <div class="d-flex justify-content-between">
              <NuxtLink :to="`/movies/${screening.movie_id}`" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Volver a la película
              </NuxtLink>
              <button 
                class="btn btn-primary" 
                :disabled="selectedSeats.length === 0"
                @click="proceedToCheckout"
              >
                Continuar <i class="bi bi-arrow-right ms-2"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useScreeningsStore } from '~/stores/screenings';
import { useRoute, useRouter } from 'vue-router';

export default {
  name: 'SeatsSelectionPage',
  
  setup() {
    const screeningsStore = useScreeningsStore();
    const route = useRoute();
    const router = useRouter();
    
    return {
      screeningsStore,
      route,
      router
    };
  },
  
  data() {
    return {
      loading: true,
      error: null,
      screening: {
        movie_id: null,
        movie: {
          title: 'Cargando...',
        },
        date_time: null,
        price: 0,
        is_special: false,
        auditorium_id: null
      },
      selectedSeats: [],
      // Simulación de asientos ocupados (en una app real, esto vendría de la API)
      occupiedSeats: []
    };
  },
  
  computed: {
    screeningId() {
      return this.route.params.id;
    },
    
    auditoriumName() {
      return this.screening.auditorium
        ? this.screening.auditorium.name
        : `Sala ${this.screening.auditorium_id}`;
    },
    
    totalPrice() {
      return this.selectedSeats.length * (this.screening.price || 0);
    }
  },
  
  async mounted() {
    await this.loadScreeningData();
  },
  
  methods: {
    async loadScreeningData() {
      this.loading = true;
      this.error = null;
      
      try {
        // Obtener la información del screening desde la API
        const data = await this.screeningsStore.fetchScreeningById(this.screeningId);
        
        if (!data) {
          throw new Error('No se encontró la información de la proyección');
        }
        
        console.log('Screening cargado:', data);
        this.screening = data;
        
        // Cargar los asientos disponibles para esta proyección
        const seatsData = await this.screeningsStore.fetchAvailableSeats(this.screeningId);
        console.log('Asientos cargados:', seatsData);
        
        // Actualizar los asientos ocupados con datos reales
        if (seatsData && seatsData.seats_by_row) {
          // Mapear los asientos que no están disponibles
          this.occupiedSeats = [];
          
          Object.entries(seatsData.seats_by_row).forEach(([row, seats]) => {
            seats.forEach(seat => {
              if (seat.status !== 'available') {
                this.occupiedSeats.push({
                  row: row,
                  number: parseInt(seat.column)
                });
              }
            });
          });
          
          console.log('Asientos ocupados:', this.occupiedSeats);
        }
      } catch (error) {
        console.error('Error al cargar la proyección:', error);
        this.error = error.message || 'No se pudo cargar la información de la proyección';
      } finally {
        this.loading = false;
      }
    },
    
    isOccupied(row, number) {
      return this.occupiedSeats.some(seat => seat.row === row && seat.number === number);
    },
    
    isSelected(row, number) {
      return this.selectedSeats.some(seat => seat.row === row && seat.number === number);
    },
    
    toggleSeat(row, number) {
      // No permitir seleccionar asientos ocupados
      if (this.isOccupied(row, number)) {
        return;
      }
      
      const seatIndex = this.selectedSeats.findIndex(
        seat => seat.row === row && seat.number === number
      );
      
      if (seatIndex === -1) {
        // Añadir asiento
        this.selectedSeats.push({ row, number });
      } else {
        // Quitar asiento
        this.selectedSeats.splice(seatIndex, 1);
      }
    },
    
    formatScreeningDate(dateObj) {
      if (!dateObj) return 'Fecha no disponible';
      
      try {
        const date = new Date(dateObj);
        const weekdays = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const day = weekdays[date.getDay()];
        
        // Formateamos la fecha con el día de la semana destacado
        return `${day} ${date.getDate()} de ${date.toLocaleString('es-ES', {month: 'long'})} - ${date.toLocaleString('es-ES', {hour: '2-digit', minute: '2-digit'})}`;
      } catch (e) {
        console.error('Error al formatear fecha:', e);
        return String(dateObj);
      }
    },
    
    formatPrice(price) {
      if (price === undefined || price === null) return '0,00 €';
      return Number(price).toLocaleString('es-ES', {
        style: 'currency',
        currency: 'EUR'
      });
    },
    
    proceedToCheckout() {
      // Simulación: en una app real, aquí navegarías a la página de pago
      // y guardarías la selección en el store o sessionStorage
      alert(`Selección confirmada: ${this.selectedSeats.map(s => `${s.row}${s.number}`).join(', ')}`);
      
      // Ejemplo de navegación:
      // this.router.push({
      //   path: '/checkout',
      //   query: { 
      //     screening_id: this.screening.id,
      //     seats: this.selectedSeats.map(s => `${s.row}${s.number}`).join(',')
      //   }
      // });
    }
  }
};
</script>

<style scoped>
/* Estilos para la selección de asientos */
.screen-container {
  position: relative;
  margin-bottom: 30px;
}

.screen {
  width: 100%;
  max-width: 400px;
  height: 20px;
  background: #d1d1d1;
  margin: 0 auto;
  border-radius: 50%;
  padding: 10px;
  text-align: center;
  font-size: 12px;
  line-height: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  position: relative;
}

.screen:before {
  content: '';
  position: absolute;
  width: 80%;
  height: 100px;
  background: rgba(173, 216, 230, 0.1);
  bottom: -85px;
  border-radius: 50%;
  left: 10%;
  z-index: -1;
}

.seat-row {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 10px;
}

.row-label {
  width: 25px;
  text-align: center;
  font-weight: bold;
  color: #777;
}

.seat {
  width: 35px;
  height: 35px;
  margin: 3px;
  background-color: #e6e6e6;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.8em;
  transition: all 0.2s ease;
  border: 1px solid #ccc;
}

.seat:hover:not(.seat-occupied) {
  background-color: #cce5ff;
  transform: scale(1.05);
}

.seat-occupied {
  background-color: #f8d7da;
  cursor: not-allowed;
  color: #721c24;
}

.seat-selected {
  background-color: #007bff;
  color: white;
  border-color: #0056b3;
}

.seat-sample {
  width: 20px;
  height: 20px;
  margin-right: 5px;
  border-radius: 3px;
  background-color: #e6e6e6;
  border: 1px solid #ccc;
}

.seat-legend {
  margin-top: 30px;
  font-size: 0.9em;
  color: #6c757d;
}

.selection-summary {
  background-color: #f8f9fa;
  border-radius: 5px;
}

.price-tag {
  background-color: #f8f9fa;
  border-left: 3px solid #007bff;
}
</style>