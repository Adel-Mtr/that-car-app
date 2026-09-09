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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('registration', 16);
            $table->string('vin', 32)->nullable();
            $table->string('make');
            $table->string('model');
            $table->string('variant')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('colour')->nullable();
            $table->string('fuel_type', 32)->nullable();
            $table->string('transmission', 32)->nullable();
            $table->unsignedInteger('engine_size_cc')->nullable();
            $table->unsignedInteger('current_mileage')->default(0);
            $table->unsignedInteger('annual_mileage')->nullable();
            $table->string('mot_status', 32)->default('unknown');
            $table->date('mot_due_at')->nullable();
            $table->string('tax_status', 32)->default('unknown');
            $table->date('tax_due_at')->nullable();
            $table->date('insurance_due_at')->nullable();
            $table->unsignedTinyInteger('health_score')->default(100);
            $table->unsignedInteger('valuation_pence')->nullable();
            $table->string('image_path')->nullable();
            $table->string('visibility', 24)->default('private');
            $table->json('metadata')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['owner_id', 'registration']);
            $table->index(['owner_id', 'health_score']);
            $table->index(['mot_due_at', 'tax_due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
