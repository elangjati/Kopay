<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Owner
        User::updateOrCreate(
            ['email' => 'owner@kopay.com'],
            ['name' => 'Owner', 'password' => Hash::make('Kopay@Owner2026'), 'role' => 'owner']
        );

        // Kasir
        User::updateOrCreate(
            ['email' => 'kasir@kopay.com'],
            ['name' => 'Kasir', 'password' => Hash::make('Kopay@Kasir2026'), 'role' => 'kasir']
        );

        $menus = [
            ['name' => 'Espresso',     'category' => 'kopi',     'price' => 18000, 'description' => 'Kopi hitam pekat, shot tunggal'],
            ['name' => 'Americano',    'category' => 'kopi',     'price' => 22000, 'description' => 'Espresso dengan air panas'],
            ['name' => 'Cappuccino',   'category' => 'kopi',     'price' => 28000, 'description' => 'Espresso dengan susu dan foam'],
            ['name' => 'Latte',        'category' => 'kopi',     'price' => 30000, 'description' => 'Espresso dengan susu steamed'],
            ['name' => 'Matcha Latte', 'category' => 'non-kopi', 'price' => 32000, 'description' => 'Matcha premium dengan susu'],
            ['name' => 'Coklat Panas', 'category' => 'non-kopi', 'price' => 25000, 'description' => 'Coklat hangat creamy'],
            ['name' => 'Croissant',    'category' => 'makanan',  'price' => 20000, 'description' => 'Croissant butter renyah'],
            ['name' => 'Roti Bakar',   'category' => 'makanan',  'price' => 15000, 'description' => 'Roti bakar dengan selai'],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(['name' => $menu['name']], array_merge($menu, ['is_available' => true]));
        }
    }
}
