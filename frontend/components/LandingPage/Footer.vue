<template>
  <footer class="footer-section">
    <div class="container mx-auto px-4 py-16">
      <div class="footer-grid">
        <!-- Columna 1: Información de la Compañía -->
        <div class="footer-col">
          <h4 class="footer-heading">Quiénes somos</h4>
          <ul class="footer-links">
            <li><a href="#">Quiénes somos</a></li>
            <li><a href="#">Trabaja con nosotros</a></li>
            <li><a href="#">CineXperience Business</a></li>
            <li><a href="#">Atención al cliente</a></li>
            <li><a href="#">Apoyo Institucional</a></li>
            <li><a href="#">Transparencia</a></li>
          </ul>
        </div>
        
        <!-- Columna 2: Eventos y Experiencias -->
        <div class="footer-col">
          <h4 class="footer-heading">Eventos</h4>
          <ul class="footer-links">
            <li><a href="#">CineXperience LUXE</a></li>
            <li><a href="#">Salas Premium</a></li>
            <li><a href="#">Infantil y Colegios</a></li>
            <li><a href="#">Ciclos</a></li>
            <li><a href="#">Blog de CineXperience</a></li>
          </ul>
        </div>
        
        <!-- Columna 3: Legal -->
        <div class="footer-col">
          <h4 class="footer-heading">Aviso Legal</h4>
          <ul class="footer-links">
            <li><a href="#">Condiciones de compra</a></li>
            <li><a href="#">Condiciones del programa</a></li>
            <li><a href="#">UNLIMITED CARD</a></li>
            <li><a href="#">Política de privacidad</a></li>
            <li><a href="#">Política de Cookies</a></li>
            <li><a href="#">Código de conducta y ética</a></li>
          </ul>
        </div>
        
        <!-- Columna 4: Apps y Redes Sociales -->
        <div class="footer-col">
          <h4 class="footer-heading">Apps y Redes</h4>
          <ul class="footer-links">
            <li><a href="#">Descarga la app móvil para Android</a></li>
            <li><a href="#">Descarga la app móvil para Apple</a></li>
          </ul>
          
          <div class="social-links mt-4">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      
      <div class="copyright-bar mt-16 pt-6 border-t border-gray-700 text-center">
        <p>© 2025 CineXperience. Todos los derechos reservados.</p>
      </div>
      
      <!-- Barra de compra rápida (fija en la parte inferior) -->
      <div class="quick-purchase-bar">
        <div class="container mx-auto px-4">
          <div class="quick-purchase-grid">
            <div class="quick-purchase-text">Compra rápida</div>
            <select class="purchase-select">
              <option value="">Cine</option>
              <option value="1">CineXperience Madrid</option>
              <option value="2">CineXperience Barcelona</option>
            </select>
            <select class="purchase-select">
              <option value="">Películas</option>
              <option value="1">Mickey 17</option>
              <option value="2">Wolfgang</option>
            </select>
            <select class="purchase-select">
              <option value="">Fecha</option>
              <option value="1">Hoy, 12 Marzo</option>
              <option value="2">Mañana, 13 Marzo</option>
            </select>
            <select class="purchase-select">
              <option value="">Sesiones</option>
              <option value="1">16:30 - VOSE</option>
              <option value="2">19:00 - 3D</option>
              <option value="3">22:15 - Standard</option>
            </select>
            <button class="buy-tickets-btn">Comprar entradas</button>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
}
</script>

<style scoped>
.footer-section {
  background-color: #051D40;
  color: #f0f0f0;
  position: relative;
  padding: 100px 0 100px 0; /* Aumentado el espacio */
}

/* Grid para el footer - 4 columnas */
.footer-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px; /* Aumentado el espacio entre columnas */
}

.footer-heading {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 1.8rem; /* Más espacio debajo de los encabezados */
  color: white;
  position: relative;
}

.footer-heading:after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -12px; /* Más espacio para la línea */
  width: 40px;
  height: 2px;
  background-color: #0078C8;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.8rem; /* Más espacio entre enlaces */
}

.footer-links a {
  color: #bbb;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  padding: 3px 0; /* Área de clic más grande */
  display: inline-block; /* Para que el padding funcione */
}

.footer-links a:hover {
  color: #0078C8;
  padding-left: 8px; /* Desplazamiento más visible */
}

.social-links {
  display: flex;
  gap: 20px; /* Más espacio entre iconos */
  flex-wrap: wrap;
  margin-top: 20px; /* Más espacio arriba */
}

.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px; /* Iconos más grandes */
  height: 40px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
  transition: all 0.3s ease;
  font-size: 1.1rem; /* Íconos más grandes */
}

.social-icon:hover {
  background-color: #0078C8;
  transform: translateY(-5px); /* Más elevación */
  box-shadow: 0 5px 15px rgba(0, 120, 200, 0.3); /* Sombra más visible */
}

.copyright-bar {
  color: #bbb;
  font-size: 0.95rem;
  margin-top: 80px; /* Más espacio en la parte superior */
  padding-top: 20px; /* Más espacio del borde */
}

/* Barra de compra rápida */
.quick-purchase-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #0A2647;
  padding: 15px 0; /* Más altura */
  z-index: 100;
  box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.2); /* Sombra más visible */
}

.quick-purchase-grid {
  display: grid;
  grid-template-columns: auto repeat(4, 1fr) auto;
  gap: 15px; /* Más espacio entre elementos */
  align-items: center;
}

.quick-purchase-text {
  font-weight: 600;
  white-space: nowrap;
  padding-right: 15px;
  font-size: 1.05rem; /* Texto más grande */
}

.purchase-select {
  background-color: #051D40;
  border: 1px solid #0078C8;
  color: white;
  padding: 8px 15px; /* Más altura */
  border-radius: 4px;
  transition: all 0.3s ease;
  width: 100%;
  font-size: 0.95rem; /* Texto más grande */
}

.purchase-select:focus {
  border-color: #00A0E4;
  outline: none;
  box-shadow: 0 0 0 2px rgba(0, 160, 228, 0.25); /* Enfoque visible */
}

.buy-tickets-btn {
  background-color: #0078C8;
  color: white;
  border: none;
  padding: 10px 24px; /* Botón más grande */
  border-radius: 4px;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.buy-tickets-btn:hover {
  background-color: #00A0E4;
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 160, 228, 0.3); /* Sombra más visible */
}

/* Responsive */
@media (max-width: 992px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
  }
  
  .quick-purchase-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .quick-purchase-text {
    text-align: center;
    margin-bottom: 10px;
  }
  
  .buy-tickets-btn {
    margin-top: 10px;
  }
  
  .footer-section {
    padding-bottom: 150px; /* Espacio adicional para la barra fija */
  }
}

@media (max-width: 576px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 25px;
  }
  
  .footer-section {
    padding: 60px 0 150px 0;
  }
}
</style>