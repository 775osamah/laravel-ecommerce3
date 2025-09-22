<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists
        if (Schema::hasTable('personal_access_tokens')) {
            // Make sure columns are not nullable
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->string('tokenable_type')->nullable(false)->change();
                $table->unsignedBigInteger('tokenable_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        // Revert changes if needed
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('tokenable_type')->nullable()->change();
            $table->unsignedBigInteger('tokenable_id')->nullable()->change();
        });
    }
};