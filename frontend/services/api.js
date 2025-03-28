// api.js - Servicio para comunicarse con la API del backend

// Obtener la URL base de la API del archivo de configuración
const API_URL = 'http://cinema.a23aleminram.daw.inspedralbes.cat/api/';

// Configuración común para las peticiones fetch
const defaultHeaders = {
  'Accept': 'application/json',
  'Content-Type': 'application/json'
};

// Función para manejar errores de fetch
const handleResponse = async (response) => {
  if (!response.ok) {
    // Intentar obtener mensaje de error de la respuesta JSON
    let errorMessage;
    try {
      const errorData = await response.json();
      errorMessage = errorData.message || `Error ${response.status}: ${response.statusText}`;
    } catch (e) {
      errorMessage = `Error ${response.status}: ${response.statusText}`;
    }
    throw new Error(errorMessage);
  }
  return response.json();
};

// Servicios para géneros
const genres = {
  // Obtener todos los géneros
  async getAll() {
    const response = await fetch(`${API_URL}genre`, {
      headers: defaultHeaders
    });
    return handleResponse(response);
  },
  
  // Obtener un género por ID
  async getById(id) {
    const response = await fetch(`${API_URL}genre/${id}`, {
      headers: defaultHeaders
    });
    return handleResponse(response);
  },
  
  // Crear un nuevo género
  async create(data) {
    const response = await fetch(`${API_URL}genre`, {
      method: 'POST',
      headers: defaultHeaders,
      body: JSON.stringify(data)
    });
    return handleResponse(response);
  },
  
  // Actualizar un género existente
  async update(id, data) {
    const response = await fetch(`${API_URL}genre/${id}`, {
      method: 'PUT', // o PATCH dependiendo de la API
      headers: defaultHeaders,
      body: JSON.stringify(data)
    });
    return handleResponse(response);
  },
  
  // Eliminar un género
  async delete(id) {
    const response = await fetch(`${API_URL}genre/${id}`, {
      method: 'DELETE',
      headers: defaultHeaders
    });
    return handleResponse(response);
  },
  
  // Obtener películas por género
  async getMovies(id) {
    const response = await fetch(`${API_URL}genre/${id}/movies`, {
      headers: defaultHeaders
    });
    return handleResponse(response);
  }
};

// Servicios para películas
const movies = {
  // Obtener todas las películas
  async getAll() {
    const response = await fetch(`${API_URL}movie`, {
      headers: defaultHeaders
    });
    return handleResponse(response);
  },
  
  // Obtener una película por ID
  async getById(id) {
    const response = await fetch(`${API_URL}movie/${id}`, {
      headers: defaultHeaders
    });
    return handleResponse(response);
  },
  
  // Crear una nueva película (con posible archivo de imagen)
  async create(formData) {
    // No incluimos Content-Type aquí ya que FormData lo establece automáticamente
    const response = await fetch(`${API_URL}movie`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json'
      },
      body: formData
    });
    return handleResponse(response);
  },
  
  // Actualizar una película existente (con posible archivo de imagen)
  async update(id, formData) {
    // No incluimos Content-Type aquí ya que FormData lo establece automáticamente
    const response = await fetch(`${API_URL}movie/${id}`, {
      method: 'POST', // Asumiendo que es POST con _method=PUT para Laravel
      headers: {
        'Accept': 'application/json'
      },
      body: formData
    });
    return handleResponse(response);
  },
  
  // Eliminar una película
  async delete(id) {
    const response = await fetch(`${API_URL}movie/${id}`, {
      method: 'DELETE',
      headers: defaultHeaders
    });
    return handleResponse(response);
  }
};

// Exportamos todos los servicios
const apiService = {
  genres,
  movies
};

export default apiService;
