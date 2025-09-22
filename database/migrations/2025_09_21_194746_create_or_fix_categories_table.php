<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            // Create the table if it doesn't exist
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            // Fix the table if it exists but has wrong structure
            if (!Schema::hasColumn('categories', 'name')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->string('name')->after('id');
                });
            }
            
            if (!Schema::hasColumn('categories', 'slug')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->string('slug')->after('name');
                });
            }
            
            if (!Schema::hasColumn('categories', 'description')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('slug');
                });
            }
        }
    }

    public function down(): void
    {
        // Don't drop the table in down method to avoid data loss
        Schema::table('categories', function (Blueprint $table) {
            // You can optionally remove columns here if needed
        });
    }
};