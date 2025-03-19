<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Movie;
use App\Models\Auditorium;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero limpiamos los screenings existentes (sin usar truncate para evitar problemas con claves foráneas)
        try {
            DB::table('screenings')->delete();
        } catch (\Exception $e) {
            $this->command->error('No se pudieron eliminar los screenings existentes: ' . $e->getMessage());
            // Continuamos con el seeder aunque no se hayan podido eliminar los registros
        }

        // Obtenemos todas las películas disponibles
        $allMovies = Movie::all();
        
        // Verificamos que haya al menos 7 días * 2 películas = 14 películas
        // Si hay menos, repetiremos algunas películas
        $totalMovies = $allMovies->count();
        
        // Si no hay películas, no creamos screenings
        if ($allMovies->count() === 0) {
            $this->command->info('No hay películas disponibles para crear screenings');
            return;
        }
        
        // Obtenemos todas las salas disponibles
        $auditoriums = Auditorium::all();
        
        // Si no hay salas, no creamos screenings
        if ($auditoriums->count() === 0) {
            $this->command->info('No hay auditorios disponibles para crear screenings');
            return;
        }
        
        // Horarios disponibles (solo 18:00 y 20:00 según el requerimiento)
        $times = [18, 20];
        
        // Crear proyecciones para los próximos 7 días
        $screenings = [];
        $now = Carbon::now();
        
        // Determinar si el miércoles es día especial
        $isWednesdaySpecial = true;
        
        // Para cada día
        for ($day = 0; $day < 7; $day++) {
            $date = $now->copy()->addDays($day);
            
            // Determinar si es día especial (miércoles)
            $isSpecialDay = ($date->dayOfWeek === Carbon::WEDNESDAY && $isWednesdaySpecial);
            
            // Seleccionar 2 películas diferentes para este día
            // Usamos el índice del día para rotar entre todas las películas disponibles
            $moviesForThisDay = [];
            for ($i = 0; $i < 2; $i++) {
                // Cálculo para seleccionar película: rotamos el índice para cada día
                $movieIndex = ($day * 2 + $i) % $totalMovies;
                $moviesForThisDay[] = $allMovies[$movieIndex];
            }
            
            // Distribuir exactamente 2 películas por día, una a las 18:00 y otra a las 20:00
            // Para cada día, aseguramos que la película 1 tenga horario de 18:00 y la película 2 tenga horario de 20:00
            foreach ($moviesForThisDay as $index => $movie) {
                // Asignar horario fijo según índice (0 -> 18:00, 1 -> 20:00)
                $time = $times[$index]; // Esto asegura que la película 1 sea a las 18 y la película 2 a las 20
                
                // Asignamos a la sala (alternando)
                $auditoriumIndex = $index % $auditoriums->count();
                $auditorium = $auditoriums[$auditoriumIndex];
                
                // Solo crear si el horario es futuro o hoy pero hora futura
                $screeningDateTime = $date->copy()->setTime($time, 0, 0);
                
                if ($screeningDateTime->gt($now)) {
                    // Calcular precio base según la sala y si es día especial
                    $basePrice = 6.00; // Precio base normal
                    
                    // Aumentamos precio para la última sala (asumimos que es premium/IMAX)
                    if ($auditorium->id === $auditoriums->last()->id) {
                        $basePrice = 8.00;
                    }
                    
                    // Reducir precio si es día especial
                    if ($isSpecialDay) {
                        $basePrice = max(4.00, $basePrice - 2.00); // Reducimos 2€, pero mínimo 4€
                    }
                    
                    $screenings[] = [
                        'movie_id' => $movie->id,
                        'auditorium_id' => $auditorium->id,
                        'date_time' => $screeningDateTime,
                        'price' => $basePrice,
                        'is_special' => $isSpecialDay,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $this->command->info("Creando screening: {$movie->title} - {$screeningDateTime}");
                }
            }
        }
        
        // Si no hay screenings creados, notificamos
        if (empty($screenings)) {
            $this->command->info('No se generaron screenings futuros');
            return;
        }
        
        // Insertar todos los screenings
        DB::table('screenings')->insert($screenings);
        $this->command->info('Se han creado ' . count($screenings) . ' screenings con éxito');
    }
}
