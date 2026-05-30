<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed admin login user.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@bagtopedia.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin12345'),
                'is_admin' => true,
                'role' => 'super_admin',
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin1@bagtopedia.local'],
            [
                'name' => 'Admin Operasional',
                'password' => Hash::make('admin12345'),
                'is_admin' => true,
                'role' => 'admin',
            ],
        );
    }
}
