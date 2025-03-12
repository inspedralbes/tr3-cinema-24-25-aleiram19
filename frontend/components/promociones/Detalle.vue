<template>
  <section class="promociones-section py-20">
    <div class="container mx-auto px-4">
      <div class="section-header text-center mb-14">
        <h1 class="section-title">PROMOCIONES Y DESCUENTOS</h1>
        <div class="divider mx-auto"></div>
        <p class="text-gray-300 mt-4">Aprovecha las mejores ofertas y descuentos en CineXeperience</p>
      </div>
      
      <!-- Filtros por categoría -->
      <div class="filtros mb-10">
        <div class="flex flex-wrap justify-center gap-4">
          <button 
            v-for="categoria in categorias" 
            :key="categoria"
            @click="filtrarPorCategoria(categoria)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all',
              filtroCategoria === categoria 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
            ]"
          >
            {{ categoria }}
          </button>
        </div>
      </div>
      
      <!-- Lista de promociones con detalles completos -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div 
          v-for="promo in promocionesFiltradas" 
          :key="promo.id"
          class="promo-card"
        >
          <div class="flex flex-col md:flex-row overflow-hidden h-full">
            <div class="promo-image md:w-2/5">
              <!-- Reemplazamos las imágenes con un gradiente -->
              <div class="w-full h-full min-h-[200px] bg-gradient-to-r from-blue-800 to-blue-500 flex items-center justify-center">
                <span class="text-white font-bold">{{ promo.titulo }}</span>
              </div>
            </div>
            <div class="promo-content md:w-3/5 p-6 flex flex-col">
              <div class="categoria-badge mb-2">{{ promo.categoria }}</div>
              <h3 class="promo-titulo text-2xl font-bold text-white mb-3">{{ promo.titulo }}</h3>
              <p class="promo-descripcion text-gray-300 mb-4 flex-grow">{{ promo.descripcion }}</p>
              
              <div class="promo-details mt-auto">
                <div class="flex items-center mb-2">
                  <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                  <span class="text-white">{{ promo.validez }}</span>
                </div>
                
                <div v-if="promo.condiciones" class="mb-4">
                  <div class="text-gray-400 font-medium mb-1">Condiciones:</div>
                  <ul class="text-gray-300 text-sm ml-5">
                    <li v-for="(condicion, index) in promo.condiciones" :key="index" class="mb-1">
                      {{ condicion }}
                    </li>
                  </ul>
                </div>
                
                <div class="flex items-center justify-between mt-4">
                  <div class="text-blue-500 font-bold text-lg">
                    <span v-if="promo.descuento">{{ promo.descuento }}</span>
                    <span v-else-if="promo.precio">{{ promo.precio }}</span>
                  </div>
                  <button class="btn-promo">
                    {{ promo.botonTexto }}
                    <i class="fas fa-chevron-right ml-2"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Sección de suscripción a newsletter para promociones -->
      <div class="newsletter-section mt-20 p-8 rounded-lg">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-white mb-2">Recibe nuestras promociones</h2>
          <p class="text-gray-300">Suscríbete y recibe todas nuestras promociones exclusivas directamente en tu correo.</p>
        </div>
        
        <form @submit.prevent="suscribirse" class="flex flex-col md:flex-row gap-4 max-w-2xl mx-auto">
          <input 
            v-model="email" 
            type="email" 
            placeholder="Tu correo electrónico" 
            class="flex-grow p-3 rounded-lg bg-gray-800 text-white border border-gray-700 focus:border-blue-500 focus:outline-none"
            required
          >
          <button 
            type="submit" 
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-colors"
          >
            Suscribirse
          </button>
        </form>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "Detalle",
  data() {
    return {
      filtroCategoria: 'Todas',
      email: '',
      categorias: ['Todas', 'Entradas', 'Snacks', 'Cumpleaños', 'Estudiantes', 'Grupos'],
      promociones: [
        {
          id: 1,
          titulo: 'Palomitas gratis en tu cumpleaños',
          categoria: 'Cumpleaños',
          descripcion: 'Celebra tu cumpleaños con nosotros y recibe unas palomitas grandes completamente gratis. Válido presentando tu identificación el día de tu cumpleaños o dentro de la semana de tu cumpleaños.',
          validez: 'Válido durante todo el año',
          condiciones: [
            'Debes presentar tu identificación oficial',
            'Válido solo el día de tu cumpleaños o hasta 7 días después',
            'No acumulable con otras promociones',
            'Un combo de palomitas grande por persona'
          ],
          botonTexto: 'Registrarme'
        },
        {
          id: 2,
          titulo: 'Matinés a mitad de precio',
          categoria: 'Entradas',
          descripcion: 'Disfruta de todas las películas en horarios antes de las 15:00h con un 50% de descuento en tu entrada. Una excelente manera de disfrutar los mejores estrenos a un precio increíble.',
          descuento: '50% de descuento',
          validez: 'Oferta permanente - Lunes a viernes',
          condiciones: [
            'Solo válido para funciones antes de las 15:00h',
            'Aplica de lunes a viernes (excepto festivos)',
            'No acumulable con otras promociones',
            'Descuento aplicable sobre el precio regular'
          ],
          botonTexto: 'Ver horarios'
        },
        {
          id: 3,
          titulo: 'Día del espectador',
          categoria: 'Entradas',
          descripcion: 'Todos los miércoles las entradas tienen un 30% de descuento. Aprovecha este día para disfrutar de los mejores estrenos a un precio especial en cualquiera de nuestras salas.',
          descuento: '30% de descuento',
          validez: 'Todos los miércoles del año',
          condiciones: [
            'Válido únicamente los miércoles (excepto festivos)',
            'Aplica para todas las películas y horarios',
            'No acumulable con otras promociones',
            'Sujeto a disponibilidad de asientos'
          ],
          botonTexto: 'Reservar entradas'
        },
        {
          id: 4,
          titulo: 'Tarjeta Familia',
          categoria: 'Grupos',
          descripcion: 'Obtén un 20% de descuento en todas las entradas familiares con nuestra tarjeta especial. Ideal para familias que disfrutan regularmente del cine y quieren ahorrar en cada visita.',
          precio: '€29.99/año',
          validez: 'Suscripción anual',
          condiciones: [
            'Válida para un máximo de 5 miembros de la familia',
            'Descuento del 20% en todas las entradas',
            'Incluye descuentos en combos familiares',
            'Acumulable con algunas promociones especiales'
          ],
          botonTexto: 'Suscribirme'
        },
        {
          id: 5,
          titulo: 'Combo Estudiante',
          categoria: 'Estudiantes',
          descripcion: 'Pensado especialmente para estudiantes. Incluye entrada, palomitas medianas y refresco mediano a un precio especial. Presenta tu carnet de estudiante vigente para aprovechar esta promoción.',
          precio: '€12.99',
          validez: 'Válido todo el año lectivo',
          condiciones: [
            'Imprescindible presentar carnet de estudiante vigente',
            'Válido de domingo a jueves (excepto festivos)',
            'No acumulable con otras promociones',
            'Sujeto a disponibilidad de asientos'
          ],
          botonTexto: 'Comprar ahora'
        },
        {
          id: 6,
          titulo: '2x1 en Snacks Martes y Jueves',
          categoria: 'Snacks',
          descripcion: 'Aprovecha esta oferta especial y llévate 2 productos por el precio de 1 en toda nuestra selección de snacks y bebidas. Perfecto para compartir y ahorrar.',
          descuento: '2x1 en todos los snacks',
          validez: 'Martes y jueves (todo el día)',
          condiciones: [
            'Aplicable solo a productos de igual o menor valor',
            'Válido únicamente martes y jueves',
            'No acumulable con otras promociones',
            'No aplica a menús combinados'
          ],
          botonTexto: 'Ver productos'
        },
        {
          id: 7,
          titulo: 'Paquete Grupal',
          categoria: 'Grupos',
          descripcion: 'Reserva para grupos de 10 o más personas y obtén un 25% de descuento en todas las entradas. Ideal para celebraciones, eventos de empresa o salidas con amigos.',
          descuento: '25% de descuento',
          validez: 'Válido todos los días previa reserva',
          condiciones: [
            'Mínimo 10 personas',
            'Reserva con al menos 24 horas de antelación',
            'Sujeto a disponibilidad',
            'Aplicable a cualquier película y horario'
          ],
          botonTexto: 'Reservar grupo'
        },
        {
          id: 8,
          titulo: 'Combo Pareja Romántica',
          categoria: 'Snacks',
          descripcion: 'Combo especial para parejas que incluye 2 entradas, palomitas grandes para compartir y 2 bebidas medianas. La combinación perfecta para disfrutar de una película juntos.',
          precio: '€24.99',
          validez: 'Disponible todos los días',
          condiciones: [
            'No válido para estrenos (primera semana)',
            'Válido para cualquier sala excepto VIP o especiales',
            'No acumulable con otras promociones',
            'Se puede adquirir online o en taquilla'
          ],
          botonTexto: 'Comprar combo'
        }
      ]
    };
  },
  computed: {
    promocionesFiltradas() {
      if (this.filtroCategoria === 'Todas') {
        return this.promociones;
      }
      return this.promociones.filter(promo => promo.categoria === this.filtroCategoria);
    }
  },
  methods: {
    filtrarPorCategoria(categoria) {
      this.filtroCategoria = categoria;
    },
    suscribirse() {
      alert(`Te has suscrito con el email: ${this.email}`);
      this.email = '';
    }
  }
};
</script>

<style scoped>
.section-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.divider {
  height: 4px;
  width: 100px;
  background: #0078C8;
  margin-top: 15px;
  border-radius: 2px;
}

.promo-card {
  background-color: rgba(10, 40, 80, 0.5);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
  height: 100%;
}

.promo-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 120, 200, 0.3);
}

.promo-image {
  overflow: hidden;
}

.categoria-badge {
  display: inline-block;
  background-color: #0078C8;
  color: white;
  border-radius: 20px;
  padding: 4px 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.btn-promo {
  display: inline-flex;
  align-items: center;
  background-color: transparent;
  color: #00A0E4;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid #00A0E4;
  transition: all 0.3s ease;
}

.btn-promo:hover {
  background-color: #00A0E4;
  color: white;
  transform: translateX(5px);
}

.newsletter-section {
  background: linear-gradient(135deg, rgba(0, 120, 200, 0.2), rgba(5, 29, 64, 0.8));
  border: 1px solid rgba(0, 160, 228, 0.3);
}

@media (max-width: 768px) {
  .promo-image {
    height: 200px;
  }
}
</style>
