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
        Schema::create('evaluacion_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained()->onDelete('cascade');
            $table->foreignId('juez_user_id')->constrained('users')->onDelete('cascade'); // Quién escribió
            $table->text('comentario'); // El feedback
            $table->timestamps();

            // Regla: Un juez solo puede dejar 1 comentario por proyecto (evita duplicados)
            $table->unique(['proyecto_id', 'juez_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion_comentarios');
    }
};
