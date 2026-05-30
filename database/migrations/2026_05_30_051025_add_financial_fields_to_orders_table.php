<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('selling_price', 14, 2)->nullable()->after('total_amount');
            $table->decimal('net_revenue', 14, 2)->nullable()->after('selling_price');
            $table->decimal('cost_price', 14, 2)->nullable()->after('net_revenue');
            $table->decimal('marketplace_fee', 14, 2)->nullable()->after('cost_price');
            $table->decimal('gross_profit', 14, 2)->nullable()->after('marketplace_fee');
            $table->decimal('margin_percent', 5, 2)->nullable()->after('gross_profit');
            $table->string('payment_method', 80)->nullable()->after('margin_percent');
            $table->string('courier', 80)->nullable()->after('payment_method');
            $table->string('tracking_number', 80)->nullable()->after('courier');
            $table->string('brand', 120)->nullable()->after('tracking_number');
            $table->string('buyer_username', 120)->nullable()->after('brand');
            $table->string('marketplace_account', 120)->nullable()->after('buyer_username');
        });

        DB::table('orders')->update([
            'selling_price' => DB::raw('total_amount'),
            'net_revenue' => DB::raw('total_amount'),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'selling_price',
                'net_revenue',
                'cost_price',
                'marketplace_fee',
                'gross_profit',
                'margin_percent',
                'payment_method',
                'courier',
                'tracking_number',
                'brand',
                'buyer_username',
                'marketplace_account',
            ]);
        });
    }
};
