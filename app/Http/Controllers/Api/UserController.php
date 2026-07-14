<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function bookings(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return response()->json(['bookings' => []]);
        }

        $bookings = Booking::with(['user', 'package', 'schedule', 'invoice'])
            ->withExists('testimonial')
            ->whereHas('user', fn($q) => $q->where('email', $email))
            ->latest()
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'status' => $b->status,
                'package_id' => $b->package_id,
                'customer_name' => $b->customer_name ?? $b->user->name,
                'package_name' => $b->package->package_name,
                'has_testimonial' => (bool) $b->testimonial_exists,
                'booking_date' => $b->schedule->booking_date->format('Y-m-d'),
                'booking_time' => $b->schedule->timeLabel(),
                'event_type' => $b->event_type,
                'location' => $b->location,
                'notes' => $b->customization_notes,
                'created_at' => $b->created_at->toIso8601String(),
                'invoice' => $b->invoice ? [
                    'id' => $b->invoice->id,
                    'number' => $b->invoice->invoice_number,
                    'total' => $b->invoice->grand_total,
                    'pdf_url' => $b->invoice->pdf_url,
                ] : null,
            ]);

        return response()->json(['bookings' => $bookings]);
    }
}
