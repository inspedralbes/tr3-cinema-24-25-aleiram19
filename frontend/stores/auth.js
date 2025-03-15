import { defineStore } from 'pinia';

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
        const response = await fetch(`${config.public.apiBaseUrl}/login`, {
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
        const config = useRuntimeConfig();
        const response = await fetch(`${config.public.apiBaseUrl}/register`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(userData),
        });

        const data = await response.json();
        
        if (!response.ok) {
          throw new Error(data.message || 'Error en el registro');
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
        const config = useRuntimeConfig();
        const storedToken = this.token || localStorage.getItem('token');
        
        if (!storedToken) {
          throw new Error('No hay token disponible');
        }
        
        const response = await fetch(`${config.public.apiBaseUrl}/user`, {
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
        const config = useRuntimeConfig();
        const storedToken = this.token || localStorage.getItem('token');
        
        if (storedToken) {
          await fetch(`${config.public.apiBaseUrl}/logout`, {
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
