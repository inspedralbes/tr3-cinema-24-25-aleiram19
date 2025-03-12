<template>
  <section class="peliculas-section py-20">
    <div class="container mx-auto px-4">
      <div class="section-header text-center mb-14">
        <h1 class="section-title">CARTELERA</h1>
        <div class="divider mx-auto"></div>
        <p class="text-gray-300 mt-4">Disfruta de los mejores estrenos en CineXeperience</p>
      </div>
      
      <!-- Filtros -->
      <div class="filtros mb-10">
        <div class="flex flex-wrap justify-center gap-4">
          <button 
            v-for="genero in generos" 
            :key="genero"
            @click="filtrarPorGenero(genero)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all',
              filtroGenero === genero 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
            ]"
          >
            {{ genero }}
          </button>
        </div>
      </div>
      
      <!-- Grid de películas -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="pelicula in peliculasFiltradas" 
          :key="pelicula.id"
          class="pelicula-card"
        >
          <div class="pelicula-poster relative overflow-hidden">
            <!-- Usamos un color de fondo como respaldo en caso de que la imagen no cargue -->
            <div class="w-full h-full bg-gradient-to-b from-blue-900 to-gray-900">
              <div class="pelicula-poster-placeholder flex items-center justify-center h-full">
                <span class="text-white text-lg font-bold">{{ pelicula.titulo }}</span>
              </div>
            </div>
            <div class="pelicula-overlay">
              <div class="rating">
                <i class="fas fa-star text-yellow-400"></i>
                <span>{{ pelicula.calificacion }}</span>
              </div>
              <NuxtLink 
                :to="'/select-movie?id=' + pelicula.id" 
                class="btn-comprar"
              >
                COMPRAR
              </NuxtLink>
              <button class="btn-trailer" @click="verTrailer(pelicula.trailer)">
                <i class="fas fa-play-circle"></i> Ver Trailer
              </button>
            </div>
          </div>
          <div class="pelicula-info p-4">
            <h3 class="pelicula-titulo font-bold text-white text-lg mb-1">{{ pelicula.titulo }}</h3>
            <div class="flex justify-between items-center">
              <span class="text-gray-400 text-sm">{{ pelicula.duracion }} min</span>
              <span class="genero-badge">{{ pelicula.genero }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para trailer -->
    <div v-if="trailerActivo" class="trailer-modal" @click="cerrarTrailer">
      <div class="trailer-container">
        <button class="cerrar-trailer" @click.stop="cerrarTrailer">
          <i class="fas fa-times"></i>
        </button>
        <iframe 
          :src="trailerUrl + '?autoplay=1'" 
          frameborder="0" 
          allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
          allowfullscreen
        ></iframe>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "PeliculasGrid",
  data() {
    return {
      filtroGenero: 'Todos',
      trailerActivo: false,
      trailerUrl: '',
      generos: ['Todos', 'Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 'Terror'],
      peliculas: [
        {
          id: 1,
          titulo: 'Dune: Parte Dos',
          genero: 'Ciencia Ficción',
          duracion: 166,
          calificacion: 8.7,
          trailer: 'https://www.youtube.com/embed/aSgOPHG8d5g'
        },
        {
          id: 2,
          titulo: 'Godzilla y Kong: El Nuevo Imperio',
          genero: 'Acción',
          duracion: 115,
          calificacion: 7.5,
          trailer: 'https://www.youtube.com/embed/odM92ap8_c0'
        },
        {
          id: 3,
          titulo: 'Kung Fu Panda 4',
          genero: 'Aventura',
          duracion: 94,
          calificacion: 7.2,
          trailer: 'https://www.youtube.com/embed/clx-c3hDMTg'
        },
        {
          id: 4,
          titulo: 'Ghostbusters: Apocalipsis Fantasma',
          genero: 'Comedia',
          duracion: 115,
          calificacion: 7.0,
          trailer: 'https://www.youtube.com/embed/fcAUUb3WGrY'
        },
        {
          id: 5,
          titulo: 'Furiosa: De la Saga Mad Max',
          genero: 'Acción',
          duracion: 150,
          calificacion: 8.2,
          trailer: 'https://www.youtube.com/embed/GYyTdpR6HnQ'
        },
        {
          id: 6,
          titulo: 'Inside Out 2',
          genero: 'Aventura',
          duracion: 105,
          calificacion: 8.5,
          trailer: 'https://www.youtube.com/embed/VPC7iyA4Es0'
        },
        {
          id: 7,
          titulo: 'Mi Villano Favorito 4',
          genero: 'Comedia',
          duracion: 95,
          calificacion: 7.8,
          trailer: 'https://www.youtube.com/embed/ejl0IxTXOe4'
        },
        {
          id: 8,
          titulo: 'Alien: Romulus',
          genero: 'Terror',
          duracion: 120,
          calificacion: 7.9,
          trailer: 'https://www.youtube.com/embed/yyLbSCpGaVw'
        },
        {
          id: 9,
          titulo: 'Deadpool & Wolverine',
          genero: 'Acción',
          duracion: 128,
          calificacion: 8.8,
          trailer: 'https://www.youtube.com/embed/XTKYBdWDrTI'
        },
        {
          id: 10,
          titulo: 'Joker: Folie à Deux',
          genero: 'Drama',
          duracion: 135,
          calificacion: 8.4,
          trailer: 'https://www.youtube.com/embed/a5JqIwRgZwI'
        },
        {
          id: 11,
          titulo: 'Mufasa: El Rey León',
          genero: 'Aventura',
          duracion: 118,
          calificacion: 7.7,
          trailer: 'https://www.youtube.com/embed/Z_AvpI0-QMk'
        },
        {
          id: 12,
          titulo: 'Oppenheimer',
          genero: 'Drama',
          duracion: 180,
          calificacion: 9.0,
          trailer: 'https://www.youtube.com/embed/uYPbbksJxIg'
        }
      ]
    };
  },
  computed: {
    peliculasFiltradas() {
      if (this.filtroGenero === 'Todos') {
        return this.peliculas;
      }
      return this.peliculas.filter(pelicula => pelicula.genero === this.filtroGenero);
    }
  },
  methods: {
    filtrarPorGenero(genero) {
      this.filtroGenero = genero;
    },
    verTrailer(url) {
      this.trailerUrl = url;
      this.trailerActivo = true;
      document.body.style.overflow = 'hidden';
    },
    cerrarTrailer() {
      this.trailerActivo = false;
      this.trailerUrl = '';
      document.body.style.overflow = 'auto';
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

.pelicula-card {
  border-radius: 8px;
  overflow: hidden;
  background-color: rgba(10, 40, 80, 0.5);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
}

.pelicula-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 120, 200, 0.3);
}

.pelicula-poster {
  height: 360px;
}

.pelicula-poster-placeholder {
  text-align: center;
  padding: 20px;
}

.pelicula-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(5, 29, 64, 0.1), rgba(5, 29, 64, 0.9));
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  align-items: center;
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.pelicula-poster:hover .pelicula-overlay {
  opacity: 1;
}

.rating {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: rgba(5, 29, 64, 0.8);
  color: white;
  border-radius: 20px;
  padding: 4px 10px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 5px;
}

.genero-badge {
  background-color: #0078C8;
  color: white;
  border-radius: 20px;
  padding: 2px 10px;
  font-size: 0.75rem;
  font-weight: 500;
}

.btn-comprar {
  background-color: #0078C8;
  color: white;
  text-transform: uppercase;
  font-weight: bold;
  padding: 8px 25px;
  border-radius: 30px;
  margin-bottom: 10px;
  transition: all 0.3s ease;
}

.btn-comprar:hover {
  background-color: #00A0E4;
  transform: scale(1.05);
}

.btn-trailer {
  color: white;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: all 0.3s ease;
}

.btn-trailer:hover {
  color: #00A0E4;
}

.trailer-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.trailer-container {
  position: relative;
  width: 90%;
  max-width: 900px;
  aspect-ratio: 16 / 9;
}

.trailer-container iframe {
  width: 100%;
  height: 100%;
}

.cerrar-trailer {
  position: absolute;
  top: -40px;
  right: 0;
  color: white;
  font-size: 1.5rem;
  background: none;
  border: none;
  cursor: pointer;
}

@media (max-width: 768px) {
  .pelicula-poster {
    height: 280px;
  }
}
</style>
