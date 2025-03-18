import { defineStore } from 'pinia';
import { useRuntimeConfig } from '#app';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    getUser: (state) => state.user,
    isAdmin: (state) => state.user && state.user.role === 'admin',
    isLoggedIn: (state) => state.isAuthenticated,
  },

  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;
      
      try {
        const config = useRuntimeConfig();
        const apiBaseUrl = config.public.apiBaseUrl;
        const response = await fetch(`${apiBaseUrl}/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(credentials),
        });

        const data = await response.json();
        
        if (!response.ok) {
          throw new Error(data.message || 'Error de autenticación');
        }
        
        this.token = data.token;
        localStorage.setItem('token', data.token);
        
        // Obtener los datos del usuario
        await this.fetchUser();
        
        this.isAuthenticated = true;
        return true;
      } catch (error) {
        this.error = error.message;
        console.error('Error de inicio de sesión:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },

    async register(userData) {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('Iniciando registro con datos:', userData);
        
        const config = useRuntimeConfig();
        const apiBaseUrl = config.public.apiBaseUrl;
        console.log('URL de la API:', `${apiBaseUrl}/register`);
        
        // Asegurar que last_name tenga un valor
        if (!userData.last_name) {
          userData.last_name = '';
        }
        
        const response = await fetch(`${apiBaseUrl}/register`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(userData),
          credentials: 'include',
        });

        console.log('Estado de la respuesta:', response.status);
        const data = await response.json();
        console.log('Datos de respuesta:', data);
        
        if (!response.ok) {
          if (response.status === 500) {
            throw new Error('Error en el servidor. Por favor, intenta más tarde.');
          }
          // Manejar errores de validación
          if (data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('\n');
            throw new Error(errorMessages || data.message || 'Error en el registro');
          } else {
            throw new Error(data.message || data.error || 'Error en el registro');
          }
        }
        
        this.token = data.token;
        localStorage.setItem('token', data.token);
        
        // Obtener los datos del usuario
        await this.fetchUser();
        
        this.isAuthenticated = true;
        return true;
      } catch (error) {
        this.error = error.message;
        console.error('Error de registro:', error);
        return false;
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      this.loading = true;
      
      try {
        const storedToken = this.token || localStorage.getItem('token');
        
        if (!storedToken) {
          throw new Error('No hay token disponible');
        }
        
        const config = useRuntimeConfig();
        const apiBaseUrl = config.public.apiBaseUrl;
        const response = await fetch(`${apiBaseUrl}/user`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${storedToken}`,
            'Accept': 'application/json',
          },
        });

        if (!response.ok) {
          throw new Error('Error al obtener datos del usuario');
        }
        
        const userData = await response.json();
        this.user = userData;
        this.isAuthenticated = true;
        return userData;
      } catch (error) {
        console.error('Error al obtener el usuario:', error);
        this.logout();
        return null;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      
      try {
        const storedToken = this.token || localStorage.getItem('token');
        
        if (storedToken) {
          const config = useRuntimeConfig();
          const apiBaseUrl = config.public.apiBaseUrl;
          await fetch(`${apiBaseUrl}/logout`, {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${storedToken}`,
              'Accept': 'application/json',
            },
          });
        }
      } catch (error) {
        console.error('Error durante el cierre de sesión:', error);
      } finally {
        // Limpiar el estado incluso si hay error en el logout del servidor
        this.user = null;
        this.token = null;
        this.isAuthenticated = false;
        localStorage.removeItem('token');
        this.loading = false;
      }
    },

    // Inicializar el estado de auth desde localStorage (llamar en app.vue o plugin)
    init() {
      const token = localStorage.getItem('token');
      if (token) {
        this.token = token;
        this.fetchUser();
      }
    }
  }
});
