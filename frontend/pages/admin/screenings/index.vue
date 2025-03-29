<template>
  <div class="admin-screenings-page">
    <h1 class="mb-4">Administración de Sesiones</h1>
    
    <div class="d-flex justify-content-between mb-4">
      <div>
        <button class="btn btn-success" @click="openCreateModal">
          <i class="bi bi-plus-circle me-2"></i> Nueva Sesión
        </button>
      </div>
      <div>
        <select v-model="filterStatus" class="form-select">
          <option value="all">Todas las sesiones</option>
          <option value="active">Solo activas</option>
          <option value="inactive">Solo inactivas</option>
        </select>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
    
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    
    <div v-else-if="screenings.length === 0" class="alert alert-info">
      No hay sesiones disponibles.
    </div>
    
    <div v-else class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Película</th>
            <th>Sala</th>
            <th>Fecha y Hora</th>
            <th>Precio Base</th>
            <th>Día Especial</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="screening in filteredScreenings" :key="screening.id" :class="{'table-danger': !screening.active}">
            <td>{{ screening.id }}</td>
            <td>{{ screening.movie?.title || 'Sin película' }}</td>
            <td>{{ screening.auditorium?.name || 'Sin sala' }}</td>
            <td>{{ formatDateTime(screening.date_time) }}</td>
            <td>{{ screening.price }}€</td>
            <td>
              <span v-if="screening.is_special" class="badge bg-success">Sí</span>
              <span v-else class="badge bg-secondary">No</span>
            </td>
            <td>
              <span v-if="screening.active" class="badge bg-success">Activa</span>
              <span v-else class="badge bg-danger">Inactiva</span>
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <button 
                  class="btn" 
                  :class="screening.active ? 'btn-danger' : 'btn-success'"
                  @click="toggleScreeningStatus(screening)"
                  :title="screening.active ? 'Desactivar sesión' : 'Activar sesión'"
                >
                  <i class="bi" :class="screening.active ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                </button>
                <button class="btn btn-primary" @click="openEditModal(screening)" title="Editar sesión">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger" @click="confirmDelete(screening)" title="Eliminar sesión">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal de creación/edición -->
    <div class="modal fade" id="screeningModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Editar Sesión' : 'Nueva Sesión' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditing ? updateScreening() : createScreening()">
              <div class="mb-3">
                <label for="movieId" class="form-label">Película</label>
                <select id="movieId" v-model="form.movie_id" class="form-select" required>
                  <option value="">Seleccionar película</option>
                  <option v-for="movie in movies" :key="movie.id" :value="movie.id">
                    {{ movie.title }}
                  </option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="auditoriumId" class="form-label">Sala</label>
                <select id="auditoriumId" v-model="form.auditorium_id" class="form-select" required>
                  <option value="">Seleccionar sala</option>
                  <option v-for="auditorium in auditoriums" :key="auditorium.id" :value="auditorium.id">
                    {{ auditorium.name }}
                  </option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="date" class="form-label">Fecha</label>
                <input type="date" id="date" v-model="form.date" class="form-control" required>
              </div>
              
              <div class="mb-3">
                <label for="time" class="form-label">Hora</label>
                <select id="time" v-model="form.time" class="form-select" required>
                  <option value="16:00">16:00</option>
                  <option value="18:00">18:00</option>
                  <option value="20:00">20:00</option>
                </select>
              </div>
              
              <div class="mb-3 form-check">
                <input type="checkbox" id="isSpecial" v-model="form.is_special" class="form-check-input">
                <label for="isSpecial" class="form-check-label">Día especial (precio reducido)</label>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" :disabled="formSubmitting">
                  <span v-if="formSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  {{ isEditing ? 'Actualizar' : 'Crear' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>¿Está seguro de que desea eliminar esta sesión?</p>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" @click="deleteScreening" :disabled="formSubmitting">
              <span v-if="formSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminScreeningsPage',
  
  layout: 'admin', // Asumiendo que tienes un layout para admin
  
  middleware: ['auth', 'admin'], // Middleware para verificar que el usuario esté autenticado y sea admin
  
  data() {
    return {
      screenings: [],
      movies: [],
      auditoriums: [],
      loading: true,
      error: null,
      formSubmitting: false,
      isEditing: false,
      selectedScreening: null,
      modal: null,
      deleteModal: null,
      filterStatus: 'all',
      
      form: {
        movie_id: '',
        auditorium_id: '',
        date: '',
        time: '16:00',
        is_special: false
      }
    };
  },
  
  computed: {
    filteredScreenings() {
      if (this.filterStatus === 'all') {
        return this.screenings;
      } else if (this.filterStatus === 'active') {
        return this.screenings.filter(s => s.active);
      } else {
        return this.screenings.filter(s => !s.active);
      }
    }
  },
  
  async mounted() {
    // Inicializar modales de Bootstrap
    this.modal = new bootstrap.Modal(document.getElementById('screeningModal'));
    this.deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Cargar datos
    this.loadData();
  },
  
  methods: {
    async loadData() {
      try {
        this.loading = true;
        this.error = null;
        
        // Cargar sesiones
        const screeningsResponse = await this.$api.get('/screening');
        this.screenings = screeningsResponse.data;
        
        // Cargar películas para el selector
        const moviesResponse = await this.$api.get('/movie');
        this.movies = moviesResponse.data;
        
        // Cargar auditorios para el selector
        const auditoriumsResponse = await this.$api.get('/auditorium');
        this.auditoriums = auditoriumsResponse.data;
      } catch (error) {
        console.error('Error cargando datos:', error);
        this.error = 'Error al cargar los datos. Por favor, inténtalo de nuevo.';
      } finally {
        this.loading = false;
      }
    },
    
    formatDateTime(dateTime) {
      if (!dateTime) return 'Fecha desconocida';
      
      const date = new Date(dateTime);
      return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(date);
    },
    
    resetForm() {
      this.form = {
        movie_id: '',
        auditorium_id: '',
        date: '',
        time: '16:00',
        is_special: false
      };
    },
    
    openCreateModal() {
      this.isEditing = false;
      this.resetForm();
      // Establecer fecha por defecto (mañana)
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      this.form.date = tomorrow.toISOString().split('T')[0];
      this.modal.show();
    },
    
    openEditModal(screening) {
      this.isEditing = true;
      this.selectedScreening = screening;
      
      // Parsear fecha y hora
      const dateTime = new Date(screening.date_time);
      
      this.form = {
        movie_id: screening.movie_id,
        auditorium_id: screening.auditorium_id,
        date: dateTime.toISOString().split('T')[0],
        time: dateTime.toTimeString().slice(0, 5),
        is_special: screening.is_special
      };
      
      this.modal.show();
    },
    
    async createScreening() {
      try {
        this.formSubmitting = true;
        
        const response = await this.$api.post('/screening', this.form);
        
        // Éxito: cerrar modal, recargar datos y mostrar notificación
        this.modal.hide();
        await this.loadData();
        this.$toast.success('Sesión creada correctamente');
      } catch (error) {
        console.error('Error creando sesión:', error);
        const errorMessage = error.response?.data?.message || 'Error al crear la sesión';
        this.$toast.error(errorMessage);
      } finally {
        this.formSubmitting = false;
      }
    },
    
    async updateScreening() {
      try {
        this.formSubmitting = true;
        
        const response = await this.$api.put(`/screening/${this.selectedScreening.id}`, this.form);
        
        // Éxito: cerrar modal, recargar datos y mostrar notificación
        this.modal.hide();
        await this.loadData();
        this.$toast.success('Sesión actualizada correctamente');
      } catch (error) {
        console.error('Error actualizando sesión:', error);
        const errorMessage = error.response?.data?.message || 'Error al actualizar la sesión';
        this.$toast.error(errorMessage);
      } finally {
        this.formSubmitting = false;
      }
    },
    
    confirmDelete(screening) {
      this.selectedScreening = screening;
      this.deleteModal.show();
    },
    
    async deleteScreening() {
      try {
        this.formSubmitting = true;
        
        const response = await this.$api.delete(`/screening/${this.selectedScreening.id}`);
        
        // Éxito: cerrar modal, recargar datos y mostrar notificación
        this.deleteModal.hide();
        await this.loadData();
        this.$toast.success('Sesión eliminada correctamente');
      } catch (error) {
        console.error('Error eliminando sesión:', error);
        const errorMessage = error.response?.data?.message || 'Error al eliminar la sesión';
        this.$toast.error(errorMessage);
      } finally {
        this.formSubmitting = false;
      }
    },
    
    async toggleScreeningStatus(screening) {
      try {
        const response = await this.$api.put(`/screening/${screening.id}/toggle-active`);
        
        // Actualizar la sesión localmente
        screening.active = !screening.active;
        
        const statusText = screening.active ? 'activada' : 'desactivada';
        this.$toast.success(`Sesión ${statusText} correctamente`);
      } catch (error) {
        console.error('Error cambiando estado de la sesión:', error);
        this.$toast.error('Error al cambiar el estado de la sesión');
      }
    }
  },
  
  head() {
    return {
      title: 'Administración de Sesiones - Cinema'
    };
  }
};
</script>

<style scoped>
.admin-screenings-page {
  padding: 20px;
}
</style>