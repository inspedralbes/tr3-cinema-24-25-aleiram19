# Usa la imagen base de Node.js
FROM node:18

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia los archivos de configuración de npm/yarn
COPY package.json package-lock.json* yarn.lock* ./

# Instala las dependencias
RUN npm install

# Copia el resto del código de la aplicación
COPY . .

# Asegurarse de que se copian correctamente las imágenes
COPY public/img /app/public/img

# Expone el puerto de Nuxt.js (3000 por defecto)
EXPOSE 3000

# Comando para ejecutar Nuxt en desarrollo
CMD ["npm", "run", "dev", "--", "--host"]