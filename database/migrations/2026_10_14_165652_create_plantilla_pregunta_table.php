<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_pregunta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plantilla_id')
                ->constrained('plantillas')
                ->cascadeOnDelete();

            $table->foreignId('pregunta_id')
                ->constrained('preguntas')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('orden')->nullable();

            $table->timestamps();

            $table->unique([
                'plantilla_id',
                'pregunta_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_pregunta');
    }
};