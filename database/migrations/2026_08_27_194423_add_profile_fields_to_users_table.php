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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role', 24)->default('member')->after('password');
            $table->text('bio')->nullable()->after('role');
            $table->string('avatar_url')->nullable()->after('bio');
            $table->string('postcode', 16)->nullable()->after('avatar_url');
            $table->boolean('onboarding_completed')->default(false)->after('postcode');
            $table->json('preferences')->nullable()->after('onboarding_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'role',
                'bio',
                'avatar_url',
                'postcode',
                'onboarding_completed',
                'preferences',
            ]);
        });
    }
};
