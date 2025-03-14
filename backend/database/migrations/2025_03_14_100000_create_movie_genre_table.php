<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movie_genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Agregar la relación a la tabla movies
        Schema::table('movies', function (Blueprint $table) {
            // Primero, eliminamos la columna antigua
            $table->dropColumn('movie_genre');

            // Luego añadimos la relación con la nueva tabla
            $table->foreignId('movie_genre_id')->nullable()->after('duration')->constrained('movie_genres')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restauramos la columna original en la tabla movies
        Schema::table('movies', function (Blueprint $table) {
            $table->dropForeign(['movie_genre_id']);
            $table->dropColumn('movie_genre_id');
            $table->string('movie_genre')->nullable()->after('duration');
        });

        Schema::dropIfExists('movie_genres');
    }
};
