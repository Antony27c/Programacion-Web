<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Versión reducida de la tabla de turnos: solo lo necesario para
 * relacionar turnos con servicios dentro de este módulo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('fecha_hora');
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();
        });

        Schema::create('turno_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('turno_id');
            $table->unsignedInteger('servicio_id');
            $table->decimal('precio', 10, 2);
            $table->unique(['turno_id', 'servicio_id']);

            $table->foreign('turno_id')->references('id')->on('turnos')
                ->cascadeOnDelete()->cascadeOnUpdate();
            // Un servicio usado por algún turno no se puede borrar.
            $table->foreign('servicio_id')->references('id')->on('servicios')
                ->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turno_servicio');
        Schema::dropIfExists('turnos');
    }
};
