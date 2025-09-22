<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns to categories table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'name')) {
                    $table->string('name')->after('id');
                }
                if (!Schema::hasColumn('categories', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (!Schema::hasColumn('categories', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
            });
        }

        // Add missing columns to products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'name')) {
                    $table->string('name')->after('id');
                }
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (!Schema::hasColumn('products', 'description')) {
                    $table->text('description')->after('slug');
                }
                if (!Schema::hasColumn('products', 'price')) {
                    $table->decimal('price', 10, 2)->after('description');
                }
                if (!Schema::hasColumn('products', 'quantity')) {
                    $table->integer('quantity')->after('price');
                }
                if (!Schema::hasColumn('products', 'category_id')) {
                    $table->foreignId('category_id')->after('quantity')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('products', 'image')) {
                    $table->string('image')->nullable()->after('category_id');
                }
            });
        }

        // Add missing columns to orders table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'code')) {
                    $table->string('code')->unique()->after('id');
                }
                if (!Schema::hasColumn('orders', 'user_id')) {
                    $table->foreignId('user_id')->after('code')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('orders', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->after('user_id');
                }
                if (!Schema::hasColumn('orders', 'status')) {
                    $table->enum('status', ['PENDING', 'PAID', 'CANCELLED', 'COMPLETED'])->default('PENDING')->after('total_amount');
                }
                if (!Schema::hasColumn('orders', 'address')) {
                    $table->json('address')->nullable()->after('status');
                }
            });
        }

        // Add missing columns to order_items table
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'order_id')) {
                    $table->foreignId('order_id')->after('id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('order_items', 'product_id')) {
                    $table->foreignId('product_id')->after('order_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('order_items', 'quantity')) {
                    $table->integer('quantity')->after('product_id');
                }
                if (!Schema::hasColumn('order_items', 'price')) {
                    $table->decimal('price', 10, 2)->after('quantity');
                }
            });
        }
    }

    public function down(): void
    {
        // This is a fix migration, so we don't want to remove columns in down()
    }
};