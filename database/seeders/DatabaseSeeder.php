<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        $bagtopiaShopee = Store::query()->create([
            'name' => 'Bagtopia Shopee',
            'platform' => 'shopee',
            'marketplace_store_id' => 'SHP-BAGTOPIA-01',
            'oauth_token' => 'seed-token-bagtopia-shopee',
            'is_active' => true,
        ]);

        $bagtopíaTikTok = Store::query()->create([
            'name' => 'Bagtopia TikTok Shop',
            'platform' => 'tiktok_shop',
            'marketplace_store_id' => 'TTS-BAGTOPIA-01',
            'oauth_token' => 'seed-token-bagtopia-tiktok',
            'is_active' => true,
        ]);

        Order::query()->create([
            'store_id' => $bagtopiaShopee->id,
            'order_number' => 'ORD-SHP-0001',
            'buyer_name' => 'Andi Pratama',
            'buyer_phone' => '081111111111',
            'shipping_address' => 'Jl. Mawar No. 1, Bandung',
            'total_amount' => 450000,
            'platform' => 'shopee',
            'status' => 'paid',
            'fraud_score' => 0,
            'fraud_status' => 'valid',
            'ordered_at' => now()->subHours(5),
        ]);

        Order::query()->create([
            'store_id' => $bagtopíaTikTok->id,
            'order_number' => 'ORD-TTS-0001',
            'buyer_name' => 'Budi Santoso',
            'buyer_phone' => '081111111111',
            'shipping_address' => 'Jl. Melati No. 9, Jakarta',
            'total_amount' => 750000,
            'platform' => 'tiktok_shop',
            'status' => 'pending',
            'fraud_score' => 3,
            'fraud_status' => 'suspicious',
            'ordered_at' => now()->subHours(2),
        ]);
    }
}

