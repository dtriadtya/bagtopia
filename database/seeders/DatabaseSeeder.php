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

        $bagtopediaShopee = Store::query()->create([
            'name' => 'Bagtopedia Shopee',
            'platform' => 'shopee',
            'marketplace_store_id' => 'SHP-BAGTOPEDIA-01',
            'oauth_token' => 'seed-token-bagtopedia-shopee',
            'is_active' => true,
        ]);

        $bagtopediaTikTok = Store::query()->create([
            'name' => 'Bagtopedia TikTok Shop',
            'platform' => 'tiktok_shop',
            'marketplace_store_id' => 'TTS-BAGTOPEDIA-01',
            'oauth_token' => 'seed-token-bagtopedia-tiktok',
            'is_active' => true,
        ]);

        Order::query()->create([
            'store_id' => $bagtopediaShopee->id,
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
            'store_id' => $bagtopediaTikTok->id,
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
