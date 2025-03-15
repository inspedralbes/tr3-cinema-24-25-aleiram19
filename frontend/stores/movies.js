import { defineStore } from 'pinia';
import { useRuntimeConfig } from 'nuxt/app';

export const useMoviesStore = defineStore('movies', {
  state: () => ({
    movies: [],
    loading: false,
    error: null,
    currentMovie: null
  }),
  
  getters: {
    getMovieById: (state) => (id) => {
      return state.movies.find(movie => movie.id === parseInt(id));
    },
    
    getMoviesByGenre: (state) => (genreId) => {
      if (!genreId) return state.movies;
      return state.movies.filter(movie => movie.movie_genre_id === parseInt(genreId));
    },
    
    getCurrentMovies: (state) => {
      const now = new Date();
      return state.movies.filter(movie => {
        const releaseDate = new Date(movie.release_date);
        return releaseDate <= now;
      });
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
    
    async fetchMovies() {
      this.loading = true;
      this.error = null;
      
      try {
        this.movies = await this.apiCall('GET', 'movie');
      } catch (error) {
        console.error('Error fetching movies:', error);
        this.error = error.message || 'Error al cargar las películas';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCurrentMovies() {
      this.loading = true;
      this.error = null;
      
      try {
        this.movies = await this.apiCall('GET', 'movie/current');
      } catch (error) {
        console.error('Error fetching current movies:', error);
        this.error = error.message || 'Error al cargar las películas en cartelera';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchMovieById(id) {
      if (!id) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const movie = await this.apiCall('GET', `movie/${id}`);
        this.currentMovie = movie;
        return movie;
      } catch (error) {
        console.error(`Error fetching movie with id ${id}:`, error);
        this.error = error.message || 'Error al cargar la película';
      } finally {
        this.loading = false;
      }
    },
    
    async createMovie(movieData) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('POST', 'movie', movieData);
      } catch (error) {
        console.error('Error creating movie:', error);
        this.error = error.message || 'Error al crear la película';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateMovie(id, movieData) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('PUT', `movie/${id}`, movieData);
      } catch (error) {
        console.error(`Error updating movie with id ${id}:`, error);
        this.error = error.message || 'Error al actualizar la película';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteMovie(id) {
      this.loading = true;
      this.error = null;
      
      try {
        return await this.apiCall('DELETE', `movie/${id}`);
      } catch (error) {
        console.error(`Error deleting movie with id ${id}:`, error);
        this.error = error.message || 'Error al eliminar la película';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    setCurrentMovie(movie) {
      this.currentMovie = movie;
    }
  }
});