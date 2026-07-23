<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $gallery = Gallery::orderBy('sort_order')->get()->map(fn($g) => [
            'id'        => $g->id,
            'image_url' => $g->image_url,
            'caption'   => $g->caption,
            'category'  => $g->category,
        ]);

        return inertia('Admin/CMS/Gallery', compact('gallery'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'    => 'required|image|max:3072',
            'caption'  => 'nullable|string|max:255',
            'category' => 'required|in:white,nude-semi-gold,maroon,pink-rose',
        ]);

        $path = $request->file('image')->store('gallery', config('filesystems.public_uploads_disk'));

        Gallery::create([
            'image_path' => $path,
            'caption'    => $request->caption,
            'category'   => $request->category,
            'sort_order' => Gallery::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Foto berhasil diupload.');
    }

    public function destroy(Gallery $gallery)
    {
        Storage::disk(config('filesystems.public_uploads_disk'))->delete($gallery->image_path);
        $gallery->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $index => $id) {
            Gallery::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}
