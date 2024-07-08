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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(\App\Enums\ProductType::ITEM->value);
            $table->string('sku', 32)->unique();
            $table->string('name');
            $table->string('unit', 12);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->boolean('disabled')->default(0);
            $table->boolean('published')->default(1);
            $table->foreignId('category_id')->nullable()->constrained()->on('product_categories')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->jsonb('option')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
