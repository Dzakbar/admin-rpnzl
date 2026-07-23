<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\SiteContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    public function show()
    {
        $categories = collect($this->categories());
        $contents = SiteContent::query()
            ->whereIn('key', ['hero_title', 'hero_subtitle', 'about_text'])
            ->pluck('value', 'key');

        $packages = Package::query()
            ->where('status', 'active')
            ->whereIn('package_name', $categories->pluck('name'))
            ->oldest()
            ->get()
            ->keyBy('package_name');

        $galleryItems = Gallery::query()
            ->whereIn('category', $categories->pluck('id'))
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Gallery $item) => [
                'id' => $item->id,
                'image_url' => $this->imageUrl($item->image_path),
                'caption' => $item->caption,
                'category' => $item->category,
                'category_slug' => $item->category,
                'category_name' => $categories->firstWhere('id', $item->category)['name'] ?? $item->category,
                'sort_order' => $item->sort_order,
            ]);

        $categoryPayload = $categories->map(function (array $category) use ($packages, $galleryItems) {
            $package = $packages->get($category['name']);
            $categoryGallery = $galleryItems->where('category', $category['id'])->values();

            return [
                'id' => $category['id'],
                'slug' => $category['slug'],
                'name' => $category['name'],
                'tone' => $category['tone'],
                'color' => $category['color'],
                'description' => $package?->description ?? $category['description'],
                'short_description' => $package
                    ? Str::limit($package->description, 120)
                    : $category['short_description'],
                'price' => $package ? (float) $package->price : null,
                'formatted_price' => $package
                    ? $this->formatRupiah($package->price)
                    : null,
                'package_id' => $package?->id,
                'image_url' => $this->imageUrl($package?->image),
                'gallery' => $categoryGallery,
                'images' => $categoryGallery->pluck('image_url')->filter()->values(),
            ];
        });

        return response()->json([
            'data' => [
                'contents' => [
                    'hero_title' => $contents->get('hero_title', ''),
                    'hero_subtitle' => $contents->get('hero_subtitle', ''),
                    'about_text' => $contents->get('about_text', ''),
                ],
                'categories' => $categoryPayload,
                'packages' => $categoryPayload,
                'gallery' => $galleryItems,
            ],
            'links' => [
                'schedule_availability' => url('/api/schedules/availability'),
                'schedule_slots' => url('/api/schedules/slots'),
                'schedule_unavailable' => url('/api/schedules/unavailable'),
                'booking_page' => url('/booking'),
            ],
        ]);
    }

    private function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        return Storage::disk(config('filesystems.public_uploads_disk'))->url($path);
    }

    private function formatRupiah(float|string $value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    private function categories(): array
    {
        return [
            [
                'id' => 'white',
                'slug' => 'white',
                'name' => 'White Henna',
                'tone' => 'Soft white',
                'color' => '#efe9e2',
                'short_description' => 'Detail putih yang lembut untuk tampilan bridal yang clean dan elegan.',
                'description' => 'Detail putih yang lembut untuk tampilan bridal yang clean dan elegan.',
            ],
            [
                'id' => 'nude-semi-gold',
                'slug' => 'nude-semi-gold',
                'name' => 'Nude Semi Gold Henna',
                'tone' => 'Warm glow',
                'color' => '#c7a169',
                'short_description' => 'Nuansa nude natural dengan aksen gold yang halus dan mewah.',
                'description' => 'Nuansa nude natural dengan aksen gold yang halus dan mewah.',
            ],
            [
                'id' => 'maroon',
                'slug' => 'maroon',
                'name' => 'Henna Maroon',
                'tone' => 'Deep romantic',
                'color' => '#7b2f3c',
                'short_description' => 'Warna maroon yang tegas untuk motif elegan dan klasik.',
                'description' => 'Warna maroon yang tegas untuk motif elegan dan klasik.',
            ],
            [
                'id' => 'pink-rose',
                'slug' => 'pink-rose',
                'name' => 'Pink Rose Henna',
                'tone' => 'Romantic rose',
                'color' => '#d8899d',
                'short_description' => 'Pink rose yang manis untuk tampilan henna yang lembut dan romantis.',
                'description' => 'Pink rose yang manis untuk tampilan henna yang lembut dan romantis.',
            ],
        ];
    }
}
