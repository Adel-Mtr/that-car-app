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
        Schema::create('mot_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mot_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_record_id')->nullable()->constrained()->nullOnDelete();
            $table->text('text');
            $table->string('type', 24);
            $table->boolean('dangerous')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['mot_test_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mot_defects');
    }
};
