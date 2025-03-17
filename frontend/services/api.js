import { useRuntimeConfig } from 'nuxt/app';

/**
 * Servicio centralizado para hacer peticiones HTTP
 */
class ApiService {
  constructor() {
    this.config = useRuntimeConfig();
    this.baseUrl = this.config.public.apiBaseUrl;
  }
  
  /**
   * Método para realizar una petición GET
   * @param {string} endpoint - Endpoint de la API sin la base URL
   * @param {Object} options - Opciones adicionales para fetch
   * @returns {Promise<any>} Respuesta de la API en formato JSON
   */
  async get(endpoint, options = {}) {
    return this.request(endpoint, { 
      method: 'GET',
      ...options 
    });
  }
  
  /**
   * Método para realizar una petición POST
   * @param {string} endpoint - Endpoint de la API sin la base URL
   * @param {Object} data - Datos a enviar en el cuerpo de la petición
   * @param {Object} options - Opciones adicionales para fetch
   * @returns {Promise<any>} Respuesta de la API en formato JSON
   */
  async post(endpoint, data, options = {}) {
    return this.request(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data),
      ...options
    });
  }
  
  /**
   * Método para realizar una petición PUT
   * @param {string} endpoint - Endpoint de la API sin la base URL
   * @param {Object} data - Datos a enviar en el cuerpo de la petición
   * @param {Object} options - Opciones adicionales para fetch
   * @returns {Promise<any>} Respuesta de la API en formato JSON
   */
  async put(endpoint, data, options = {}) {
    return this.request(endpoint, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data),
      ...options
    });
  }
  
  /**
   * Método para realizar una petición DELETE
   * @param {string} endpoint - Endpoint de la API sin la base URL
   * @param {Object} options - Opciones adicionales para fetch
   * @returns {Promise<any>} Respuesta de la API en formato JSON
   */
  async delete(endpoint, options = {}) {
    return this.request(endpoint, {
      method: 'DELETE',
      ...options
    });
  }
  
  /**
   * Método base para realizar cualquier petición HTTP
   * @param {string} endpoint - Endpoint de la API sin la base URL
   * @param {Object} options - Opciones para fetch
   * @returns {Promise<any>} Respuesta de la API en formato JSON
   */
  async request(endpoint, options = {}) {
    try {
      const url = `${this.baseUrl}/${endpoint.replace(/^\//, '')}`;
      
      // Agregar token de autenticación si existe
      const token = localStorage.getItem('token');
      if (token) {
        options.headers = {
          ...options.headers,
          'Authorization': `Bearer ${token}`
        };
      }
      
      const response = await fetch(url, options);
      
      // Manejar respuestas no exitosas
      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(
          errorData.message || 
          `Error en la petición: ${response.status} ${response.statusText}`
        );
      }
      
      // Parsear la respuesta como JSON
      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error en la petición API:', error);
      throw error;
    }
  }
}

// Crear una instancia del servicio para exportar
const apiService = () => new ApiService();

export default apiService;
