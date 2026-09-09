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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 32);
            $table->string('status', 24)->default('planned');
            $table->string('urgency', 24)->default('routine');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source', 32)->default('owner');
            $table->date('due_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->unsignedInteger('cost_pence')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('verification_status', 24)->default('owner_entered');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'status', 'due_at']);
            $table->index(['vehicle_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
