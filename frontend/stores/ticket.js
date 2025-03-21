import { defineStore } from 'pinia';

export const useTicketStore = defineStore('ticket', {
  state: () => ({
    selectedScreening: null,
    selectedSeats: [],
    guestInfo: null,
    paymentInfo: null,
    loading: false,
    error: null
  }),

  getters: {
    getSelectedScreening: (state) => state.selectedScreening,
    getSelectedSeats: (state) => state.selectedSeats,
    getGuestInfo: (state) => state.guestInfo,
    getPaymentInfo: (state) => state.paymentInfo,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
    hasGuestInfo: (state) => state.guestInfo !== null,
    
    // Calcular el total a pagar basado en los asientos seleccionados
    getTotalAmount: (state) => {
      if (!state.selectedScreening || !state.selectedSeats.length) return 0;
      return state.selectedSeats.length * (state.selectedScreening.price || 100); // Precio por defecto 100 si no hay precio definido
    },
    
    // Verificar si se puede continuar con el siguiente paso
    canProceedToPayment: (state) => {
      return state.selectedScreening !== null && 
             state.selectedSeats.length > 0 && 
             state.guestInfo !== null;
    }
  },

  actions: {
    // Establecer la función seleccionada
    setSelectedScreening(screening) {
      this.selectedScreening = screening;
    },
    
    // Gestionar los asientos seleccionados
    selectSeat(seat) {
      const index = this.selectedSeats.findIndex(s => 
        s.row === seat.row && s.column === seat.column
      );
      
      if (index === -1) {
        this.selectedSeats.push(seat);
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
    
    // Establecer la información del invitado
    setGuestInfo(guestData) {
      this.guestInfo = guestData;
    },
    
    // Establecer la información de pago
    setPaymentInfo(paymentData) {
      this.paymentInfo = paymentData;
    },
    
    // Realizar el pago y completar la compra
    async completeGuestPurchase() {
      this.loading = true;
      this.error = null;
      
      try {
        if (!this.selectedScreening || this.selectedSeats.length === 0 || !this.guestInfo || !this.paymentInfo) {
          throw new Error('Información incompleta para realizar la compra');
        }
        
        const purchaseData = {
          screening_id: this.selectedScreening.id,
          seats: this.selectedSeats.map(seat => ({
            row: seat.row,
            column: seat.column
          })),
          guest: this.guestInfo,
          payment: this.paymentInfo
        };
        
        const response = await fetch(`http://localhost:8000/api/guest/tickets/purchase`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(purchaseData),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Error al completar la compra');
        }
        
        const data = await response.json();
        
        // Limpiar el estado después de una compra exitosa
        this.clearPurchaseData();
        
        return data;
      } catch (error) {
        this.error = error.message;
        console.error('Error al completar la compra:', error);
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    // Limpiar todos los datos de la compra
    clearPurchaseData() {
      this.selectedScreening = null;
      this.selectedSeats = [];
      this.guestInfo = null;
      this.paymentInfo = null;
      this.error = null;
    }
  }
});
