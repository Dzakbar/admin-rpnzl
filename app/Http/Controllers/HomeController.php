<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Package;
use App\Models\SiteContent;

class HomeController extends Controller
{
    public function index()
    {
        $packages = Package::where('status', 'active')->get();
        $gallery  = Gallery::orderBy('sort_order')->limit(8)->get();

        return inertia('Home', [
            'packages' => $packages->map(fn($p) => [
                'id'           => $p->id,
                'package_name' => $p->package_name,
                'description'  => $p->description,
                'price'        => $p->price,
                'image_url'    => $p->image_url,
            ]),
            'gallery' => $gallery->map(fn($g) => [
                'id'        => $g->id,
                'image_url' => $g->image_url,
                'caption'   => $g->caption,
                'category'  => $g->category,
            ]),
            'contents' => [
                'hero_title'    => SiteContent::get('hero_title'),
                'hero_subtitle' => SiteContent::get('hero_subtitle'),
                'about_text'    => SiteContent::get('about_text'),
            ],
        ]);
    }
}
