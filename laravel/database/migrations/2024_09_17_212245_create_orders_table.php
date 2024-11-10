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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('customer_name', 50);
            $table->string('customer_email', 50);
            $table->string('customer_phone', 20);
            $table->string('province_id', 10)->nullable();
            $table->string('district_id', 10)->nullable();
            $table->string('ward_id', 10)->nullable();
            $table->string('shipping_address', 255);
            $table->string('note', 255)->nullable();
            $table->foreignId('shipping_method_id')
                ->constrained('shipping_methods')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('order_status', 50)->index()->default('pending')->comment('pending', 'processing', 'delivering', 'completed', 'cancelled', 'returned');
            $table->string('payment_status', 50)->default('unpaid')->comment('unpaid, paid');
            $table->decimal('total_price', 15, 2);
            $table->decimal('shipping_fee', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('final_price', 15, 2);
            $table->timestamp('ordered_at')->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('additional_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
