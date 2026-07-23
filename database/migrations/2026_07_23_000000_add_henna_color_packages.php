<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
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
            $existingPackage = DB::table('packages')
                ->where('package_name', $package['package_name'])
                ->first();

            if ($existingPackage) {
                DB::table('packages')
                    ->where('id', $existingPackage->id)
                    ->update([...$package, 'updated_at' => $now]);

                continue;
            }

            DB::table('packages')->insert([
                ...$package,
                'id' => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('packages')
            ->whereIn('package_name', ['Gold Henna', 'Maroon Henna', 'Nude Henna'])
            ->update(['status' => 'inactive', 'updated_at' => $now]);
    }

    public function down(): void
    {
        $now = now();

        DB::table('packages')
            ->whereIn('package_name', [
                'White Henna',
                'Nude Semi Gold Henna',
                'Henna Maroon',
                'Pink Rose Henna',
            ])
            ->update(['status' => 'inactive', 'updated_at' => $now]);

        DB::table('packages')
            ->whereIn('package_name', ['Gold Henna', 'Maroon Henna', 'Nude Henna'])
            ->update(['status' => 'active', 'updated_at' => $now]);
    }
};
