<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Get or create a sample category
        $category = Category::firstOrCreate(
            ['name' => 'Electronics'],
            ['description' => 'Electronic devices and gadgets', 'slug' => 'electronics']
        );

        // Create sample products
        $products = [
            [
                'name' => 'Laptop Gaming Pro',
                'slug' => Str::slug('Laptop Gaming Pro'),
                'description' => 'High performance gaming laptop with RGB keyboard.',
                'price' => 1200.00,
                'quantity' => 15,
                'category_id' => $category->id,
            ],
            [
                'name' => 'Wireless Bluetooth Headphones',
                'slug' => Str::slug('Wireless Bluetooth Headphones'),
                'description' => 'Noise-cancelling headphones with 20hr battery life.',
                'price' => 89.99,
                'quantity' => 30,
                'category_id' => $category->id,
            ],
            [
                'name' => 'Smartphone X',
                'slug' => Str::slug('Smartphone X'),
                'description' => 'Latest smartphone with AMOLED display and 5G.',
                'price' => 799.99,
                'quantity' => 25,
                'category_id' => $category->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}