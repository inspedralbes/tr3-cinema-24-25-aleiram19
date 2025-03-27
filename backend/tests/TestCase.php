<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase {
        refreshDatabase as baseRefreshDatabase;
    }
    
    /**
     * Sobrescribir el método refreshDatabase
     */
    public function refreshDatabase()
    {
        $this->baseRefreshDatabase();
        
        // Antes de truncar cualquier tabla, desactivar la verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncar todas las tablas relevantes para evitar conflictos de unique constraint
        DB::table('movie_genres')->truncate();
        
        // Reactivar la verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Configura el entorno de pruebas
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable foreign key checks during setup
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Asegurar que el directorio de almacenamiento existe
        Storage::makeDirectory('framework/testing/disks/public');
        
        // Crear roles si no existen
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'description' => 'Administrator']);
        }
        
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user', 'description' => 'Regular User']);
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
