import { defineStore } from 'pinia';

const API_URL = 'cinema.a23aleminram.daw.inspedralbes.cat/api';

// Definición del store para asientos
export const useSeatsStore = defineStore('seats', {
  // Estado del store
  state: () => ({
    seats: {},            // Asientos organizados por filas
    loading: false,       // Indicador de carga
    error: null,          // Mensaje de error si ocurre alguno
    currentScreeningId: null, // ID de la proyección actual
    auditorium: null,     // Información del auditorio
    screening: null       // Información de la proyección
  }),

  // Getters
  getters: {
    // Obtener los asientos ordenados por fila
    seatsByRow() {
      return this.seats;
    },
    
    // Contabilizar los asientos disponibles
    availableSeatsCount() {
      let count = 0;
      
      Object.values(this.seats).forEach(row => {
        row.forEach(seat => {
          if (seat.status === 'available') {
            count++;
          }
        });
      });
      
      return count;
    },
    
    // Obtener asientos VIP
    vipSeats() {
      const vipRow = this.seats['F'] || [];
      return vipRow.filter(seat => seat.is_vip);
    },
    
    // Verificar si está cargando
    isLoading() {
      return this.loading;
    }
  },

  // Acciones
  actions: {
    // Obtener los asientos para una proyección específica
    async fetchSeatsForScreening(screeningId) {
      try {
        this.loading = true;
        this.error = null;
        this.currentScreeningId = screeningId;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch(`${API_URL}/screening/${screeningId}/seats`, {
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        
        this.seats = data.seats_by_row;
        this.auditorium = data.auditorium;
        this.screening = data.screening;
        
        // Verificar la estructura del auditorio
        if (this.auditorium) {
          if (this.auditorium.number === undefined) {
            // Si no tiene número pero tiene ID, podríamos usar el ID como número
            if (this.auditorium.id && !this.auditorium.number) {
              this.auditorium.number = this.auditorium.id;
            }
          }
        }
        return data;
      } catch (error) {
        console.error('Error en fetchSeatsForScreening:', error);
        this.error = error.message;
        return { seats_by_row: {}, auditorium: null, screening: null };
      } finally {
        this.loading = false;
      }
    },
    
    // Actualizar el estado de un asiento
    async updateSeatStatus(seatId, status) {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch(`${API_URL}/seats/${seatId}/status`, {
          method: 'PUT',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ status })
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Actualizar el asiento en el estado local
        if (this.currentScreeningId) {
          await this.fetchSeatsForScreening(this.currentScreeningId);
        }
        
        return data;
      } catch (error) {
        console.error('Error en updateSeatStatus:', error);
        this.error = error.message;
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Limpiar los datos del store
    clearSeats() {
      this.seats = {};
      this.currentScreeningId = null;
      this.auditorium = null;
      this.screening = null;
      this.error = null;
    }
  }
});
