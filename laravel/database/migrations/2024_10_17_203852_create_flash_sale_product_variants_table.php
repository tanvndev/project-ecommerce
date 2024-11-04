<?php

use App\Models\FlashSale;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flash_sale_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FlashSale::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductVariant::class)->constrained()->cascadeOnDelete();
            $table->integer('max_quantity');
            $table->integer('sold_quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_product_variants');
    }
};
