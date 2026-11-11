<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evento_id')->constrained()->cascadeOnDelete();
            $table->dateTime('data_inscricao');
            $table->timestamps();

            $table->unique(['user_id', 'evento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
