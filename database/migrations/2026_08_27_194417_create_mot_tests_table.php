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
        Schema::create('mot_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('test_number')->nullable()->unique();
            $table->dateTime('completed_at');
            $table->date('expiry_at')->nullable();
            $table->string('result', 16);
            $table->unsignedInteger('odometer_value')->nullable();
            $table->string('odometer_unit', 8)->default('mi');
            $table->string('data_source', 16)->default('dvsa');
            $table->timestamps();

            $table->index(['vehicle_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mot_tests');
    }
};
