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
        
        // Verificamos que haya películas
        $totalMovies = $allMovies->count();
        
        // Si no hay películas, no creamos screenings
        if ($totalMovies === 0) {
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
        
        // Horarios disponibles (asignamos un horario para cada película)
        $times = [18, 20]; // 18:00 para la primera película, 20:00 para la segunda
        
        // Crear proyecciones para los próximos 7 días
        $screenings = [];
        $now = Carbon::now();
        
        // El día del espectador es el miércoles
        $specialDay = Carbon::WEDNESDAY;
        
        // Para cada día
        for ($day = 0; $day < 7; $day++) {
            $date = $now->copy()->addDays($day);
            
            // Determinar si es día del espectador (miércoles)
            $isSpecialDay = ($date->dayOfWeek === $specialDay);
            
            // Seleccionar 2 películas diferentes para este día
            // Usamos el índice del día para rotar entre todas las películas disponibles
            $moviesForThisDay = [];
            for ($i = 0; $i < 2; $i++) {
                // Cálculo para seleccionar película: rotamos el índice para cada día
                $movieIndex = ($day * 2 + $i) % $totalMovies;
                $moviesForThisDay[] = $allMovies[$movieIndex];
            }
            
            // Asignamos cada película a un auditorio diferente
            // Usamos 2 películas por día (esto se puede ampliar en el futuro)
            for ($i = 0; $i < 2; $i++) {
                $movie = $moviesForThisDay[$i];
                $time = $times[$i]; // 18:00 para la primera película, 20:00 para la segunda
                $auditorium = $auditoriums[$i]; // Cada película va a un auditorio diferente
                
                $screeningDateTime = $date->copy()->setTime($time, 0, 0);
                
                // Solo crear si el horario es futuro o hoy pero hora futura
                if ($screeningDateTime->gt($now)) {
                    // Configuramos precio normal y VIP según los requisitos
                    $normalPrice = 6.00; // Precio normal base
                    $vipPrice = 8.00;    // Precio VIP base (fila F)
                    
                    // Reducir precios si es día del espectador (miércoles)
                    if ($isSpecialDay) {
                        $normalPrice = 4.00; // 6€ - 2€ para día del espectador
                        $vipPrice = 6.00;    // 8€ - 2€ para día del espectador
                    }
                    
                    // Crear screening con el precio base
                    $screenings[] = [
                        'movie_id' => $movie->id,
                        'auditorium_id' => $auditorium->id,
                        'date_time' => $screeningDateTime,
                        'price' => $normalPrice,  // Precio normal para asientos regulares
                        'is_special' => $isSpecialDay,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $this->command->info("Creando screening: {$movie->title} en {$auditorium->name} - {$screeningDateTime->format('Y-m-d H:i')} - Precio normal: {$normalPrice}€");
                    
                    // Informar sobre precios VIP para la sala 2 (tiene asientos VIP en la fila F)
                    if ($auditorium->id == 2) {
                        $this->command->info("  - Asientos VIP (fila F) para esta proyección: {$vipPrice}€");
                    }
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