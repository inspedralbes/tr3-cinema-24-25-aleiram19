# Documentación de la Lógica de Negocio

Este documento describe las reglas de negocio implementadas en el sistema de gestión de cine.

## Estructura de la Base de Datos

La base de datos utiliza las siguientes tablas principales:

- `users`: Usuarios del sistema (clientes y administradores)
- `roles`: Roles de usuario (cliente, administrador)
- `movies`: Películas disponibles en cartelera
- `auditoriums`: Salas de cine
- `seats`: Asientos dentro de cada sala
- `screenings`: Sesiones programadas para ver películas
- `bookings`: Reservas de asientos para sesiones específicas
- `tickets`: Tickets generados para las reservas confirmadas

## Salas y Asientos

- Cada sala (`auditorium`) tiene una capacidad y un tipo (2D, 3D, IMAX).
- Los asientos se identifican con una combinación de letra (fila) y número (ej. A1, B3, F2).
- **Asientos VIP**: Los asientos de la fila F son considerados VIP y tienen un precio especial.

## Precios de Entradas

Los precios de las entradas se basan en dos factores:
1. **Tipo de asiento**: Normal o VIP (fila F)
2. **Tipo de sesión**: Normal o Especial (día del espectador)

Tabla de precios:

| Tipo de Asiento | Día Normal | Día del Espectador |
|-----------------|------------|-------------------|
| Normal          | 6€         | 4€                |
| VIP (Fila F)    | 8€         | 6€                |

## Reglas de Reserva

1. Un usuario no puede reservar más de 10 asientos para una misma sesión.
2. Un usuario no puede tener reservas para múltiples sesiones futuras simultáneamente.
3. Las reservas tienen un estado (`reserved` o `purchased`).
4. Un asiento reservado cambia su estado a `busy`.

## Flujo de Compra de Entradas

1. El usuario selecciona una sesión.
2. El sistema verifica si el usuario puede realizar una reserva.
3. El usuario selecciona los asientos (máximo 10).
4. El sistema calcula el precio total basado en:
   - El tipo de asiento (normal o VIP)
   - Si es día del espectador
5. El usuario confirma la reserva.
6. Se generan los registros en las tablas `bookings` y `tickets`.

## Estructura de Directorios

El proyecto sigue la estructura estándar de Laravel con las siguientes particularidades:

- Los modelos se encuentran en `app/Models/`
- Los controladores se encuentran en `app/Http/Controllers/`
- Las migraciones se encuentran en `database/migrations/`

## Limitaciones Técnicas

- Las sesiones solo pueden ser programadas en tres horarios: 16:00, 18:00 y 20:00.
- No se permite la modificación de una sesión que ya tiene reservas asociadas.
- Los asientos son específicos para cada sala y no se pueden mover entre salas.
