<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->unsignedSmallInteger('anio_estreno');
            $table->string('genero', 40);
            $table->string('director', 120)->nullable();
            $table->date('fecha_vista')->index();
            $table->unsignedTinyInteger('calificacion');
            $table->text('comentario');
            $table->string('portada_url', 2048)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
