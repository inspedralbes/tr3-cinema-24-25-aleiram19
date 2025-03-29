import { defineStore } from 'pinia';
import { useAuthStore } from './auth';

const API_URL = 'http://localhost:8000/api/';

export const useTicketsStore = defineStore('tickets', {
  state: () => ({
    userTickets: [],
    currentTicket: null,
    selectedSeats: [],
    canBuyTickets: true,
    loading: false,
    error: null
  }),

  getters: {
    getUserTickets: (state) => state.userTickets,
    getCurrentTicket: (state) => state.currentTicket,
    getSelectedSeats: (state) => state.selectedSeats,
    getCanBuyTickets: (state) => state.canBuyTickets,
    isLoading: (state) => state.loading
  },

  actions: {
    async fetchUserTickets() {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          throw new Error('Autenticación requerida');
        }
        
        const response = await fetch(`${API_URL}tickets`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          // Solo usamos mode: 'cors' sin credentials para evitar problemas
          mode: 'cors',
        });

        if (!response.ok) {
          throw new Error('Error al obtener los tickets');
        }
        
        const data = await response.json();
        this.userTickets = data;
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener los tickets:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchTicketDetails(id) {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          throw new Error('Autenticación requerida');
        }
        
        const response = await fetch(`${API_URL}tickets/${id}`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          // Solo usamos mode: 'cors' sin credentials para evitar problemas
          mode: 'cors',
        });

        if (!response.ok) {
          throw new Error('Error al obtener los detalles del ticket');
        }
        
        const data = await response.json();
        this.currentTicket = data;
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al obtener los detalles del ticket:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    async checkCanBuyTickets(screeningId) {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          // Si no hay token, asumimos que no puede comprar
          this.canBuyTickets = false;
          return false;
        }
        
        const response = await fetch(`${API_URL}tickets/screening/${screeningId}/can-buy`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          // Solo usamos mode: 'cors' sin credentials para evitar problemas
          mode: 'cors',
        });

        if (!response.ok) {
          throw new Error('Error al verificar disponibilidad de compra');
        }
        
        const data = await response.json();
        this.canBuyTickets = data.canBuy;
        return data.canBuy;
      } catch (error) {
        this.error = error.message;
        console.error('Error al verificar disponibilidad de compra:', error);
        this.canBuyTickets = false;
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    // Gestionar los asientos seleccionados localmente
    selectSeat(seat) {
      const index = this.selectedSeats.findIndex(s => 
        s.row === seat.row && s.column === seat.column
      );
      
      if (index === -1) {
        // Verificar que no se exceda el límite de 10 asientos
        if (this.selectedSeats.length < 10) {
          this.selectedSeats.push(seat);
        } else {
          console.warn('No se pueden seleccionar más de 10 asientos por sesión');
          // Aquí podríamos lanzar un error o utilizar una notificación
        }
      }
    },
    
    unselectSeat(seat) {
      this.selectedSeats = this.selectedSeats.filter(s => 
        !(s.row === seat.row && s.column === seat.column)
      );
    },
    
    clearSelectedSeats() {
      this.selectedSeats = [];
    },
    
    // Reservar asientos seleccionados
    async reserveSeats(screeningId) {
      this.loading = true;
      this.error = null;
      
      try {
        if (this.selectedSeats.length === 0) {
          throw new Error('No hay asientos seleccionados');
        }
        
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          throw new Error('Autenticación requerida');
        }
        
        const seatsData = {
          screening_id: screeningId,
          seats: this.selectedSeats.map(seat => ({
            row: seat.row,
            column: seat.column
          }))
        };
        
        const response = await fetch(`${API_URL}tickets/reserve`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          // Solo usamos mode: 'cors' sin credentials para evitar problemas
          mode: 'cors',
          body: JSON.stringify(seatsData),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Error al reservar los asientos');
        }
        
        const data = await response.json();
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al reservar los asientos:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Confirmar compra de tickets
    async confirmTickets(reservationData) {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          throw new Error('Autenticación requerida');
        }

        // Usar el mismo enfoque que las otras funciones
        const requestBody = {
          screening_id: reservationData.screening_id,
          seats: reservationData.seats,
          quantity: reservationData.quantity || 1,
          total_pay: reservationData.total_pay || this.selectedSeats.length * 100 // Usar el valor proporcionado o calcular un valor por defecto
        };
                
        const response = await fetch(`${API_URL}tickets/purchase`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          mode: 'cors',
          body: JSON.stringify(requestBody),
        });

        if (!response.ok) {
          const errorData = await response.json();
          console.error('Respuesta de error del servidor:', errorData);
          throw new Error(errorData.message || 'Error al confirmar los tickets');
        }
        
        const data = await response.json();
        
        // Verificar si se enviaron todos los correos electrónicos
        if (data.all_emails_sent === false) {
          // Mostrar alerta al usuario con más detalles
          const warningMessage = data.warning || 'No se pudo enviar el correo con el código QR.';
          console.warn('Advertencia: ' + warningMessage);
        } else {
          // Confirmar que los correos se enviaron correctamente
          console.log('Todos los correos se enviaron correctamente');
        }
        
        // Actualizar la lista de tickets del usuario
        await this.fetchUserTickets();
        // Limpiar los asientos seleccionados
        this.clearSelectedSeats();
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al confirmar los tickets:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Cancelar tickets
    async cancelTickets(ticketIds) {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        const token = authStore.token;
        
        if (!token) {
          throw new Error('Autenticación requerida');
        }
        
        const response = await fetch(`${API_URL}tickets/cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          // Solo usamos mode: 'cors' sin credentials para evitar problemas
          mode: 'cors',
          body: JSON.stringify({ ticket_ids: ticketIds }),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Error al cancelar los tickets');
        }
        
        // Actualizar la lista de tickets del usuario
        await this.fetchUserTickets();
        return true;
      } catch (error) {
        this.error = error.message;
        console.error('Error al cancelar los tickets:', error);
        return false;
      } finally {
        this.loading = false;
      }
    }
  }
});
