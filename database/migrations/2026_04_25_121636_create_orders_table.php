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
            $table->foreignId('reseller_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->string('buyer_name');
            $table->string('buyer_phone', 30)->nullable();
            $table->text('shipping_address');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->enum('platform', ['shopee', 'tiktok_shop']);
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->unsignedTinyInteger('fraud_score')->default(0);
            $table->enum('fraud_status', ['valid', 'suspicious', 'fraud'])->default('valid');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();

            $table->index(['reseller_id', 'platform', 'ordered_at']);
            $table->index(['fraud_status', 'ordered_at']);
            $table->index('buyer_phone');
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
