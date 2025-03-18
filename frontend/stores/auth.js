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
        // URL base fija para la API
        const apiBaseUrl = 'http://localhost:8000/api';
        
        let response;
        try {
          response = await fetch(`${apiBaseUrl}/login`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
            body: JSON.stringify(credentials),
          });
        } catch (networkError) {
          console.error('Error de red:', networkError);
          throw new Error('Error de conexión con el servidor. Verifica tu conexión a internet.');
        }

        const data = await response.json();
        
        if (!response.ok) {
          if (response.status === 500) {
            console.error('Error del servidor 500:', data);
            throw new Error('Error en el servidor. Por favor, intenta más tarde.');
          }
          
          if (data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('\n');
            throw new Error(errorMessages || data.message || 'Error de autenticación');
          } else if (data.message) {
            throw new Error(data.message);
          } else {
            throw new Error('Error de autenticación. Verifica tus credenciales.');
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
        
        // URL base fija para la API
        const apiBaseUrl = 'http://localhost:8000/api';
        console.log('URL de la API:', `${apiBaseUrl}/register`);
        
        // Validar datos requeridos
        if (!userData.name || !userData.email || !userData.password || !userData.password_confirmation) {
          throw new Error('Todos los campos son obligatorios');
        }

        // Validar que las contraseñas coincidan
        if (userData.password !== userData.password_confirmation) {
          throw new Error('Las contraseñas no coinciden');
        }

        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(userData.email)) {
          throw new Error('El formato del email no es válido');
        }
        
        // Asegurar que last_name tenga un valor
        if (!userData.last_name) {
          userData.last_name = '';
        }

        // Preparar los datos a enviar al servidor
        const registerData = {
          name: userData.name,
          last_name: userData.last_name,
          email: userData.email,
          password: userData.password,
          password_confirmation: userData.password_confirmation
        };
        
        console.log('Datos a enviar:', registerData);
        
        let response;
        try {
          response = await fetch(`${apiBaseUrl}/register`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
            body: JSON.stringify(registerData)
          });
        } catch (networkError) {
          console.error('Error de red:', networkError);
          throw new Error('Error de conexión con el servidor. Verifica tu conexión a internet.');
        }
        
        console.log('Estado de la respuesta:', response.status);
        const data = await response.json();
        console.log('Datos de respuesta:', data);
        
        if (!response.ok) {
          if (response.status === 500) {
            console.error('Error del servidor 500:', data);
            throw new Error('Error en el servidor. Por favor, intenta más tarde.');
          }
          
          // Manejar errores de validación
          if (data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('\n');
            throw new Error(errorMessages || data.message || 'Error en el registro');
          } else if (data.message) {
            throw new Error(data.message);
          } else {
            throw new Error('Error en el registro. Intenta nuevamente.');
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
        
        const apiBaseUrl = 'http://localhost:8000/api';
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
          const apiBaseUrl = 'http://localhost:8000/api';
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
