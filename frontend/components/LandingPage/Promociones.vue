<template>
  <section class="ofertas-section">
    <div class="container mx-auto px-4 py-20">
      <div class="section-header text-center mb-14">
        <h2 class="section-title">OFERTAS Y SORTEOS</h2>
        <div class="divider mx-auto"></div>
      </div>
      
      <!-- Grilla de promociones sin slider -->
      <div class="promos-grid">

        <!-- Promoción 1: Palomitas gratis -->
        <div class="promo-card">
          <div class="promo-image-container">
            <img src="/img/promo1.jpg" alt="Palomitas gratis" class="promo-image">
          </div>
          <div class="promo-content">
            <h3 class="promo-title">Palomitas gratis en tu cumpleaños</h3>
            <p class="promo-description">
              Registra tu fecha de cumpleaños y disfruta de palomitas gratis en tu día especial.
            </p>
            <div class="promo-footer">
              <span class="expiry-date">Válido todo el año</span>
              <a href="#" class="participate-btn">
                Registrarme <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Promoción 2: Matinés -->
        <div class="promo-card">
          <div class="promo-image-container">
            <img src="/img/promo2.jpg" alt="Matinés" class="promo-image">
          </div>
          <div class="promo-content">
            <h3 class="promo-title">Matinés a mitad de precio</h3>
            <p class="promo-description">
              Disfruta de todas las películas antes de las 15:00h con un 50% de descuento en tu entrada.
            </p>
            <div class="promo-footer">
              <span class="expiry-date">Oferta permanente</span>
              <a href="#" class="participate-btn">
                Ver horarios <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Promoción 3: Día del espectador -->
        <div class="promo-card">
          <div class="promo-image-container">
            <img src="/img/promo3.jpg" alt="Día del espectador" class="promo-image">
          </div>
          <div class="promo-content">
            <h3 class="promo-title">Día del espectador</h3>
            <p class="promo-description">
              Todos los miércoles las entradas tienen un 30% de descuento. ¡Reserva ya!
            </p>
            <div class="promo-footer">
              <span class="expiry-date">Oferta permanente</span>
              <a href="#" class="participate-btn">
                Reservar <i class="fas fa-chevron-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Promoción 4: Tarjeta Familia -->
        <div class="promo-card">
          <div class="promo-image-container">
            <img src="/img/promo4.jpg" alt="Tarjeta Familia" class="promo-image">
          </div>
          <div class="promo-content">
            <h3 class="promo-title">Tarjeta Familia</h3>
            <p class="promo-description">
              Obtén un 20% de descuento en todas las entradas familiares con nuestra tarjeta especial.
            </p>
            <div class="promo-footer">
              <span class="expiry-date">Suscripción anual</span>
              <a href="#" class="participate-btn">
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

<style scoped>
.ofertas-section {
  background-color: #f0f4f8;
  position: relative;
  padding: 100px 0 80px 0;
}

.section-title {
  font-size: 2rem;
  font-weight: 800;
  color: #051D40;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.divider {
  height: 4px;
  width: 100px;
  background: #0078C8;
  margin-top: 15px;
  margin-bottom: 50px;
  border-radius: 2px;
}

/* Grilla de promociones - 4 columnas */
.promos-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
  width: 100%;
}

/* Tarjeta de promoción */
.promo-card {
  transition: all 0.3s ease;
  overflow: hidden;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.promo-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(5, 29, 64, 0.15);
}

.promo-image-container {
  height: 180px;
  overflow: hidden;
  position: relative;
}

.promo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.promo-card:hover .promo-image {
  transform: scale(1.05);
}

.promo-content {
  display: flex;
  flex-direction: column;
  padding: 20px;
  flex: 1;
}

.promo-title {
  color: #051D40;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 12px;
  transition: color 0.3s ease;
  line-height: 1.3;
}

.promo-card:hover .promo-title {
  color: #0078C8;
}

.promo-description {
  color: #555;
  font-size: 0.9rem;
  line-height: 1.5;
  margin-bottom: 20px;
  flex: 1;
}

.promo-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.expiry-date {
  color: #888;
  font-size: 0.8rem;
}

.participate-btn {
  display: inline-block;
  color: #0078C8;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
  font-size: 0.9rem;
}

.participate-btn:hover {
  color: #00A0E4;
  transform: translateX(5px);
}

/* Responsive */
@media (max-width: 992px) {
  .promos-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}

@media (max-width: 576px) {
  .promos-grid {
    grid-template-columns: 1fr;
  }
  
  .section-title {
    font-size: 1.8rem;
  }
}
</style>
