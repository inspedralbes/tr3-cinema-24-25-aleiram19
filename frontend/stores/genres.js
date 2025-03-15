import { defineStore } from 'pinia';
import { useRuntimeConfig } from 'nuxt/app';

export const useGenresStore = defineStore('genres', {
  state: () => ({
    genres: [],
    loading: false,
    error: null,
    currentGenre: null
  }),
  
  getters: {
    getGenreById: (state) => (id) => {
      return state.genres.find(genre => genre.id === parseInt(id));
    },
    
    getGenreName: (state) => (id) => {
      const genre = state.genres.find(genre => genre.id === parseInt(id));
      return genre ? genre.name : 'Desconocido';
    },
    
    getAllGenreNames: (state) => {
      return state.genres.map(genre => genre.name);
    }
  },
  
  actions: {
    // Función base para realizar llamadas a la API
    async apiCall(method, endpoint, body = null) {
      const config = useRuntimeConfig();
      const baseUrl = config.public.apiBaseUrl;
      
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
        const response = await fetch(`${baseUrl}/${endpoint}`, options);
        
        if (!response.ok) {
          throw new Error(`Error API: ${response.status} ${response.statusText}`);
        }
        
        return await response.json();
      } catch (error) {
        console.error(`Error en petición ${method} a ${endpoint}:`, error);
        throw error;
      }
    },
    
    async fetchGenres() {
      this.loading = true;
      this.error = null;
      
      try {
        this.genres = await this.apiCall('GET', 'genre');
      } catch (error) {
        console.error('Error fetching genres:', error);
        this.error = error.message || 'Error al cargar los géneros';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchGenreById(id) {
      if (!id) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const genre = await this.apiCall('GET', `genre/${id}`);
        this.currentGenre = genre;
        return genre;
      } catch (error) {
        console.error(`Error fetching genre with id ${id}:`, error);
        this.error = error.message || 'Error al cargar el género';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchMoviesByGenre(id) {
      if (!id) return [];
      
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('GET', `genre/${id}/movies`);
      } catch (error) {
        console.error(`Error fetching movies for genre ${id}:`, error);
        this.error = error.message || 'Error al cargar las películas del género';
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async createGenre(genreData) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('POST', 'genre', genreData);
      } catch (error) {
        console.error('Error creating genre:', error);
        this.error = error.message || 'Error al crear el género';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateGenre(id, genreData) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('PUT', `genre/${id}`, genreData);
      } catch (error) {
        console.error(`Error updating genre with id ${id}:`, error);
        this.error = error.message || 'Error al actualizar el género';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteGenre(id) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('DELETE', `genre/${id}`);
      } catch (error) {
        console.error(`Error deleting genre with id ${id}:`, error);
        this.error = error.message || 'Error al eliminar el género';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    setCurrentGenre(genre) {
      this.currentGenre = genre;
    }
  }
});