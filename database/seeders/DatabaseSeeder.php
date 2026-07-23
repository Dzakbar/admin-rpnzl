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
                'package_name' => 'White Henna',
                'description' => 'Detail putih yang lembut untuk tampilan bridal yang clean dan elegan.',
                'price' => 450000,
                'status' => 'active',
            ],
            [
                'package_name' => 'Nude Semi Gold Henna',
                'description' => 'Nuansa nude natural dengan aksen gold yang halus dan mewah.',
                'price' => 400000,
                'status' => 'active',
            ],
            [
                'package_name' => 'Henna Maroon',
                'description' => 'Warna maroon yang tegas untuk motif elegan dan klasik.',
                'price' => 380000,
                'status' => 'active',
            ],
            [
                'package_name' => 'Pink Rose Henna',
                'description' => 'Pink rose yang manis untuk tampilan henna yang lembut dan romantis.',
                'price' => 350000,
                'status' => 'active',
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['package_name' => $package['package_name']],
                $package
            );
        }

        Package::whereIn('package_name', [
            'Gold Henna',
            'Maroon Henna',
            'Nude Henna',
            'Bridal Henna',
            'Party Henna',
            'Event Henna',
        ])
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
