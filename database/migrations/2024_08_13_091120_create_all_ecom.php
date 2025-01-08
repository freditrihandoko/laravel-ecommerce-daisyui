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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('sku')->unique()->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            // $table->integer('stock')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('product_image')->nullable();
            $table->enum('product_type', ['single', 'variant'])->default('single');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('weight', 10, 2)->nullable(); // Add weight column
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            // $table->integer('stock');
            $table->string('product_image')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('label')->nullable();
            $table->string('name')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan');
            $table->string('kota_kab');
            $table->string('provinsi');
            $table->string('kelurahan_id')->nullable();
            $table->string('kecamatan_id')->nullable();
            $table->string('kabupaten_id')->nullable();
            $table->string('provinsi_id')->nullable();
            $table->string('country')->default('Indonesia');
            $table->string('zip_code');
            $table->string('phone');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description');
            $table->string('discount_type');
            $table->decimal('discount_value', 10, 2);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->decimal('minimum_order_value', 10, 2)->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('cost', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('instructions')->nullable(); // Payment instructions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('address_id'); // Relasi ke tabel addresses
            $table->json('address'); // New JSON column to store address details
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2); // Subtotal sebelum diskon dan biaya pengiriman
            $table->decimal('discount_amount', 10, 2)->default(0); // Jumlah diskon
            $table->decimal('shipping_cost', 10, 2)->default(0); // Biaya pengiriman
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('shipping_method_id'); // Relasi ke shipping_methods
            $table->unsignedBigInteger('payment_method_id'); // Relasi ke payment_methods
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade'); // Relasi ke tabel addresses
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
            $table->foreign('status_id')->references('id')->on('order_statuses');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });

        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(); // Reference to product
            $table->unsignedBigInteger('variant_id')->nullable(); // Reference to product variant
            $table->integer('quantity'); // Quantity (positive for addition, negative for reduction)
            $table->string('action_type'); // Type of action: 'addition', 'reduction', 'purchase', 'restock', 'return', 'cancellation', etc.
            $table->unsignedBigInteger('order_id')->nullable(); // Reference to the related order if applicable
            $table->text('note')->nullable(); // Additional notes or comments
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });

        Schema::create('shipping_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('shipping_method');
            $table->decimal('shipping_cost', 10, 2);
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('payment_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('transaction_id')->nullable();
            $table->string('payment_status');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('rating');
            $table->text('review');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('button_text');
            $table->string('button_link');
            $table->string('background_image');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('website_name');
            $table->string('slogan')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('payment_information');
        Schema::dropIfExists('shipping_information');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
