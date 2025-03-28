<template>
  <footer class="bg-blue-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
      <!-- Sección superior -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Información de la compañía -->
        <div>
          <h3 class="text-xl font-bold mb-6 pb-2 border-b border-blue-600 inline-block">Sobre Nosotros</h3>
          <p class="text-gray-300 mb-4">
            CineXeperience es un grupo líder en exhibición cinematográfica que ofrece las mejores experiencias de entretenimiento.
          </p>
          <div class="flex space-x-4 mt-6">
            <a href="#" class="text-white hover:text-blue-400 transition-colors">
              <i class="fab fa-facebook-f text-xl"></i>
            </a>
            <a href="#" class="text-white hover:text-blue-400 transition-colors">
              <i class="fab fa-twitter text-xl"></i>
            </a>
            <a href="#" class="text-white hover:text-blue-400 transition-colors">
              <i class="fab fa-instagram text-xl"></i>
            </a>
            <a href="#" class="text-white hover:text-blue-400 transition-colors">
              <i class="fab fa-youtube text-xl"></i>
            </a>
          </div>
        </div>
        
        <!-- Enlaces rápidos -->
        <div>
          <h3 class="text-xl font-bold mb-6 pb-2 border-b border-blue-600 inline-block">Enlaces Rápidos</h3>
          <ul class="space-y-3">
            <li>
              <NuxtLink to="/cartelera" class="text-gray-300 hover:text-blue-400 transition-colors">
                <i class="fas fa-chevron-right mr-2 text-sm"></i> Cartelera
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/promociones" class="text-gray-300 hover:text-blue-400 transition-colors">
                <i class="fas fa-chevron-right mr-2 text-sm"></i> Promociones
              </NuxtLink>
            </li>
            <li>
              <a href="#" class="text-gray-300 hover:text-blue-400 transition-colors">
                <i class="fas fa-chevron-right mr-2 text-sm"></i> Nuestros Cines
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-300 hover:text-blue-400 transition-colors">
                <i class="fas fa-chevron-right mr-2 text-sm"></i> Próximos Estrenos
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-300 hover:text-blue-400 transition-colors">
                <i class="fas fa-chevron-right mr-2 text-sm"></i> Trabaja con Nosotros
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Información de contacto -->
        <div>
          <h3 class="text-xl font-bold mb-6 pb-2 border-b border-blue-600 inline-block">Contacto</h3>
          <ul class="space-y-3">
            <li class="flex items-start">
              <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-400"></i>
              <span class="text-gray-300">Avenida Principal 123, Madrid, España</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-phone-alt mr-3 text-blue-400"></i>
              <span class="text-gray-300">+34 91 234 56 78</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-envelope mr-3 text-blue-400"></i>
              <span class="text-gray-300">info@cinexperience.com</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-clock mr-3 text-blue-400"></i>
              <span class="text-gray-300">Lun - Dom: 10:00 - 00:00</span>
            </li>
          </ul>
        </div>
        
        <!-- Boletín informativo -->
        <div>
          <h3 class="text-xl font-bold mb-6 pb-2 border-b border-blue-600 inline-block">Suscríbete</h3>
          <p class="text-gray-300 mb-4">
            Suscríbete a nuestro boletín para recibir las últimas noticias y promociones exclusivas.
          </p>
          <div class="flex mt-4">
            <input 
              type="email" 
              placeholder="Tu correo electrónico" 
              class="px-4 py-2 bg-navy-800 text-white rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-400 w-full"
            >
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-r-md transition-colors">
              <i class="fas fa-paper-plane"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Línea divisoria -->
      <div class="border-t border-gray-700 my-8"></div>
      
      <!-- Sección inferior -->
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-400 text-sm mb-4 md:mb-0">
          &copy; {{ new Date().getFullYear() }} CineXeperience. Todos los derechos reservados.
        </p>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-400 hover:text-blue-400 text-sm transition-colors">Aviso Legal</a>
          <a href="#" class="text-gray-400 hover:text-blue-400 text-sm transition-colors">Política de Privacidad</a>
          <a href="#" class="text-gray-400 hover:text-blue-400 text-sm transition-colors">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
</template>
