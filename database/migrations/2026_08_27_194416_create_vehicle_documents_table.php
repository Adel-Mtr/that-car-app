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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('type', 32);
            $table->string('disk', 32)->default('local');
            $table->string('path');
            $table->string('original_filename');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size_bytes');
            $table->date('document_date')->nullable();
            $table->string('verification_status', 24)->default('owner_uploaded');
            $table->timestamp('verified_at')->nullable();
            $table->json('extracted_data')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'type', 'document_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
