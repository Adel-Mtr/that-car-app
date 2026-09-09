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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 32);
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_at');
            $table->unsignedSmallInteger('lead_days')->default(14);
            $table->string('channel', 32)->default('database');
            $table->string('recurrence', 32)->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'completed_at', 'due_at']);
            $table->index(['due_at', 'last_sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
