<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'first_name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'admin123',
        ]);
    }
}
