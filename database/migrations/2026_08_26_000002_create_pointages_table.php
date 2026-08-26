<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborateur_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('contenu_fait');
            $table->text('blocage')->nullable();
            $table->timestamps();

            $table->unique(['collaborateur_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
