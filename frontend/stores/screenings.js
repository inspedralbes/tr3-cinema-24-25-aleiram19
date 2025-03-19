import { defineStore } from 'pinia';
import { useRuntimeConfig } from 'nuxt/app';

export const useScreeningsStore = defineStore('screenings', {
  state: () => ({
    screenings: [],
    currentScreening: null,
    availableSeats: [],
    loading: false,
    error: null
  }),

  getters: {
    getScreenings: (state) => state.screenings,
    getCurrentScreening: (state) => state.currentScreening,
    getAvailableSeats: (state) => state.availableSeats,
    isLoading: (state) => state.loading
  },

  actions: {
    // Función base para realizar llamadas a la API directamente con URL
    async apiCall(method, endpoint, body = null) {
      const options = {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      };
      
      if (body) {
        options.body = JSON.stringify(body);
      }
      
      try {
        const response = await fetch(`http://localhost:8000/api/${endpoint}`, options);
        
        if (!response.ok) {
          throw new Error(`Error API: ${response.status} ${response.statusText}`);
        }
        
        return await response.json();
      } catch (error) {
        console.error(`Error en petición ${method} a ${endpoint}:`, error);
        throw error;
      }
    },
    
    async fetchAuditoriums() {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('GET', 'auditorium');
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener los auditorios:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },

    async fetchScreenings() {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('GET', 'screening');
        this.screenings = data;
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener las proyecciones:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchScreeningById(id) {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('GET', `screening/${id}`);
        this.currentScreening = data;
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener la proyección:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    async fetchAvailableSeats(screeningId) {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('GET', `screening/${screeningId}/seats`);
        this.availableSeats = data;
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener los asientos disponibles:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    // Solo para administradores
    async createScreening(screeningData) {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('POST', 'screening', screeningData);
        // Actualizar lista de proyecciones
        await this.fetchScreenings();
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al crear la proyección:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Solo para administradores
    async updateScreening(id, screeningData) {
      this.loading = true;
      this.error = null;
      
      try {
        const data = await this.apiCall('PUT', `screening/${id}`, screeningData);
        // Actualizar lista de proyecciones
        await this.fetchScreenings();
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al actualizar la proyección:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Solo para administradores
    async deleteScreening(id) {
      this.loading = true;
      this.error = null;
      
      try {
        await this.apiCall('DELETE', `screening/${id}`);
        // Actualizar lista de proyecciones
        await this.fetchScreenings();
        return true;
      } catch (error) {
        this.error = error.message;
        console.error('Error al eliminar la proyección:', error);
        return false;
      } finally {
        this.loading = false;
      }
    }
  }
});