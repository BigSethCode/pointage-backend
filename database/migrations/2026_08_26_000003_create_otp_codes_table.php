<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborateur_id')->constrained()->cascadeOnDelete();
            $table->string('code', 6);
            $table->dateTime('expires_at');
            $table->dateTime('consumed_at')->nullable();
            $table->timestamps();

            $table->index(['collaborateur_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
