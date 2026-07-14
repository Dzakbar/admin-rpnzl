<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $testimonials = Testimonial::query()
            ->with(['booking', 'package'])
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->rating, fn ($query) => $query->where('rating', $request->rating))
            ->when($request->source, fn ($query) => $query->where('source', $request->source))
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('package_name', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->through(fn (Testimonial $testimonial) => [
                'id' => $testimonial->id,
                'booking_id' => $testimonial->booking_id,
                'customer_name' => $testimonial->customer_name,
                'customer_email' => $testimonial->customer_email,
                'customer_whatsapp' => $testimonial->customer_whatsapp,
                'rating' => $testimonial->rating,
                'message' => $testimonial->message,
                'package_name' => $testimonial->package_name,
                'source' => $testimonial->source,
                'status' => $testimonial->status,
                'is_featured' => $testimonial->is_featured,
                'created_at' => $testimonial->created_at->format('d M Y H:i'),
                'published_at' => $testimonial->published_at?->format('d M Y H:i'),
            ]);

        return inertia('Admin/Testimonials/Index', [
            'testimonials' => $testimonials,
            'filters' => $request->only('status', 'rating', 'source', 'search'),
        ]);
    }

    public function updateStatus(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,published,hidden',
        ]);

        $testimonial->update([
            'status' => $data['status'],
            'published_at' => $data['status'] === Testimonial::STATUS_PUBLISHED ? now() : null,
        ]);

        return back()->with('success', 'Status testimoni berhasil diperbarui.');
    }

    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_featured' => ! $testimonial->is_featured,
        ]);

        return back()->with('success', 'Pilihan featured testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
