<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaborateurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->string('nom')->nullable();
            $table->string('email');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organisation_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborateurs');
    }
};
