<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $limit = min(max((int) $request->query('limit', 6), 1), 24);

        $testimonials = Testimonial::published()
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Testimonial $testimonial) => $this->serialize($testimonial));

        return response()->json([
            'data' => $testimonials,
            'meta' => [
                'average_rating' => round((float) Testimonial::published()->avg('rating'), 1),
                'total_published' => Testimonial::published()->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('booking_id')) {
            $booking = Booking::findOrFail($request->input('booking_id'));

            return $this->storeBooking($request, $booking);
        }

        return $this->storeHome($request);
    }

    public function storeHome(Request $request)
    {
        $data = $request->validate([
            'package_id' => 'nullable|exists:packages,id',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required_without:testimonial|nullable|string|min:10|max:700',
            'testimonial' => 'required_without:message|nullable|string|min:10|max:700',
            'customer.name' => 'required_without:name|nullable|string|max:120',
            'customer.email' => 'nullable|email|max:255',
            'customer.whatsapp_number' => 'nullable|string|max:30',
            'name' => 'required_without:customer.name|nullable|string|max:120',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
        ]);

        $package = ! empty($data['package_id'])
            ? Package::find($data['package_id'])
            : null;

        $customer = $this->customerFromPayload($data);

        $testimonial = Testimonial::create([
            'booking_id' => null,
            'package_id' => $package?->id,
            'package_name' => $package?->package_name,
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'] ?? null,
            'customer_whatsapp' => $customer['whatsapp_number'] ?? null,
            'rating' => $data['rating'],
            'message' => $data['message'] ?? $data['testimonial'],
            'source' => Testimonial::SOURCE_HOME,
            'status' => Testimonial::STATUS_PENDING,
        ]);

        return $this->createdResponse($testimonial);
    }

    public function storeBooking(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required_without:testimonial|nullable|string|min:10|max:700',
            'testimonial' => 'required_without:message|nullable|string|min:10|max:700',
            'customer.name' => 'nullable|string|max:120',
            'customer.email' => 'nullable|email|max:255',
            'customer.whatsapp_number' => 'nullable|string|max:30',
            'name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
        ]);

        if ($booking->testimonial()->exists()) {
            return response()->json([
                'message' => 'Booking ini sudah memiliki testimoni.',
            ], 422);
        }

        $booking->loadMissing(['user', 'package']);
        $customer = $this->customerFromPayload($data);

        $testimonial = Testimonial::create([
            'booking_id' => $booking->id,
            'package_id' => $booking->package_id,
            'package_name' => $booking->package?->package_name,
            'customer_name' => $customer['name'] ?? $booking->customer_name ?? $booking->user?->name,
            'customer_email' => $customer['email'] ?? $booking->user?->email,
            'customer_whatsapp' => $customer['whatsapp_number'] ?? $booking->user?->whatsapp_number,
            'rating' => $data['rating'],
            'message' => $data['message'] ?? $data['testimonial'],
            'source' => Testimonial::SOURCE_BOOKING,
            'status' => Testimonial::STATUS_PENDING,
        ]);

        return $this->createdResponse($testimonial);
    }

    private function customerFromPayload(array $data): array
    {
        $customer = $data['customer'] ?? [];

        return [
            'name' => $customer['name'] ?? $data['name'] ?? null,
            'email' => $customer['email'] ?? $data['email'] ?? null,
            'whatsapp_number' => $customer['whatsapp_number']
                ?? $data['whatsapp_number']
                ?? $data['whatsapp']
                ?? null,
        ];
    }

    private function createdResponse(Testimonial $testimonial)
    {
        return response()->json([
            'message' => 'Testimoni berhasil dikirim dan menunggu approval admin.',
            'testimonial' => $this->serialize($testimonial),
        ], 201);
    }

    private function serialize(Testimonial $testimonial): array
    {
        return [
            'id' => $testimonial->id,
            'customer_name' => $testimonial->customer_name,
            'rating' => $testimonial->rating,
            'message' => $testimonial->message,
            'package_name' => $testimonial->package_name,
            'source' => $testimonial->source,
            'created_at' => $testimonial->created_at?->toIso8601String(),
        ];
    }
}
