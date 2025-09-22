<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema; // Add this import
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists and has records with NULL values
        if (Schema::hasTable('personal_access_tokens')) {
            // First, fix any existing NULL values
            DB::table('personal_access_tokens')
                ->whereNull('tokenable_type')
                ->orWhereNull('tokenable_id')
                ->update([
                    'tokenable_type' => 'App\Models\User',
                    'tokenable_id' => 1
                ]);
        }
    }

    public function down(): void
    {
        // This migration doesn't need to be reversed
    }
};