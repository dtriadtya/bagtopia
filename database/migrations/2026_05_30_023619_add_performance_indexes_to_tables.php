<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Index ordered_at, fraud_status, platform, buyer_phone sudah ada.
        // Schema::table('orders', function (Blueprint $table) {});

        DB::statement('CREATE INDEX orders_shipping_address_idx ON orders (shipping_address(100))');

        Schema::table('products', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });

        DB::statement('DROP INDEX orders_shipping_address_idx ON orders');

        // Schema::table('orders', function (Blueprint $table) {
        // });
    }
};
