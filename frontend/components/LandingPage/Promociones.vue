<template>
  <section class="bg-gray-100 py-24 relative">
    <div class="container mx-auto px-4">
      <div class="text-center mb-14">
        <h2 class="text-3xl font-extrabold text-blue-900 uppercase tracking-wide">OFERTAS Y SORTEOS</h2>
        <div class="h-1 w-24 bg-blue-600 mx-auto mt-4 mb-12 rounded"></div>
      </div>
      
      <!-- Grilla de promociones sin slider -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">

        <!-- Promoción 1: Palomitas gratis -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 border border-gray-200">
          <div class="h-44 overflow-hidden relative">
            <img src="/img/promo1.jpg" alt="Palomitas gratis" class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-105">
          </div>
          <div class="p-6 flex flex-col h-60">
            <h3 class="text-xl font-bold text-blue-900 mb-3 transition-colors duration-300 hover:text-blue-600">Palomitas gratis en tu cumpleaños</h3>
            <p class="text-gray-700 text-base leading-relaxed mb-5 flex-grow">
              Registra tu fecha de cumpleaños y disfruta de palomitas gratis.
            </p>
            <div class="flex justify-between items-center mt-auto">
              <span class="text-gray-500 font-medium text-sm">Válido todo el año</span>
              <a href="#" class="text-blue-600 font-bold text-sm hover:text-blue-500 transition-all duration-300 hover:translate-x-1 flex items-center">
                Registrarme <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Promoción 2: Día del espectador -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 border border-gray-200">
          <div class="h-44 overflow-hidden relative">
            <img src="/img/promo3.jpg" alt="Día del espectador" class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-105">
          </div>
          <div class="p-6 flex flex-col h-60">
            <h3 class="text-xl font-bold text-blue-900 mb-3 transition-colors duration-300 hover:text-blue-600">Día del espectador</h3>
            <p class="text-gray-700 text-base leading-relaxed mb-5 flex-grow">
              Todos los miércoles las entradas tienen un 30% de descuento. ¡Reserva ya!
            </p>
            <div class="flex justify-between items-center mt-auto">
              <span class="text-gray-500 font-medium text-sm">Oferta permanente</span>
              <a href="#" class="text-blue-600 font-bold text-sm hover:text-blue-500 transition-all duration-300 hover:translate-x-1 flex items-center">
                Reservar <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Promoción 4: Tarjeta Familia -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 border border-gray-200">
          <div class="h-44 overflow-hidden relative">
            <img src="/img/promo4.jpg" alt="Tarjeta Familia" class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-105">
          </div>
          <div class="p-6 flex flex-col h-60">
            <h3 class="text-xl font-bold text-blue-900 mb-3 transition-colors duration-300 hover:text-blue-600">Tarjeta Familia</h3>
            <p class="text-gray-700 text-base leading-relaxed mb-5 flex-grow">
              Obtén un 20% de descuento en todas las entradas familiares con nuestra tarjeta especial.
            </p>
            <div class="flex justify-between items-center mt-auto">
              <span class="text-gray-500 font-medium text-sm">Suscripción anual</span>
              <a href="#" class="text-blue-600 font-bold text-sm hover:text-blue-500 transition-all duration-300 hover:translate-x-1 flex items-center">
                Suscribirme <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>            
      </div>    
    </div>
  </section>
</template>

<script>
export default {
  name: "OfertasSection"
};
</script>
