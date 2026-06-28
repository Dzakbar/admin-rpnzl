<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        User::updateOrCreate(
            ['email' => 'admin@rpnzlart.com'],
            [
                'name' => 'Admin RPNZL Art',
                'password' => Hash::make('password'),
                'whatsapp_number' => '6281234567890',
                'role' => 'admin',
            ]
        );

        $packages = [
            [
                'package_name' => 'Gold Henna',
                'description' => 'Detail putih lembut dengan aksen gold untuk look bridal yang glowing.',
                'price' => 450000,
                'status' => 'active',
            ],
            [
                'package_name' => 'Maroon Henna',
                'description' => 'Warna maroon yang lebih tegas untuk motif elegan dan klasik.',
                'price' => 380000,
                'status' => 'active',
            ],
            [
                'package_name' => 'Nude Henna',
                'description' => 'Nuansa nude yang halus untuk hasil clean, manis, dan modern.',
                'price' => 320000,
                'status' => 'active',
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['package_name' => $package['package_name']],
                $package
            );
        }

        Package::whereIn('package_name', ['Bridal Henna', 'Party Henna', 'Event Henna'])
            ->update(['status' => 'inactive']);

        $contents = [
            ['key' => 'hero_title', 'value' => 'Crafted with love, worn on skin', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Henna art untuk momen spesialmu', 'type' => 'text'],
            ['key' => 'about_text', 'value' => 'RPNZL Art adalah studio henna profesional untuk momen spesialmu.', 'type' => 'html'],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }
    }
}
