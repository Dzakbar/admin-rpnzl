<?php

namespace App\Http\Controllers\Admin\CMS;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get()->map(fn($p) => [
            'id'           => $p->id,
            'package_name' => $p->package_name,
            'description'  => $p->description,
            'price'        => $p->price,
            'image_url'    => $p->image_url,
            'status'       => $p->status,
        ]);

        return inertia('Admin/CMS/Packages', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'package_name' => 'required|string|max:100',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packages', config('filesystems.public_uploads_disk'));
        }

        Package::create($data);
        return back()->with('success', 'Paket berhasil ditambahkan.');
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'package_name' => 'required|string|max:100',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|max:2048',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image) Storage::disk(config('filesystems.public_uploads_disk'))->delete($package->image);
            $data['image'] = $request->file('image')->store('packages', config('filesystems.public_uploads_disk'));
        }

        $package->update($data);
        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if ($package->image) Storage::disk(config('filesystems.public_uploads_disk'))->delete($package->image);
        $package->delete();
        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
