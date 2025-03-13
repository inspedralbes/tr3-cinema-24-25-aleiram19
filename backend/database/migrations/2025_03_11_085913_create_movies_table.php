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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('director');
            $table->string('actors');
            $table->text('description');
            $table->string('trailer')->nullable(); // URL del tráiler
            $table->integer('duration'); // minutos
            $table->string('movie_genre');
            $table->date('release_date');
            $table->string('image')->nullable(); // URL del póster o imagen
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
