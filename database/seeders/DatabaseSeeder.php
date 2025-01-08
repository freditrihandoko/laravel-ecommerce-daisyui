<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        \App\Models\User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        \App\Models\Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        \App\Models\Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        $electronicsCategory = \App\Models\Category::where('slug', 'electronics')->first();
        $clothingCategory = \App\Models\Category::where('slug', 'clothing')->first();

        \App\Models\Product::create([
            'name' => 'Smartphone',
            'slug' => 'smartphone',
            'description' => 'A high-quality smartphone with latest features.',
            'category_id' => $electronicsCategory->id,
            'price' => 4500000,
            // ... other fields
        ]);

        \App\Models\Product::create([
            'name' => 'Tshirt',
            'slug' => 'tshirt',
            'description' => 'A high-quality tshirt with latest features.',
            'category_id' => $clothingCategory->id,
            'price' => 150000,
            // ... other fields
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Credit Card',
            'instructions' => 'Enter your credit card details.',
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Bank BeCAk',
            'instructions' => 'Enter your payment details.',
        ]);

        \App\Models\PaymentMethod::create([
            'name' => 'Bank BRO',
            'instructions' => 'Enter your payment details.',
        ]);

        \App\Models\ShippingMethod::create([
            'name' => 'Standard Shipping',
            'cost' => 10000,
        ]);

        \App\Models\ShippingMethod::create([
            'name' => 'Express Shipping',
            'cost' => 14000,
        ]);

        \App\Models\OrderStatus::create(['name' => 'Pending']);
        \App\Models\OrderStatus::create(['name' => 'Approved']);
        \App\Models\OrderStatus::create(['name' => 'Canceled']);
        \App\Models\OrderStatus::create(['name' => 'Packing']);
        \App\Models\OrderStatus::create(['name' => 'Shipped']);
    }
}
