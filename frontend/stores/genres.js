// Importaciones necesarias para Vue (Pinia)
import { defineStore } from 'pinia';
import { useNuxtApp } from 'nuxt/app';

const API_URL = 'http://cinema.a23aleminram.daw.inspedralbes.cat/api/';

// Definición del store para géneros
export const useGenresStore = defineStore('genres', {
  // Estado del store
  state: () => ({
    genres: [],          // Lista de géneros
    loading: false,      // Indicador de carga
    error: null          // Mensaje de error si ocurre alguno
  }),

  // Getters
  getters: {
    // Obtener todos los géneros
    getAllGenres() {
      return this.genres;
    },
    
    // Obtener género por ID
    getGenreById: (state) => (id) => {
      return state.genres.find(genre => genre.id === id);
    }
  },

  // Acciones
  actions: {
    // Obtener todos los géneros
    async fetchGenres() {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch(`${API_URL}genre`, {
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        this.genres = data;
        
        // Mostrar un mensaje de éxito usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.success('Géneros cargados correctamente');
        }
        
        return data;
      } catch (error) {
        console.error('Error en fetchGenres:', error);
        this.error = error.message;
        
        // Mostrar un mensaje de error usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.error(`Error al cargar géneros: ${error.message}`);
        }
        
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    // Obtener películas por género
    async fetchMoviesByGenre(genreId) {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch(`${API_URL}genre/${genreId}/movies`, {
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        return data; // No se actualiza el estado local, se devuelve la respuesta
      } catch (error) {
        console.error('Error en fetchMoviesByGenre:', error);
        this.error = error.message;
        return [];
      } finally {
        this.loading = false;
      }
    }
  }
});
